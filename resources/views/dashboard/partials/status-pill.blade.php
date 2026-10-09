{{-- A lead status as a pill, in the lead listing's colours. Expects
     $status (stored value) and $label. --}}
@php
    // Lead Staging group colours (Lead::STAGE_GROUP_SLUGS).
    $stageGroupClasses = [
        'lead' => 'pill-lead',
        'pricing' => 'pill-pricing',
        'closing' => 'pill-issues',
        'tender' => 'pill-tender',
        'loa' => 'pill-loa',
        'contracts' => 'pill-contracts',
    ];

    $pillClass = match ($status) {
        \App\Models\Lead::STATUS_DRAFT => 'pill-draft',
        \App\Models\Lead::STATUS_PUBLISHED => 'pill-open',
        \App\Models\Lead::STATUS_WITH_ACCOUNT_MANAGER => 'pill-am',
        \App\Models\Lead::STATUS_ASSIGNED, \App\Models\Lead::STATUS_IN_PROGRESS => 'pill-legacy',
        \App\Models\Lead::STATUS_SENT_BACK => 'pill-sent_back',
        \App\Models\Lead::STATUS_HOLD => 'pill-hold',
        \App\Models\Lead::STATUS_LOST => 'pill-lost',
        \App\Models\Lead::STATUS_CLOSED => 'pill-closed',
        \App\Models\Lead::STATUS_CONTRACT_LIVE => 'pill-live',
        default => $stageGroupClasses[\App\Models\Lead::stageGroupSlugs()[$status] ?? ''] ?? '',
    };
@endphp
<span class="status-pill {{ $pillClass }}">{{ $label }}</span>
