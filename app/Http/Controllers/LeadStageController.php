<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\LeadStageGroup;
use App\Models\Product;
use App\Models\Role;
use App\Support\LeadStages;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Stage Management (Admin / Super Admin): the Lead Staging groups
 * (LeadStageGroupController) and their stages - name, group, Order in
 * the group and the roles that may pick each one. A stage's products
 * are its group's. A stage's key (the value stored in leads.status) is
 * set once from its first name and never changes, so renaming a stage
 * keeps every lead and history row at it. Deleting is refused while any
 * lead is at the stage; otherwise it's a soft delete, so past stage
 * history keeps the name.
 */
class LeadStageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['groups' => $this->groupRows()]);
        }

        return view('lead-stages.index', [
            'roles' => $this->assignableRoles(),
            'products' => Product::orderBy('id')->get(['id', 'name']),
            'colors' => LeadStageGroup::COLORS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateStage($request);

        $position = $validated['sort_order'];
        unset($validated['sort_order']);

        $stage = new LeadStage($validated);
        $stage->key = $this->uniqueKey($validated['name']);
        $stage->sort_order = 999999; // placed properly just below
        $stage->save();

        // No Order given - added at the end of its group.
        $this->placeAt($stage, $position);

        return response()->json([
            'success' => "Stage \"{$stage->name}\" created successfully.",
        ]);
    }

    public function update(Request $request, LeadStage $leadStage)
    {
        $validated = $this->validateStage($request, $leadStage);

        $position = $validated['sort_order'];
        unset($validated['sort_order']);

        $oldGroupId = (int) $leadStage->group_id;
        $leadStage->update($validated);

        if ($oldGroupId !== (int) $leadStage->group_id) {
            // Moved to another group - the end of it unless an Order is
            // given, and the old group closes the gap.
            $this->placeAt($leadStage, $position);
            $this->renumberGroup($oldGroupId);
        } elseif ($position !== null) {
            // Order left blank - it keeps its place.
            $this->placeAt($leadStage, $position);
        }

        return response()->json([
            'success' => "Stage \"{$leadStage->name}\" updated successfully.",
        ]);
    }

    public function destroy(LeadStage $leadStage)
    {
        $count = $leadStage->leadsAtStageCount();

        if ($count > 0) {
            return response()->json([
                'message' => "{$count} " . Str::plural('lead', $count) . " " . ($count === 1 ? 'is' : 'are')
                    . " at \"{$leadStage->name}\" - move " . ($count === 1 ? 'it' : 'them') . ' to another stage before deleting it.',
            ], 422);
        }

        $leadStage->delete();

        // The stages after it in its group move up - no gap in the numbering.
        $this->renumberGroup((int) $leadStage->group_id);

        return response()->json([
            'success' => "Stage \"{$leadStage->name}\" deleted successfully.",
        ]);
    }

    private function validateStage(Request $request, ?LeadStage $stage = null): array
    {
        $request->merge(['name' => trim((string) $request->input('name'))]);

        $roleIds = $this->assignableRoles()->pluck('id')->all();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                // Unique among the active stages, ignoring case.
                function (string $attribute, mixed $value, \Closure $fail) use ($stage) {
                    $taken = LeadStage::whereRaw('LOWER(name) = ?', [mb_strtolower($value)])
                        ->when($stage, fn ($q) => $q->whereKeyNot($stage->id))
                        ->exists();

                    if ($taken) {
                        $fail('A stage with this name already exists.');
                    }
                },
            ],
            'group_id' => ['required', 'integer', Rule::exists('lead_stage_groups', 'id')->whereNull('deleted_at')],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', Rule::in($roleIds)],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ], [
            'name.required' => 'Stage name is required.',
            'name.max' => 'Stage name may not be longer than 100 characters.',
            'group_id.required' => 'Please select a group.',
            'group_id.integer' => 'Please select a valid group.',
            'group_id.exists' => 'Please select a valid group.',
            'roles.required' => 'Please select at least one role.',
            'roles.min' => 'Please select at least one role.',
            'roles.*.in' => 'Please select valid roles.',
            'sort_order.integer' => 'Order must be a whole number.',
            'sort_order.min' => 'Order must be 1 or more.',
            'sort_order.max' => 'Order may not be greater than 9999.',
        ]);

        $validated['group_id'] = (int) $validated['group_id'];
        $validated['sort_order'] = isset($validated['sort_order']) ? (int) $validated['sort_order'] : null;
        $validated['roles'] = array_values(array_unique(array_map('intval', $validated['roles'])));

        return $validated;
    }

    /**
     * Puts $stage at Order $position in its group (the end when null or
     * past it) and numbers the group's active stages 1, 2, 3 ... with no
     * gaps - the stages from that position on move down one. Other
     * groups are left alone.
     */
    private function placeAt(LeadStage $stage, ?int $position): void
    {
        $others = LeadStage::where('group_id', $stage->group_id)
            ->whereKeyNot($stage->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $index = $position === null ? $others->count() : min(max($position, 1) - 1, $others->count());

        $others->splice($index, 0, [$stage]);

        $this->renumber($others);
    }

    private function renumberGroup(int $groupId): void
    {
        $this->renumber(LeadStage::where('group_id', $groupId)->orderBy('sort_order')->orderBy('id')->get());
    }

    private function renumber($stages): void
    {
        foreach ($stages->values() as $i => $stage) {
            if ($stage->sort_order !== $i + 1) {
                // Quietly - one flush of the stage cache at the end is enough.
                LeadStage::whereKey($stage->id)->update(['sort_order' => $i + 1]);
            }
        }

        app(LeadStages::class)->flush();
    }

    /**
     * Roles a stage can be assigned to. Admin and Super Admin are left
     * out - they can always pick every stage.
     */
    private function assignableRoles()
    {
        return Role::whereNotIn('id', [config('roles.super_admin'), config('roles.admin')])
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * The stored status value for a new stage: its name in snake_case,
     * never one already used by a stage (deleted ones included) or by
     * a non-stage status.
     */
    private function uniqueKey(string $name): string
    {
        // Up to 50 characters with any suffix (leads.draft_stage).
        $base = rtrim(Str::limit(Str::slug($name, '_'), 44, ''), '_') ?: 'stage';
        $reserved = array_merge(
            array_keys(Lead::BASE_STATUS_LABELS),
            LeadStage::withTrashed()->pluck('key')->all()
        );

        $key = $base;
        $n = 2;

        while (in_array($key, $reserved, true)) {
            $key = $base . '_' . $n++;
        }

        return $key;
    }

    /**
     * The active groups in Order, each with its active stages in Order.
     * leads_count isn't listed - the page uses it to explain why a stage
     * can't be deleted before asking to confirm.
     */
    private function groupRows(): array
    {
        $roleNames = Role::withTrashed()->pluck('name', 'id');
        $productNames = Product::pluck('name', 'id');
        $active = app(LeadStages::class)->active();
        $stages = $active->groupBy('group_id');

        // As LeadStage::leadsAtStageCount(), in two queries.
        $leadCounts = Lead::whereIn('status', $active->pluck('key'))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $draftCounts = Lead::where('status', Lead::STATUS_DRAFT)
            ->whereNotNull('draft_stage')
            ->selectRaw('draft_stage, COUNT(*) as total')
            ->groupBy('draft_stage')
            ->pluck('total', 'draft_stage');

        return LeadStageGroup::orderBy('sort_order')->orderBy('id')->get()
            ->map(fn (LeadStageGroup $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'color' => $group->color,
                'sort_order' => $group->sort_order,
                'product_ids' => $group->productIds(),
                'products' => collect($group->productIds())->map(fn ($id) => $productNames[$id] ?? null)->filter()->values(),
                'stages' => collect($stages[$group->id] ?? [])->map(fn (LeadStage $stage) => [
                    'id' => $stage->id,
                    'key' => $stage->key,
                    'name' => $stage->name,
                    'group_id' => $stage->group_id,
                    'sort_order' => $stage->sort_order,
                    'role_ids' => $stage->roleIds(),
                    'roles' => collect($stage->roleIds())->map(fn ($id) => $roleNames[$id] ?? null)->filter()->values(),
                    'leads_count' => (int) (($stage->is_draft ? $draftCounts : $leadCounts)[$stage->key] ?? 0),
                ])->values(),
            ])->values()->all();
    }
}
