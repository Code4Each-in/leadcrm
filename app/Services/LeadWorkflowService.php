<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadAssignment;
use App\Models\LeadPricing;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use App\Notifications\LeadWorkflowNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Single write path for the MIS -> Account Manager workflow:
 *
 *   Open -> With Account Manager --> Hold / Lost / Closed
 *
 * and, for a product with a Pricing section, the pricing review that
 * runs while the lead is with its Account Manager:
 *
 *   published -> awaiting approval --> approved
 *                      ^         |
 *                      |         v
 *     MIS publishes new pricing  declined
 *
 * MIS assigns the lead to an Account Manager - with or without
 * pricing; they can start working it straight away - and once MIS
 * publishes the pricing the Account Manager approves or declines it
 * (pricingPublished() tells them it has arrived). The lead stays
 * with the Account Manager throughout: a decline only marks the pricing, and
 * as soon as MIS publishes new pricing it is back in front of the
 * same Account Manager (pricingPublished()) - no reassignment - until
 * it is approved.
 *
 * Every transition runs in one transaction under a row lock on the
 * lead (so two people acting on it at once are applied one after
 * the other), validates the current state, updates the lead, writes
 * a lead_assignments history row and a lead_logs entry, and only
 * after the commit sends notifications - which can never fail the
 * transition itself.
 *
 * Who is *allowed* to trigger a transition is LeadPolicy's job; this
 * class only enforces that the lead is in a state where it makes
 * sense.
 */
class LeadWorkflowService
{
    // ------------------------------------------------------------
    // Who a lead can go to
    // ------------------------------------------------------------

    /**
     * Active Account Managers who have access to the lead's product
     * (users.product_id) - the only users a lead can be assigned to.
     * Product access is compared in PHP rather than with a JSON query
     * since the user form stores ids as strings.
     */
    public function assignableAccountManagers(Lead $lead): Collection
    {
        return User::where('role_id', config('roles.manager'))
            ->where('status', 1)
            ->orderBy('name')
            ->get()
            ->filter(fn (User $am) => $am->hasProductAccess($lead->product_id))
            ->values();
    }

    // ------------------------------------------------------------
    // Transitions - each returns the fresh Lead (+ whatever the
    // caller needs for its response)
    // ------------------------------------------------------------

    /**
     * MIS / Admin / Super Admin assign a lead to an Account Manager,
     * or reassign it to another one, whatever its pricing stage. With
     * no published pricing yet the Account Manager can start working
     * the lead anyway, and is told when pricing arrives (see
     * pricingPublished()); a reassigned lead's new Account Manager
     * simply picks the review up where it is.
     *
     * $note is written to the assignment history (e.g. why an
     * automatic assignment happened); the manual Assign button passes
     * none.
     *
     * @return array{0: Lead, 1: User, 2: bool} lead, Account Manager, whether anything changed
     */
    public function assignToAccountManager(Lead $lead, int $accountManagerId, User $actor, ?string $note = null): array
    {
        $notify = null;

        [$lead, $am, $changed] = $this->transaction($lead, function (Lead $lead) use ($accountManagerId, $actor, $note, &$notify) {

            if (!$lead->isPublishedOrBeyond()) {
                throw ValidationException::withMessages([
                    'account_manager_id' => 'Only an Open lead can be assigned. Publish this lead first.',
                ]);
            }

            if ($lead->isFinished()) {
                throw ValidationException::withMessages([
                    'account_manager_id' => 'A lost or closed lead cannot be reassigned.',
                ]);
            }

            $am = $this->assignableAccountManagers($lead)->firstWhere('id', $accountManagerId);

            if (!$am) {
                throw ValidationException::withMessages([
                    'account_manager_id' => 'Please select an active Account Manager with access to this lead\'s product.',
                ]);
            }

            $previousOwner = $lead->assignee;

            // Same Account Manager picked again while they already
            // hold it - nothing to change, and no duplicate
            // notification/email.
            if ($lead->isWithAccountManager() && (int) $lead->assigned_to === $am->id) {
                return [$lead, $am, false];
            }

            $fromStatus = $lead->status;
            $reassigned = $lead->account_manager_id !== null || $previousOwner !== null;

            // Whoever was working the lead (an Account Manager, or the
            // AE on an old-workflow lead) - not the MIS user a
            // declined lead went back to.
            $previousWorker = ($lead->isWithAccountManager() || $lead->isWithAe()) ? $previousOwner : null;

            $lead->update([
                'assigned_to' => $am->id,
                'assigned_by' => $actor->id,
                'account_manager_id' => $am->id,
                // Whoever the lead was imported for, it has now really
                // been assigned - by hand or automatically.
                'intended_account_manager_id' => null,
                'am_assigned_at' => now(),
                'status' => Lead::STATUS_WITH_ACCOUNT_MANAGER,
            ]);

            LeadLogger::leadAssigned($lead, $am, $previousOwner);

            $this->record(
                $lead,
                $reassigned ? LeadAssignment::ACTION_REASSIGNED : LeadAssignment::ACTION_ASSIGNED,
                $actor,
                // A first assignment comes from the assigner (the "MIS"
                // side); a reassignment from whoever held it.
                $previousOwner ?? $actor,
                $am,
                $fromStatus,
                $lead->status,
                $note
            );

            $notify = function () use ($am, $lead, $actor, $reassigned, $previousWorker) {
                $am->notify(new LeadAssignedNotification($lead, $actor, $reassigned));

                // Whoever just lost the lead is told (in-app) - unless
                // they did it themselves or it went to the same person.
                if ($previousWorker && $previousWorker->id !== $actor->id && $previousWorker->id !== $am->id && !$previousWorker->trashed()) {
                    $previousWorker->notify(new LeadWorkflowNotification(
                        $lead, $actor, LeadWorkflowNotification::EVENT_REASSIGNED, null, $am
                    ));
                }
            };

            return [$lead, $am, true];
        });

        $this->dispatch($notify);

        return [$lead, $am, $changed];
    }

    /**
     * History note on an assignment made by
     * assignIntendedAccountManager().
     */
    public const AUTO_ASSIGN_NOTE = 'Auto-assigned to the Account Manager specified on import.';

    /**
     * An imported lead remembers the Account Manager named in its
     * "User Name" column (intended_account_manager_id). This assigns
     * it to them as soon as the lead is assignable under the normal
     * rules - published, and not already with an Account Manager or
     * finished. Pricing is not needed: the Account Manager is told
     * when it is published later (see pricingPublished()).
     *
     * Called whenever those conditions may have just become true: on
     * import (a published row), when the lead is published, and when
     * its pricing is published (a no-op by then unless the lead was
     * held back for some other reason). $actor - whoever
     * did that - is recorded as assigned_by exactly like a manual
     * assignment, so the existing assignment / pricing-decision /
     * status notifications all go where they normally would. The
     * assignment itself goes through assignToAccountManager(), so
     * every one of its rules still applies; nothing is bypassed.
     *
     * A lead with no intended Account Manager (every manually created
     * lead) is left alone. If the intended one no longer qualifies,
     * the lead stays Open for manual assignment and that is logged.
     */
    public function assignIntendedAccountManager(Lead $lead, ?User $actor): ?Lead
    {
        $lead = $lead->fresh();

        if (!$lead || !$lead->intended_account_manager_id || !$actor) {
            return null;
        }

        if (!$lead->isPublishedOrBeyond()
            || $lead->isWithAccountManager()
            || $lead->isWithAe()
            || $lead->isFinished()) {
            return null;
        }

        $am = $this->assignableAccountManagers($lead)->firstWhere('id', (int) $lead->intended_account_manager_id);

        if (!$am) {
            $intended = $lead->intendedAccountManager;

            $lead->updateQuietly(['intended_account_manager_id' => null]);

            LeadLogger::intendedAccountManagerDropped($lead, $intended);

            return null;
        }

        [$lead] = $this->assignToAccountManager($lead, $am->id, $actor, self::AUTO_ASSIGN_NOTE);

        return $lead;
    }

    /**
     * A lead has just been published (from draft) - for a Multiple
     * Site batch, every one of its sites. See
     * assignIntendedAccountManager().
     */
    public function leadPublished(Lead $lead, ?User $actor): void
    {
        $leads = $lead->isMultisite() ? $lead->siblingSites()->get() : collect([$lead]);

        foreach ($leads as $site) {
            $this->assignIntendedAccountManager($site, $actor);
        }
    }

    /**
     * The Account Manager approves or declines the lead's current
     * (published) pricing. The lead stays with them either way - they
     * carry on working it (Hold / Lost / Close). A decline needs a
     * reason; MIS then publishes new pricing, which comes straight
     * back to this Account Manager (see pricingPublished()). The
     * decision is stamped on the pricing record, written to Notes &
     * Documents and the Assignment History, and the assigner is told.
     */
    public function reviewPricing(Lead $lead, User $actor, bool $approve, ?string $note = null): Lead
    {
        if (!$approve && !filled($note)) {
            throw ValidationException::withMessages(['note' => 'Please enter the reason you are declining this pricing.']);
        }

        $notify = null;

        $lead = $this->transaction($lead, function (Lead $lead) use ($actor, $approve, $note, &$notify) {

            if ($lead->pricingStage() !== Lead::PRICING_STAGE_AWAITING_APPROVAL) {
                throw ValidationException::withMessages([
                    'pricing' => 'There is no pricing waiting for your approval on this lead.',
                ]);
            }

            $pricing = $lead->currentPricing;
            $assigner = $lead->assigner;

            // Quietly - the pricing observer would log this as a plain
            // edit; LeadLogger::pricingReviewed() logs it properly.
            $pricing->updateQuietly([
                'status' => $approve ? LeadPricing::STATUS_APPROVED : LeadPricing::STATUS_DECLINED,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
            ]);

            LeadLogger::pricingReviewed($lead, $pricing, $actor, $approve, $note);

            // The lead doesn't move - it is from and to the Account
            // Manager, and its status is unchanged.
            $this->record(
                $lead,
                $approve ? LeadAssignment::ACTION_PRICING_APPROVED : LeadAssignment::ACTION_PRICING_DECLINED,
                $actor,
                $actor,
                $actor,
                $lead->status,
                $lead->status,
                $note
            );

            $this->recordNote(
                $lead,
                $actor,
                $approve ? LeadAssignment::ACTION_PRICING_APPROVED : LeadAssignment::ACTION_PRICING_DECLINED,
                $approve ? 'Pricing Approved' : 'Pricing Declined',
                $note,
                always: true
            );

            if ($assigner && !$assigner->trashed() && $assigner->id !== $actor->id) {
                $event = $approve ? LeadWorkflowNotification::EVENT_PRICING_APPROVED : LeadWorkflowNotification::EVENT_PRICING_DECLINED;

                $notify = fn () => $assigner->notify(new LeadWorkflowNotification($lead, $actor, $event, $note));
            }

            return $lead;
        });

        $this->dispatch($notify);

        return $lead;
    }

    /**
     * Called whenever pricing is published (Add Pricing, a draft
     * published, or a CSV import). If the lead is already with an
     * Account Manager, the new pricing goes straight to them for
     * review - MIS doesn't assign again - and they are told: "pricing
     * available" for the lead's first pricing (it was assigned before
     * there was any), "pricing updated" for pricing published after
     * an earlier one (e.g. after a decline). A lead not yet with an
     * Account Manager is simply left for assignment.
     */
    public function pricingPublished(LeadPricing $pricing, ?User $actor): void
    {
        $notify = null;

        $this->transaction($pricing->lead, function (Lead $lead) use ($pricing, $actor, &$notify) {

            if (!$lead->requiresPricing()
                || (int) $lead->currentPricing?->id !== $pricing->id
                || $lead->pricingStage() !== Lead::PRICING_STAGE_AWAITING_APPROVAL) {
                return;
            }

            $am = $lead->assignee;

            if (!$am) {
                return;
            }

            // Any earlier pricing that got as far as the Account Manager
            // (published / approved / declined) makes this a resubmission.
            $isFirst = !$lead->pricings()
                ->whereKeyNot($pricing->id)
                ->where('status', '!=', LeadPricing::STATUS_DRAFT)
                ->exists();

            $isFirst
                ? LeadLogger::pricingPublishedToAccountManager($lead, $pricing, $actor, $am)
                : LeadLogger::pricingResubmitted($lead, $pricing, $actor, $am);

            $this->record(
                $lead,
                $isFirst ? LeadAssignment::ACTION_PRICING_PUBLISHED : LeadAssignment::ACTION_PRICING_RESUBMITTED,
                $actor ?? $am,
                $actor,
                $am,
                $lead->status,
                $lead->status
            );

            if (!$am->trashed() && $am->id !== $actor?->id) {
                $event = $isFirst
                    ? LeadWorkflowNotification::EVENT_PRICING_PUBLISHED
                    : LeadWorkflowNotification::EVENT_PRICING_RESUBMITTED;

                $notify = fn () => $am->notify(new LeadWorkflowNotification($lead, $actor ?? $am, $event));
            }
        });

        $this->dispatch($notify);

        // Not yet with an Account Manager - if it was imported for
        // one and is still waiting, assign it now.
        $this->assignIntendedAccountManager($pricing->lead, $actor);
    }

    /**
     * What each of the Account Manager's status choices does. `from`
     * is where it may be applied; the Account Manager stays the
     * lead's owner in every case (assigned_to is never touched).
     * Hold pauses the lead; Lost and Closed end it.
     */
    private const AM_STATUS_ACTIONS = [
        Lead::STATUS_HOLD => [
            'from' => [Lead::STATUS_WITH_ACCOUNT_MANAGER],
            'action' => LeadAssignment::ACTION_HOLD,
            'note_label' => 'Put on Hold',
            'event' => LeadWorkflowNotification::EVENT_HOLD,
            'stamp' => ['hold_at', 'hold_by'],
            'message' => 'Only a lead under review can be put on hold.',
        ],
        Lead::STATUS_LOST => [
            'from' => [Lead::STATUS_WITH_ACCOUNT_MANAGER, Lead::STATUS_HOLD],
            'action' => LeadAssignment::ACTION_LOST,
            'note_label' => 'Mark as Lost',
            'event' => LeadWorkflowNotification::EVENT_LOST,
            'stamp' => ['lost_at', 'lost_by'],
            'message' => 'Only a lead that is with an Account Manager can be marked as lost.',
        ],
        Lead::STATUS_CLOSED => [
            'from' => [Lead::STATUS_WITH_ACCOUNT_MANAGER, Lead::STATUS_HOLD],
            'action' => LeadAssignment::ACTION_CLOSED,
            'note_label' => 'Close Lead',
            'event' => LeadWorkflowNotification::EVENT_CLOSED,
            'stamp' => ['closed_at', 'closed_by'],
            'message' => 'Only a lead that is with an Account Manager can be closed.',
        ],
    ];

    /**
     * The Account Manager's single "Update Lead Status" control:
     * Hold, Lost or Closed (pass the new stored status). The Account
     * Manager remains the lead's current / last owner; whoever
     * assigned the lead is told (in-app always, by email for Lost /
     * Closed - see LeadWorkflowNotification::MAIL_EVENTS).
     * A reason is required for Lost.
     */
    public function setAccountManagerStatus(Lead $lead, User $actor, string $status, ?string $note = null): Lead
    {
        $config = self::AM_STATUS_ACTIONS[$status] ?? null;

        if (!$config) {
            throw ValidationException::withMessages(['status' => 'Please choose Hold, Lost or Close.']);
        }

        if ($status === Lead::STATUS_LOST && !filled($note)) {
            throw ValidationException::withMessages(['note' => 'Please enter the reason this lead was lost.']);
        }

        $notify = null;

        $lead = $this->transaction($lead, function (Lead $lead) use ($actor, $status, $note, $config, &$notify) {

            $this->assertStatus($lead, $config['from'], $config['message'], 'status');

            $fromStatus = $lead->status;

            [$atColumn, $byColumn] = $config['stamp'];

            $lead->update([
                'status' => $status,
                $atColumn => now(),
                $byColumn => $actor->id,
            ]);

            LeadLogger::leadStatusSetByAccountManager($lead, $actor, $status, $fromStatus, $note);

            // The Account Manager stays with the lead, so it is from and to them.
            $this->record($lead, $config['action'], $actor, $actor, $actor, $fromStatus, $lead->status, $note);
            $this->recordNote($lead, $actor, $status, $config['note_label'], $note);

            $assigner = $lead->assigner;

            if ($assigner && !$assigner->trashed() && $assigner->id !== $actor->id) {
                $notify = fn () => $assigner->notify(new LeadWorkflowNotification($lead, $actor, $config['event'], $note));
            }

            return $lead;
        });

        $this->dispatch($notify);

        return $lead;
    }

    // ------------------------------------------------------------
    // Internals
    // ------------------------------------------------------------

    /**
     * Runs $callback in a transaction against a freshly locked copy
     * of the lead, so the state it validates is the state it changes.
     */
    private function transaction(Lead $lead, callable $callback): mixed
    {
        return DB::transaction(function () use ($lead, $callback) {
            $locked = Lead::whereKey($lead->id)->lockForUpdate()->firstOrFail();

            return $callback($locked);
        });
    }

    private function assertStatus(Lead $lead, array $allowed, string $message, string $field = 'lead'): void
    {
        if (!in_array($lead->status, $allowed, true)) {
            throw ValidationException::withMessages([$field => $message]);
        }
    }

    /**
     * Appends a lead_assignments history row. $from / $to are the
     * users the lead moves between (null when there isn't one, e.g.
     * a close); names and roles are snapshotted.
     */
    private function record(
        Lead $lead,
        string $action,
        User $actor,
        ?User $from,
        ?User $to,
        ?string $fromStatus,
        ?string $toStatus,
        ?string $note = null
    ): LeadAssignment {
        return LeadAssignment::create([
            'lead_id' => $lead->id,
            'action' => $action,
            'performed_by' => $actor->id,
            'performed_by_name' => $actor->name,
            'performed_by_role' => $actor->role?->name,
            'from_user_id' => $from?->id,
            'from_user_name' => $from?->name,
            'from_role' => $from?->role?->name,
            'to_user_id' => $to?->id,
            'to_user_name' => $to?->name,
            'to_role' => $to?->role?->name,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => filled($note) ? trim($note) : null,
        ]);
    }

    /**
     * Copies the action note into the lead's Notes & Documents, as a
     * permanent workflow note (see LeadActivity::isWorkflowNote()).
     * Stored as ready-to-show HTML - the same shape the note editor
     * produces - so the feed needs no special rendering beyond a
     * badge, and the wording stays as it was even if the user is
     * later renamed. Unless $always is set (pricing decisions are
     * always recorded), nothing is written when no note was entered
     * (the history and log still record the action). Deliberately not
     * routed through LeadLogger::activityCreated(): the workflow
     * already logged this action, with its note.
     */
    private function recordNote(Lead $lead, User $actor, string $action, string $actionLabel, ?string $note, bool $always = false): void
    {
        if (!filled($note) && !$always) {
            return;
        }

        $role = $actor->role?->name;
        $who = e($actor->name) . ($role ? ' (' . e($role) . ')' : '');

        LeadActivity::create([
            'lead_id' => $lead->id,
            'created_by' => $actor->id,
            'workflow_action' => $action,
            'content' => '<p><strong>Action:</strong> ' . e($actionLabel) . '</p>'
                . '<p><strong>Performed By:</strong> ' . $who . '</p>'
                . '<p><strong>Date &amp; Time:</strong> ' . now()->format('d M Y, h:i A') . '</p>'
                . (filled($note) ? '<p><strong>Note:</strong> ' . nl2br(e(trim($note))) . '</p>' : ''),
        ]);
    }

    /**
     * After the commit - the outermost one, when a caller such as the
     * pricing CSV import wraps several steps in its own transaction -
     * and never allowed to fail the transition: the in-app
     * notification is stored before any email is attempted, so a mail
     * outage only costs the email.
     */
    private function dispatch(?callable $notify): void
    {
        if (!$notify) {
            return;
        }

        DB::afterCommit(function () use ($notify) {
            try {
                $notify();
            } catch (Throwable $e) {
                report($e);
            }
        });
    }
}
