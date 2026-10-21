<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Stage groups move out of LeadStage::GROUPS into Stage Management:
 * each has a name, a colour, its products and its place in the Lead
 * Stages dropdown. A stage now belongs to a group (group_id) and takes
 * its products from it - lead_stages.group and lead_stages.products go.
 * Order becomes per group: each group's stages are numbered 1, 2, 3 ...
 * in the order they were in.
 *
 * Seeded with the six groups as they were, same order and colours,
 * each for the products its stages were for.
 */
return new class extends Migration
{
    private const GROUPS = [
        'Lead' => 'lead',
        'Pricing' => 'pricing',
        'Tender Pricing' => 'tender',
        'Closing' => 'closing',
        'LOA' => 'loa',
        'Contract' => 'contracts',
    ];

    public function up(): void
    {
        Schema::create('lead_stage_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('color', 32);
            $table->json('products');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('lead_stages', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('name')->constrained('lead_stage_groups');
        });

        $stages = DB::table('lead_stages')
            ->orderByRaw('deleted_at IS NOT NULL')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'group', 'products', 'deleted_at']);

        $now = now();
        $order = 0;

        foreach (self::GROUPS as $name => $color) {
            $groupStages = $stages->where('group', $name);

            $products = $groupStages->whereNull('deleted_at')
                ->flatMap(fn ($stage) => json_decode($stage->products, true) ?: [])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values()
                ->all();

            $groupId = DB::table('lead_stage_groups')->insertGetId([
                'name' => $name,
                'color' => $color,
                'products' => json_encode($products ?: [3]), // Product::AU_SAVERS_ID
                'sort_order' => ++$order,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($groupStages->values() as $i => $stage) {
                DB::table('lead_stages')->where('id', $stage->id)->update([
                    'group_id' => $groupId,
                    'sort_order' => $i + 1,
                ]);
            }
        }

        Schema::table('lead_stages', function (Blueprint $table) {
            $table->dropColumn(['group', 'products']);
        });
    }

    public function down(): void
    {
        Schema::table('lead_stages', function (Blueprint $table) {
            $table->string('group', 32)->default('')->after('name');
            $table->json('products')->nullable()->after('roles');
        });

        $groups = DB::table('lead_stage_groups')->get()->keyBy('id');

        // Back to one sequence across all stages, group by group.
        $stages = DB::table('lead_stages')
            ->leftJoin('lead_stage_groups', 'lead_stage_groups.id', '=', 'lead_stages.group_id')
            ->orderByRaw('lead_stages.deleted_at IS NOT NULL')
            ->orderBy('lead_stage_groups.sort_order')
            ->orderBy('lead_stages.sort_order')
            ->orderBy('lead_stages.id')
            ->get(['lead_stages.id', 'lead_stages.group_id']);

        foreach ($stages as $i => $stage) {
            $group = $groups[$stage->group_id] ?? null;

            DB::table('lead_stages')->where('id', $stage->id)->update([
                'group' => $group->name ?? 'Lead',
                'products' => $group->products ?? json_encode([3]),
                'sort_order' => $i + 1,
            ]);
        }

        Schema::table('lead_stages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('group_id');
        });

        Schema::dropIfExists('lead_stage_groups');
    }
};
