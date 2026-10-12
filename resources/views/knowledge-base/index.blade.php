@extends('layout')
@section('title', $section['title'])
@section('subtitle', $section['title'])
@section('content')
<style>
    /* ==========================================================
       Header (same eyebrow/title pattern used on Roles / Login Logs)
       ========================================================== */
    #knowledgeBaseCard .kb-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
        gap: 14px;
        flex-wrap: wrap;
    }

    #knowledgeBaseCard .kb-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #6c63ff;
        margin-bottom: 10px;
    }

    #knowledgeBaseCard .kb-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #knowledgeBaseCard .kb-header h4 {
        font-weight: 700;
        font-size: 27px;
        color: #1a1f2b;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    #knowledgeBaseCard .kb-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
        max-width: 560px;
    }

    /* ==========================================================
       Category filter chips
       ========================================================== */
    #knowledgeBaseCard .kb-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 26px;
    }

    #knowledgeBaseCard .kb-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid #eeeef5;
        background: #fff;
        color: #6f6f80;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    #knowledgeBaseCard .kb-chip i {
        font-size: 15px;
    }

    #knowledgeBaseCard .kb-chip:hover {
        border-color: #d9d6ff;
        color: #4b49ac;
    }

    #knowledgeBaseCard .kb-chip.active {
        background: #4b49ac;
        border-color: #4b49ac;
        color: #fff;
    }

    /* ==========================================================
       Resource cards
       ========================================================== */
    /* Sized like the dashboard's KPI cards (dashboard/partials/styles). */
    #knowledgeBaseCard .kb-grid > .kb-item {
        display: flex; /* not .d-flex - its !important would beat the filter's .toggle() */
        margin-bottom: 24px;
    }

    #knowledgeBaseCard .kb-card {
        display: flex;
        flex-direction: column;
        width: 100%;
        min-height: 210px;
        background: #fff;
        border: 1px solid #eeeef5;
        border-radius: 16px;
        padding: 22px 24px;
        color: inherit;
        text-decoration: none;
        transition: box-shadow .15s ease, transform .15s ease, border-color .15s ease;
    }

    #knowledgeBaseCard .kb-card:hover {
        border-color: #dcdff0;
        box-shadow: 0 10px 24px rgba(26, 31, 43, .08);
        transform: translateY(-2px);
        text-decoration: none;
    }

    #knowledgeBaseCard .kb-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
        flex-shrink: 0;
    }

    #knowledgeBaseCard .kb-icon.type-document { background: #eeedfe; color: #4b49ac; }
    #knowledgeBaseCard .kb-icon.type-link     { background: #eaf0ff; color: #3b5bfd; }
    #knowledgeBaseCard .kb-icon.type-video    { background: #fdecec; color: #dc3545; }
    #knowledgeBaseCard .kb-icon.type-policy   { background: #e9f8ef; color: #198754; }
    #knowledgeBaseCard .kb-icon.type-faq      { background: #fff4db; color: #b77900; }

    #knowledgeBaseCard .kb-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #26215c;
        line-height: 1.35;
        margin-bottom: 6px;
    }

    #knowledgeBaseCard .kb-card-desc {
        font-size: 13.5px;
        color: #77778a;
        line-height: 1.5;
        margin-bottom: 18px;
        flex-grow: 1;
    }

    #knowledgeBaseCard .kb-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid #f0f1f5;
    }

    #knowledgeBaseCard .kb-meta {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #a4aab5;
    }

    #knowledgeBaseCard .kb-card-link {
        font-size: 13px;
        font-weight: 600;
        color: #4b49ac;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    #knowledgeBaseCard .kb-card:hover .kb-card-link {
        color: #3f3e91;
    }

    #knowledgeBaseCard .kb-empty {
        display: none;
        padding: 40px 15px;
        text-align: center;
        color: #8a8a9a;
        font-size: 13px;
    }

    @media (max-width: 767px) {
        #knowledgeBaseCard .kb-header {
            text-align: center;
            justify-content: center;
        }

        #knowledgeBaseCard .kb-toolbar {
            justify-content: center;
        }
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card" id="knowledgeBaseCard">
            <div class="card-body">

                {{-- Header --}}
                <div class="kb-header">
                    <div>
                        <div class="kb-eyebrow">
                            Resource Library
                        </div>
                        <h4 class="card-title mb-1">
                            {{ $section['title'] }}
                        </h4>
                        <p>
                            {{ $section['description'] }}
                        </p>
                    </div>
                </div>

                {{-- Category filters --}}
                <div class="kb-toolbar" id="kbToolbar">
                    <span class="kb-chip active" data-filter="all">
                        <i class="mdi mdi-view-grid-outline"></i>
                        All
                    </span>
                    @foreach ($categories as $key => $category)
                        <span class="kb-chip" data-filter="{{ $key }}">
                            <i class="mdi {{ $category['icon'] }}"></i>
                            {{ $category['label'] }}
                        </span>
                    @endforeach
                </div>

                {{-- Resource grid --}}
                {{-- The whole card is the link; documents open in the viewer in a new tab. --}}
                <div class="row kb-grid" id="kbGrid">
                    @foreach ($items as $item)
                        <div class="col-xl-4 col-md-6 col-12 kb-item" data-category="{{ $item['type'] }}">
                            <a href="{{ $item['url'] }}" class="kb-card" target="_blank" rel="noopener">
                                <div class="kb-icon type-{{ $item['type'] }}">
                                    <i class="mdi {{ $categories[$item['type']]['icon'] ?? 'mdi-file-outline' }}"></i>
                                </div>
                                <div class="kb-card-title">{{ $item['title'] }}</div>
                                <div class="kb-card-desc">{{ $item['description'] ?? '' }}</div>
                                <div class="kb-card-footer">
                                    <span class="kb-meta">{{ $item['meta'] ?? '' }}</span>
                                    <span class="kb-card-link">
                                        {{ isset($item['file']) ? 'View' : 'Open' }}
                                        <i class="mdi mdi-arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="kb-empty" id="kbEmpty" @if (empty($items)) style="display: block;" @endif>
                    No resources in this category yet.
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#kbToolbar').on('click', '.kb-chip', function () {
            var filter = $(this).data('filter');

            $('#kbToolbar .kb-chip').removeClass('active');
            $(this).addClass('active');

            var $items = $('#kbGrid .kb-item');
            var visible = 0;

            $items.each(function () {
                var match = filter === 'all' || $(this).data('category') === filter;
                $(this).toggle(match);
                if (match) visible++;
            });

            $('#kbEmpty').toggle(visible === 0);
        });
    });
</script>
@endsection
