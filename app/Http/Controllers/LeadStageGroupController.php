<?php

namespace App\Http\Controllers;

use App\Models\LeadStageGroup;
use App\Support\LeadStages;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Stage Management's groups (Admin / Super Admin): name, colour, the
 * products its stages apply to and its Order in the Lead Stages
 * dropdown. Listed with their stages by LeadStageController::index().
 * Two groups for the same product can't share a name; deleting is
 * refused while a group has stages.
 */
class LeadStageGroupController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validateGroup($request);

        $position = $validated['sort_order'];
        unset($validated['sort_order']);

        $group = new LeadStageGroup($validated);
        $group->sort_order = 999999; // placed properly just below
        $group->save();

        // No Order given - added at the end.
        $this->placeAt($group, $position);

        return response()->json([
            'success' => "Group \"{$group->name}\" created successfully.",
        ]);
    }

    public function update(Request $request, LeadStageGroup $leadStageGroup)
    {
        $validated = $this->validateGroup($request, $leadStageGroup);

        $position = $validated['sort_order'];
        unset($validated['sort_order']);

        $leadStageGroup->update($validated);

        // Order left blank - it keeps its place.
        if ($position !== null) {
            $this->placeAt($leadStageGroup, $position);
        }

        return response()->json([
            'success' => "Group \"{$leadStageGroup->name}\" updated successfully.",
        ]);
    }

    public function destroy(LeadStageGroup $leadStageGroup)
    {
        $count = $leadStageGroup->stages()->count();

        if ($count > 0) {
            return response()->json([
                'message' => "\"{$leadStageGroup->name}\" has {$count} " . Str::plural('stage', $count)
                    . ' - delete or move ' . ($count === 1 ? 'it' : 'them') . ' to another group first.',
            ], 422);
        }

        $leadStageGroup->delete();

        $this->renumber(LeadStageGroup::orderBy('sort_order')->orderBy('id')->get());

        return response()->json([
            'success' => "Group \"{$leadStageGroup->name}\" deleted successfully.",
        ]);
    }

    private function validateGroup(Request $request, ?LeadStageGroup $group = null): array
    {
        $request->merge(['name' => trim((string) $request->input('name'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:64'],
            'color' => ['required', Rule::in(array_keys(LeadStageGroup::COLORS))],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['integer', Rule::exists('products', 'id')],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ], [
            'name.required' => 'Group name is required.',
            'name.max' => 'Group name may not be longer than 64 characters.',
            'color.required' => 'Please pick a colour.',
            'color.in' => 'Please pick a valid colour.',
            'products.required' => 'Please select at least one product.',
            'products.min' => 'Please select at least one product.',
            'products.*.exists' => 'Please select valid products.',
            'sort_order.integer' => 'Order must be a whole number.',
            'sort_order.min' => 'Order must be 1 or more.',
            'sort_order.max' => 'Order may not be greater than 9999.',
        ]);

        $validated['products'] = array_values(array_unique(array_map('intval', $validated['products'])));
        $validated['sort_order'] = isset($validated['sort_order']) ? (int) $validated['sort_order'] : null;

        // The Lead Stages dropdown lists a product's groups by name - so
        // one name per product, ignoring case.
        $clash = LeadStageGroup::whereRaw('LOWER(name) = ?', [mb_strtolower($validated['name'])])
            ->when($group, fn ($q) => $q->whereKeyNot($group->id))
            ->get()
            ->first(fn (LeadStageGroup $other) => array_intersect($other->productIds(), $validated['products']));

        if ($clash) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'name' => 'A group with this name already exists for one of these products.',
            ]);
        }

        return $validated;
    }

    /**
     * Puts $group at Order $position (the end when null or past it) and
     * numbers every active group 1, 2, 3 ... with no gaps.
     */
    private function placeAt(LeadStageGroup $group, ?int $position): void
    {
        $others = LeadStageGroup::whereKeyNot($group->id)->orderBy('sort_order')->orderBy('id')->get();
        $index = $position === null ? $others->count() : min(max($position, 1) - 1, $others->count());

        $others->splice($index, 0, [$group]);

        $this->renumber($others);
    }

    private function renumber($groups): void
    {
        foreach ($groups->values() as $i => $group) {
            if ($group->sort_order !== $i + 1) {
                LeadStageGroup::whereKey($group->id)->update(['sort_order' => $i + 1]);
            }
        }

        app(LeadStages::class)->flush();
    }
}
