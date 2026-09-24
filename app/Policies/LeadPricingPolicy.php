<?php

namespace App\Policies;

use App\Models\LeadPricing;
use App\Models\User;

/**
 * Single source of truth for who can see/change a lead's pricing.
 * Auto-discovered by Laravel (class name matches the {Model}Policy
 * convention - LeadPricing -> LeadPricingPolicy), no explicit
 * registration needed. Mirrors LeadPolicy's style.
 */
class LeadPricingPolicy
{
    /**
     * Pricing is visible to anyone who can view the underlying lead -
     * everyone else stays view-only, only creating/editing/deleting
     * is restricted (see below).
     */
    public function view(User $user, LeadPricing $pricing): bool
    {
        return $user->can('view', $pricing->lead);
    }

    /**
     * Only MIS User, Admin and Super Admin can add, edit or delete
     * pricing - everyone else (including the lead's own creator) is
     * view-only for this section.
     */
    public function create(User $user): bool
    {
        return $user->isAdminOrAbove() || $user->isMis();
    }

    public function update(User $user, LeadPricing $pricing): bool
    {
        return $user->isAdminOrAbove() || $user->isMis();
    }

    public function delete(User $user, LeadPricing $pricing): bool
    {
        return $user->isAdminOrAbove() || $user->isMis();
    }
}
