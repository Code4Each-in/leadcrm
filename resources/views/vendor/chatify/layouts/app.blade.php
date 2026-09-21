@extends('layout')

@push('styles')
<style>
    html, body {
        height: 100%;
    }

    #chatify-wrapper {
        height: calc(100vh - 64px);
        overflow: hidden;
    }

    #chatify-app {
        flex: 1 1 auto;
        min-height: 0;
        height: 100%;
        position: relative;
    }

    .chatify-page-wrapper {
        padding: 0 !important;
    }

    .chatify-bottom-bar {
        display: flex !important;
    }

    header {
        display: flex !important;
    }

    .chatify-composer-thread-list {
        max-height: 85vh !important;
        overflow: hidden !important;
    }
    .chatify\:space-y-4 > * + * {
        margin-top: 1rem !important;
    }
    
</style>
@endpush

@section('content')
    <div id="chatify-wrapper" class="chatify:antialiased" style="overscroll-behavior: none; touch-action: manipulation;">
        @yield('content')
    </div>
@endsection
