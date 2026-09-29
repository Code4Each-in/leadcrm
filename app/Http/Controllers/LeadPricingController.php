<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadPricing;
use App\Services\LeadPricingCreationService;
use App\Services\LeadWorkflowService;
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
        $this->authorize('viewPricing', $lead);

        return response()->json(
            $lead->pricings()->with(['supplier', 'creator:id,name', 'reviewer:id,name'])->get()
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
        $this->authorize('viewPricing', $lead);
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
            'message' => $this->savedMessage($pricing),
        ]);
    }

    /**
     * What saving the pricing actually did - and, once published, who
     * it went to: the Account Manager already holding the lead (now
     * notified to review it), or the one it was imported for (now
     * assigned).
     */
    private function savedMessage(LeadPricing $pricing): string
    {
        if ($pricing->isDraft()) {
            return 'Pricing saved as a draft.';
        }

        $lead = $pricing->lead()->with('assignee')->first();
        $am = $lead?->isWithAccountManager() ? $lead->assignee : null;

        if (!$am || $am->id === Auth::id()) {
            return 'Pricing published.';
        }

        return "Pricing published. {$am->name} has been notified to review it.";
    }

    /**
     * Editing in place is only allowed while the record is still a
     * draft - once published (and then approved / declined by the
     * Account Manager) it's a locked historical snapshot, and any
     * further change has to go through store() as a new record (same
     * "publishing is one-way" spirit as LeadPolicy::update() for
     * leads themselves).
     */
    public function update(Request $request, LeadPricing $pricing)
    {
        $this->authorize('update', $pricing);

        if (!$pricing->isDraft()) {
            abort(403, 'Published pricing cannot be edited. Add new pricing instead.');
        }

        $validated = $request->validate(
            LeadPricingValidationRules::rules($pricing),
            LeadPricingValidationRules::messages()
        );

        $validated['total_eac_kwh'] = LeadPricingCalculator::totalEac($validated);
        $validated['annual_spend'] = LeadPricingCalculator::annualSpend($validated);

        $pricing->update($validated);

        // Publishing a draft on a lead that is already with an Account
        // Manager sends it straight back to them for review.
        if ($pricing->isPublished()) {
            app(LeadWorkflowService::class)->pricingPublished($pricing, Auth::user());
        }

        return response()->json([
            'success' => true,
            'pricing' => $pricing->fresh()->load(['supplier', 'creator:id,name']),
            'message' => $pricing->isDraft() ? 'Pricing draft updated.' : $this->savedMessage($pricing),
        ]);
    }

    public function destroy(LeadPricing $pricing)
    {
        $this->authorize('delete', $pricing);

        // An approved / declined record is the Account Manager's
        // decision on record (see Notes & Documents and the
        // Assignment History) - it stays.
        if ($pricing->isApproved() || $pricing->isDeclined()) {
            abort(403, 'Approved or declined pricing cannot be deleted.');
        }

        $pricing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pricing deleted.',
        ]);
    }
}
