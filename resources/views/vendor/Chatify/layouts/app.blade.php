@extends('layout')

@push('styles')
<style>
    :root {
        --chatify-color-chatify-primary: #3f3cbb;
        --chatify-color-chatify-primary-dark: #26215c;
        --chatify-color-chatify-sidebar: #f5f7ff;
        --chatify-color-chatify-bubble-out: #eef0fb;
    }

    html, body {
        height: 100%;
    }

    .chatify-page-wrapper {
        padding: 0 !important;
    }

    #chatify-wrapper {
        height: calc(100vh - var(--chatify-actual-header-height, 64px));
        height: calc(100dvh - var(--chatify-actual-header-height, 64px));
        overflow: hidden;
    }

    #chatify-app {
        height: 100%;
        position: relative;
    }
</style>
@endpush

@php
    $chatifyPageContent = $__env->yieldContent('content');
@endphp

@section('content')
    <div id="chatify-wrapper" class="chatify:antialiased" style="overscroll-behavior: none; touch-action: manipulation;">
        <script>
            (function () {
                function setChatifyHeaderHeight() {
                    var navbar = document.querySelector('.navbar');
                    var height = navbar ? navbar.getBoundingClientRect().height : 64;
                    document.documentElement.style.setProperty('--chatify-actual-header-height', height + 'px');
                }
                setChatifyHeaderHeight();
                window.addEventListener('resize', setChatifyHeaderHeight);
                window.addEventListener('orientationchange', setChatifyHeaderHeight);
            })();
        </script>
        {!! $chatifyPageContent !!}
    </div>
@overwrite
