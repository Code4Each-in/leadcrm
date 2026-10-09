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
     * A draft is private to the user who created it - nobody else
     * sees it, not even Admin / Super Admin / MIS. Once published,
     * Admin / Super Admin see every lead. Everyone sees the leads they
     * created. MIS User sees every published lead (Open onwards). The
     * Account Manager a lead is (or was) assigned to keeps seeing it.
     * Account Executives are not part of the workflow, so they only
     * ever see their own leads.
     *
     * Lead::scopeVisibleTo() is the query version of
     * this rule - keep the two in step.
     */
    public function view(User $user, Lead $lead): bool
    {
        if ((int) $lead->created_by === $user->id) {
            return true;
        }

        if ($lead->isDraft()) {
            return false;
        }

        if ($user->isAdminOrAbove()) {
            return true;
        }

        if ($user->isAe()) {
            return false;
        }

        if ($lead->isTeamMember($user)) {
            return true;
        }

        return $user->isMis() && $lead->isPublishedOrBeyond();
    }

    /**
     * A draft can only be edited by its creator (nobody else can
     * even see it). Once
     * published, everyone who can see the lead can edit it except an
     * Account Executive (publishing is a one-way handoff for that
     * role, even on a lead they created) - unless the lead has been
     * handed back to them to edit (Sent Back to AE / Meter Information
     * - Incorrect/Incomplete), for as long as it stays at that stage.
     * This is also what the inline Draft -> Open toggle uses.
     */
    public function update(User $user, Lead $lead): bool
    {
        if ($lead->isDraft()) {
            return (int) $lead->created_by === $user->id;
        }

        if ($user->isAe()) {
            return $lead->isReturnedToAe() && (int) $lead->created_by === $user->id;
        }

        return $this->view($user, $lead);
    }

    /**
     * A draft can only be deleted by its creator; a published lead
     * only by Admin / Super Admin.
     */
    public function delete(User $user, Lead $lead): bool
    {
        if ($lead->isDraft()) {
            return (int) $lead->created_by === $user->id;
        }

        return $user->isAdminOrAbove();
    }

    /**
     * MIS User, Admin and Super Admin assign a published lead to an
     * Account Manager - or reassign it to another one at any point
     * while it is live (including a lead whose pricing was declined,
     * and old-workflow leads still with an AE). A draft can't be
     * assigned, and neither can a Lost or Closed lead. Whether the
     * pricing is ready is checked by LeadWorkflowService.
     */
    public function assign(User $user, Lead $lead): bool
    {
        return ($user->isAdminOrAbove() || $user->isMis())
            && $lead->isPublishedOrBeyond()
            && !$lead->isFinished();
    }

    /**
     * The Pricing section - and anything that would reveal it
     * elsewhere (pricing entries in Lead Logs, pricing decisions in
     * Notes & Documents and the Assignment History) - is for Admin,
     * Super Admin, MIS User and the Account Manager the lead is
     * currently assigned to. Being the creator, an Account Executive,
     * or an Account Manager it was reassigned away from gives no
     * access.
     */
    public function viewPricing(User $user, Lead $lead): bool
    {
        if ($user->isAdminOrAbove() || $user->isMis()) {
            return $this->view($user, $lead);
        }

        return $user->isManager()
            && $lead->assigned_to !== null
            && (int) $lead->assigned_to === $user->id;
    }

    /**
     * Whether $user is the Account Manager currently holding the lead
     * (in review or on Hold) - the precondition for the Account
     * Manager's actions. Admin / Super Admin deliberately get no
     * override: they monitor the flow and can reassign, but these
     * decisions are the Account Manager's.
     */
    private function holdsAsAccountManager(User $user, Lead $lead): bool
    {
        return $user->isManager()
            && $lead->assigned_to !== null
            && (int) $lead->assigned_to === $user->id
            && $lead->isWithAccountManager();
    }

    /**
     * The Account Manager's single "Update Lead Status" control:
     * Hold, Lost or Close. Available while they hold the lead (in
     * review, or already on Hold - where Hold itself is then simply
     * not offered, see LeadWorkflowService).
     */
    public function updateWorkflowStatus(User $user, Lead $lead): bool
    {
        return $this->holdsAsAccountManager($user, $lead);
    }

    /**
     * Contract section (under Pricing, AU Savers): everyone who can
     * see the lead sees it and its documents - except an Account
     * Executive, who never sees it. Uploading is for Admin, Super
     * Admin, MIS User and the Account Manager the lead is assigned to
     * - everyone else is view-only.
     */
    public function viewContracts(User $user, Lead $lead): bool
    {
        return $lead->requiresPricing() && !$user->isAe() && $this->view($user, $lead);
    }

    public function uploadContract(User $user, Lead $lead): bool
    {
        if (!$this->viewContracts($user, $lead) || $lead->isDraft()) {
            return false;
        }

        return $user->isAdminOrAbove()
            || $user->isMis()
            || ($user->isManager() && $lead->assigned_to !== null && (int) $lead->assigned_to === $user->id);
    }

    /**
     * Lead Staging (AU Savers): whoever may edit the lead (update())
     * may change its stage - assigned or not - to one of their role's
     * stages (Lead::stageGroupsFor(); every stage for Admin / Super
     * Admin). An Account Executive picks their stages only in the
     * Save dialog, before the lead is submitted, so never here - they
     * see the stage read-only, as does any role with no stages. Which
     * stage, and whether the lead's stage can change right now
     * (published, not Hold / Lost / Closed), is LeadWorkflowService's
     * check.
     */
    public function updateStage(User $user, Lead $lead): bool
    {
        return $lead->hasStaging()
            && !$lead->isDraft()
            && !$user->isAe()
            && Lead::selectableStagesFor($user) !== []
            && $this->update($user, $lead);
    }
}
