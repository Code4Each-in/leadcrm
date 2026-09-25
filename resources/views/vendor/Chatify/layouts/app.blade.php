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

    /*
     * The site theme's reboot CSS resets HTML5 sectioning elements
     * (article, aside, header, main, nav, section, ...) to `display: block`
     * as a plain, unlayered rule. Chatify's compiled Tailwind utilities
     * (e.g. `chatify:flex` on <main>/<aside>) live inside `@layer utilities`,
     * and per the CSS cascade-layers spec, ANY unlayered author rule beats
     * ANY layered author rule regardless of selector specificity. That
     * silently breaks every flex layout chatify builds on these tags.
     * `revert-layer` tells the cascade to ignore this specific unlayered
     * override here and fall through to what the layered utility classes
     * intended, without touching the reset anywhere else on the site.
     */
    #chatify-app :where(article, aside, figcaption, figure, footer, header, hgroup, main, nav, section) {
        display: revert-layer;
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
