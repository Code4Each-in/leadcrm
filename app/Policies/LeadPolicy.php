<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

/**
 * Single source of truth for who can see/change a given lead.
 * Auto-discovered by Laravel (class name matches the {Model}Policy
 * convention), no explicit registration needed.
 */
class LeadPolicy
{
    /**
     * Open (published) and Assigned leads are visible to MIS User,
     * Admin, Super Admin, and the lead's creator - plus, once a lead
     * is assigned, the Account Executive it's assigned to. Draft
     * leads keep the original rule: creator or Admin/Super Admin
     * only - MIS User gets no special access to other people's
     * drafts.
     */
    public function view(User $user, Lead $lead): bool
    {
        if ($user->isAdminOrAbove()) {
            return true;
        }

        if ($lead->created_by === $user->id) {
            return true;
        }

        // The current owner, plus the AE / Account Manager who have
        // worked on it - a lead handed onwards stays visible (read
        // only) to whoever handed it on.
        if ($lead->isTeamMember($user)) {
            return true;
        }

        return $user->isMis() && $lead->isPublishedOrBeyond();
    }

    /**
     * Account Executives can never edit a lead once it has left
     * draft (Open or Assigned), even one they created themselves or
     * are assigned to - publishing is a one-way handoff for that
     * role. Everyone else who can view a lead can also update it
     * (this is also what the inline status toggle on the show page
     * uses, so an AE can't route around the edit block by
     * un-publishing first).
     */
    public function update(User $user, Lead $lead): bool
    {
        if ($lead->isPublishedOrBeyond() && $user->isAe()) {
            return false;
        }

        // An Account Manager works a lead through the workflow
        // actions (send back / close), not by editing it - unless
        // it's a lead they created themselves.
        if ($lead->isPublishedOrBeyond() && $user->isManager() && $lead->created_by !== $user->id) {
            return false;
        }

        return $this->view($user, $lead);
    }

    /**
     * Only MIS User, Admin and Super Admin can assign a lead to an
     * Account Executive, and only once it has been published (Open),
     * or is already with an AE (reassignment). A draft can't be
     * assigned, and neither can a Lost or Closed lead.
     *
     * While an Account Manager is reviewing it, reassigning would
     * pull it out from under them - that is what "Send Back to AE"
     * is for - so only Admin / Super Admin may take it back, as an
     * override for a lead that would otherwise be stuck (e.g. the
     * Account Manager is away).
     */
    public function assign(User $user, Lead $lead): bool
    {
        if (!($user->isAdminOrAbove() || $user->isMis())
            || !$lead->isPublishedOrBeyond()
            || $lead->isFinished()) {
            return false;
        }

        return !$lead->isWithAccountManager() || $user->isAdminOrAbove();
    }

    /**
     * Whether $user currently holds the lead (assigned_to) - the
     * precondition for every workflow action below. Admin / Super
     * Admin deliberately get no override: they monitor the flow and
     * can reassign, but the AE / Account Manager steps are theirs.
     */
    private function holds(User $user, Lead $lead): bool
    {
        return $lead->assigned_to !== null && (int) $lead->assigned_to === $user->id;
    }

    /** The AE starts working an Assigned lead. */
    public function startProcess(User $user, Lead $lead): bool
    {
        return $user->isAe()
            && $this->holds($user, $lead)
            && $lead->status === Lead::STATUS_ASSIGNED;
    }

    /** The AE hands an In Progress (or Sent Back) lead to an Account Manager. */
    public function moveToAccountManager(User $user, Lead $lead): bool
    {
        return $user->isAe()
            && $this->holds($user, $lead)
            && in_array($lead->status, [Lead::STATUS_IN_PROGRESS, Lead::STATUS_SENT_BACK], true);
    }

    /** The Account Manager returns a lead (in review or on Hold) to the AE. */
    public function sendBack(User $user, Lead $lead): bool
    {
        return $user->isManager()
            && $this->holds($user, $lead)
            && $lead->isWithAccountManager();
    }

    /**
     * The Account Manager's single "Update Lead Status" control:
     * Hold, Lost or Close. Available while they hold the lead
     * (in review, or already on Hold - where Hold itself is then
     * simply not offered, see LeadWorkflowService).
     */
    public function updateWorkflowStatus(User $user, Lead $lead): bool
    {
        return $this->sendBack($user, $lead);
    }
}
