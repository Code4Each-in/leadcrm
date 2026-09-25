<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadAssignment;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use App\Notifications\LeadWorkflowNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Single write path for the MIS -> AE -> Account Manager workflow:
 *
 *   Open -> Assigned -> In Progress -> With Account Manager --> Hold / Lost / Closed
 *                 ^                          |
 *                 +------ Sent Back to AE <--+
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
     * Active Account Executives who have access to the lead's
     * product (users.product_id) - the only users a lead can be
     * assigned to. Product access is compared in PHP rather than
     * with a JSON query since the user form stores ids as strings.
     */
    public function assignableAes(Lead $lead): Collection
    {
        return User::where('role_id', config('roles.ae'))
            ->where('status', 1)
            ->orderBy('name')
            ->get()
            ->filter(fn (User $ae) => $ae->hasProductAccess($lead->product_id))
            ->values();
    }

    /**
     * Every active Account Manager - the pool an AE picks from when
     * moving a lead forward.
     */
    public function assignableAccountManagers(): Collection
    {
        return User::where('role_id', config('roles.manager'))
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    // ------------------------------------------------------------
    // Transitions - each returns the fresh Lead (+ whatever the
    // caller needs for its response)
    // ------------------------------------------------------------

    /**
     * MIS / Admin / Super Admin assign an Open lead to an AE, or
     * reassign one that is already in the workflow.
     *
     * @return array{0: Lead, 1: User, 2: bool} lead, AE, whether anything changed
     */
    public function assignToAe(Lead $lead, int $aeId, User $actor): array
    {
        $notify = null;

        [$lead, $ae, $changed] = $this->transaction($lead, function (Lead $lead) use ($aeId, $actor, &$notify) {

            if (!$lead->isPublishedOrBeyond()) {
                throw ValidationException::withMessages([
                    'ae_id' => 'Only an Open lead can be assigned. Publish this lead first.',
                ]);
            }

            if ($lead->isFinished()) {
                throw ValidationException::withMessages([
                    'ae_id' => 'A lost or closed lead cannot be reassigned.',
                ]);
            }

            $ae = $this->assignableAes($lead)->firstWhere('id', $aeId);

            if (!$ae) {
                throw ValidationException::withMessages([
                    'ae_id' => 'Please select an active Account Executive with access to this lead\'s product.',
                ]);
            }

            $previousOwner = $lead->assignee;

            // Same AE picked again while they already hold it -
            // nothing to change, and no duplicate notification/email.
            if ($lead->isWithAe() && (int) $lead->assigned_to === $ae->id) {
                return [$lead, $ae, false];
            }

            $fromStatus = $lead->status;

            $lead->update([
                'assigned_to' => $ae->id,
                'assigned_by' => $actor->id,
                'account_executive_id' => $ae->id,
                'ae_assigned_at' => now(),
                'status' => Lead::STATUS_ASSIGNED,
                // The new AE hasn't started yet.
                'process_started_at' => null,
                'process_started_by' => null,
            ]);

            LeadLogger::leadAssigned($lead, $ae, $previousOwner);

            $this->record(
                $lead,
                $previousOwner ? LeadAssignment::ACTION_REASSIGNED : LeadAssignment::ACTION_ASSIGNED,
                $actor,
                // A first assignment comes from the assigner (the "MIS"
                // side); a reassignment from whoever held it.
                $previousOwner ?? $actor,
                $ae,
                $fromStatus,
                $lead->status
            );

            $reassigned = $previousOwner !== null;

            $notify = function () use ($ae, $lead, $actor, $reassigned, $previousOwner) {
                $ae->notify(new LeadAssignedNotification($lead, $actor, $reassigned));

                // Whoever just lost the lead is told (in-app) - unless
                // they did it themselves or it went to the same person.
                if ($previousOwner && $previousOwner->id !== $actor->id && $previousOwner->id !== $ae->id && !$previousOwner->trashed()) {
                    $previousOwner->notify(new LeadWorkflowNotification(
                        $lead, $actor, LeadWorkflowNotification::EVENT_REASSIGNED, null, $ae
                    ));
                }
            };

            return [$lead, $ae, true];
        });

        $this->dispatch($notify);

        return [$lead, $ae, $changed];
    }

    /**
     * The AE starts working the lead: Assigned -> In Progress.
     */
    public function startProcess(Lead $lead, User $actor): Lead
    {
        $notify = null;

        $lead = $this->transaction($lead, function (Lead $lead) use ($actor, &$notify) {

            $this->assertStatus($lead, [Lead::STATUS_ASSIGNED], 'Only a lead that is waiting to be started can be processed.');

            $fromStatus = $lead->status;

            $lead->update([
                'status' => Lead::STATUS_IN_PROGRESS,
                'process_started_at' => now(),
                'process_started_by' => $actor->id,
            ]);

            LeadLogger::leadProcessStarted($lead);

            $this->record($lead, LeadAssignment::ACTION_PROCESS_STARTED, $actor, $actor, $actor, $fromStatus, $lead->status);

            // FYI to whoever assigned it (not to the AE themselves).
            $assigner = $lead->assigner;
            if ($assigner && $assigner->id !== $actor->id) {
                $notify = fn () => $assigner->notify(
                    new LeadWorkflowNotification($lead, $actor, LeadWorkflowNotification::EVENT_PROCESS_STARTED)
                );
            }

            return $lead;
        });

        $this->dispatch($notify);

        return $lead;
    }

    /**
     * The AE hands the lead to an Account Manager they pick:
     * In Progress / Sent Back -> With Account Manager.
     *
     * @return array{0: Lead, 1: User} lead, Account Manager
     */
    public function moveToAccountManager(Lead $lead, int $accountManagerId, User $actor): array
    {
        $notify = null;

        [$lead, $am] = $this->transaction($lead, function (Lead $lead) use ($accountManagerId, $actor, &$notify) {

            $this->assertStatus(
                $lead,
                [Lead::STATUS_IN_PROGRESS, Lead::STATUS_SENT_BACK],
                'Start the process on this lead before moving it to an Account Manager.',
                'account_manager_id'
            );

            $am = $this->assignableAccountManagers()->firstWhere('id', $accountManagerId);

            if (!$am) {
                throw ValidationException::withMessages([
                    'account_manager_id' => 'Please select an active Account Manager.',
                ]);
            }

            $fromStatus = $lead->status;

            $lead->update([
                'assigned_to' => $am->id,
                'account_manager_id' => $am->id,
                'am_assigned_at' => now(),
                'status' => Lead::STATUS_WITH_ACCOUNT_MANAGER,
            ]);

            LeadLogger::leadMovedToAccountManager($lead, $actor, $am);

            $this->record($lead, LeadAssignment::ACTION_MOVED_TO_AM, $actor, $actor, $am, $fromStatus, $lead->status);

            $notify = fn () => $am->notify(
                new LeadWorkflowNotification($lead, $actor, LeadWorkflowNotification::EVENT_FORWARDED)
            );

            return [$lead, $am];
        });

        $this->dispatch($notify);

        return [$lead, $am];
    }

    /**
     * The Account Manager returns the lead to the AE who forwarded
     * it: With Account Manager -> Sent Back to AE.
     *
     * @return array{0: Lead, 1: User} lead, AE
     */
    public function sendBackToAe(Lead $lead, User $actor, ?string $note = null): array
    {
        $notify = null;

        [$lead, $ae] = $this->transaction($lead, function (Lead $lead) use ($actor, $note, &$notify) {

            $this->assertStatus($lead, [Lead::STATUS_WITH_ACCOUNT_MANAGER, Lead::STATUS_HOLD], 'Only a lead that is with an Account Manager can be sent back.');

            $ae = $lead->accountExecutive;

            if (!$ae || $ae->trashed() || (int) $ae->status !== 1) {
                throw ValidationException::withMessages([
                    'lead' => 'The Account Executive who worked on this lead is no longer active. Ask MIS to reassign it.',
                ]);
            }

            $fromStatus = $lead->status;

            $lead->update([
                'assigned_to' => $ae->id,
                'status' => Lead::STATUS_SENT_BACK,
            ]);

            LeadLogger::leadSentBack($lead, $actor, $ae, $note);

            $this->record($lead, LeadAssignment::ACTION_SENT_BACK, $actor, $actor, $ae, $fromStatus, $lead->status, $note);
            $this->recordNote($lead, $actor, 'sent_back', 'Send Back to AE', $note);

            $notify = fn () => $ae->notify(
                new LeadWorkflowNotification($lead, $actor, LeadWorkflowNotification::EVENT_SENT_BACK, $note)
            );

            return [$lead, $ae];
        });

        $this->dispatch($notify);

        return [$lead, $ae];
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
     * Manager remains the lead's current / last owner; the AE and
     * whoever assigned the lead are told (in-app always, by email
     * for Lost / Closed - see LeadWorkflowNotification::MAIL_EVENTS).
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

            $recipients = collect([$lead->accountExecutive, $lead->assigner])
                ->filter(fn (?User $user) => $user && !$user->trashed() && $user->id !== $actor->id)
                ->unique('id');

            $notify = function () use ($recipients, $lead, $actor, $note, $config) {
                foreach ($recipients as $recipient) {
                    $recipient->notify(new LeadWorkflowNotification($lead, $actor, $config['event'], $note));
                }
            };

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
     * later renamed. Nothing is written when no note was entered
     * (the history and log still record the action). Deliberately not
     * routed through LeadLogger::activityCreated(): the workflow
     * already logged this action, with its note.
     */
    private function recordNote(Lead $lead, User $actor, string $action, string $actionLabel, ?string $note): void
    {
        if (!filled($note)) {
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
                . '<p><strong>Note:</strong> ' . nl2br(e(trim($note))) . '</p>',
        ]);
    }

    /**
     * After the commit, and never allowed to fail the transition -
     * the in-app notification is stored before any email is
     * attempted, so a mail outage only costs the email.
     */
    private function dispatch(?callable $notify): void
    {
        if (!$notify) {
            return;
        }

        try {
            $notify();
        } catch (Throwable $e) {
            report($e);
        }
    }
}
