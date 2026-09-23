<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Products a given user may create/import leads for. Super Admin /
 * Admin get every product; everyone else is limited to the products
 * assigned to them (user->product_id). Shared by LeadController
 * (create/index) and LeadCsvController (template download / CSV
 * import) so the two flows can never drift apart on who is allowed
 * to use which product.
 */
trait ScopesProducts
{
    protected function scopedProductsQuery(User $user): Builder
    {
        if ($user->isAdminOrAbove()) {
            return Product::orderBy('name');
        }

        $assignedProductIds = $user->product_id ?? [];

        return Product::whereIn('id', $assignedProductIds)->orderBy('name');
    }

    protected function userCanUseProduct(User $user, Product $product): bool
    {
        return $this->scopedProductsQuery($user)
            ->whereKey($product->id)
            ->exists();
    }
}
