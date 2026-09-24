<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadPricing;
use App\Services\LeadPricingCreationService;
use App\Support\LeadPricingCalculator;
use App\Support\LeadPricingValidationRules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadPricingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Full pricing history for a lead, newest first - used by the
     * "Pricing History" modal on the Lead Show page.
     */
    public function index(Lead $lead)
    {
        $this->authorize('view', $lead);

        return response()->json(
            $lead->pricings()->with(['supplier', 'creator:id,name'])->get()
        );
    }

    /**
     * Always creates a NEW pricing record rather than editing an
     * existing one - so previous pricing is never overwritten and
     * stays available as history (see Lead::pricings()). The most
     * recently created record is automatically the "current" one
     * (Lead::currentPricing()).
     */
    public function store(Request $request, Lead $lead)
    {
        $this->authorize('create', LeadPricing::class);

        if (!$lead->isAuSavers()) {
            abort(422, 'Pricing is only applicable to AU Savers leads.');
        }

        $validated = $request->validate(
            LeadPricingValidationRules::rules(),
            LeadPricingValidationRules::messages()
        );

        $validated['created_by'] = Auth::id();

        $pricing = app(LeadPricingCreationService::class)->create($validated, $lead);

        return response()->json([
            'success' => true,
            'pricing' => $pricing->load(['supplier', 'creator:id,name']),
            'message' => $pricing->status === 'draft'
                ? 'Pricing saved as draft.'
                : 'Pricing published.',
        ]);
    }

    /**
     * Editing in place is only allowed while the record is still a
     * draft - once published it's a locked historical snapshot, and
     * any further change has to go through store() as a new record
     * (same "publishing is one-way" spirit as LeadPolicy::update()
     * for leads themselves).
     */
    public function update(Request $request, LeadPricing $pricing)
    {
        $this->authorize('update', $pricing);

        if ($pricing->status === 'published') {
            abort(403, 'Published pricing cannot be edited - save a new pricing record instead.');
        }

        $validated = $request->validate(
            LeadPricingValidationRules::rules($pricing),
            LeadPricingValidationRules::messages()
        );

        $validated['total_eac_kwh'] = LeadPricingCalculator::totalEac($validated);
        $validated['annual_spend'] = LeadPricingCalculator::annualSpend($validated);

        $pricing->update($validated);

        return response()->json([
            'success' => true,
            'pricing' => $pricing->fresh()->load(['supplier', 'creator:id,name']),
            'message' => $pricing->status === 'draft'
                ? 'Pricing draft updated.'
                : 'Pricing published.',
        ]);
    }

    public function destroy(LeadPricing $pricing)
    {
        $this->authorize('delete', $pricing);

        $pricing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pricing record deleted.',
        ]);
    }
}
