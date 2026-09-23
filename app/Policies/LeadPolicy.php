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
     * Published leads are visible only to MIS User, Admin, Super
     * Admin, and the lead's creator. Non-published leads keep the
     * original rule: creator or Admin/Super Admin only - MIS User
     * gets no special access to other people's drafts.
     */
    public function view(User $user, Lead $lead): bool
    {
        if ($user->isAdminOrAbove()) {
            return true;
        }

        if ($lead->created_by === $user->id) {
            return true;
        }

        return $user->isMis() && $lead->status === 'published';
    }

    /**
     * Account Executives can never edit a published lead, even one
     * they created themselves - publishing is a one-way handoff for
     * that role. Everyone else who can view a lead can also update
     * it (this is also what the inline status toggle on the show
     * page uses, so an AE can't route around the edit block by
     * un-publishing first).
     */
    public function update(User $user, Lead $lead): bool
    {
        if ($lead->status === 'published' && $user->isAe()) {
            return false;
        }

        return $this->view($user, $lead);
    }
}
