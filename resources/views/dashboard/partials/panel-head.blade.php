{{-- A panel's title bar. Expects $title; optional $subtitle, $linkUrl
     and $linkLabel. --}}
<div class="panel-head">
    <div>
        <p class="panel-title">{{ $title }}</p>
        @if(!empty($subtitle))
            <p class="panel-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if(!empty($linkUrl))
        <a href="{{ $linkUrl }}" class="panel-link">{{ $linkLabel ?? 'View all' }} <i class="mdi mdi-arrow-right"></i></a>
    @endif
</div>
