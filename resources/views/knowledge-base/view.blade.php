@extends('layout')
@section('title', $item['title'])
@section('subtitle', $section['title'])
@section('content')
<style>
    #kbViewerCard .kb-viewer-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
        padding-bottom: 20px;
        border-bottom: 1px solid #eef0f3;
        margin-bottom: 22px;
    }

    #kbViewerCard .kb-eyebrow {
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

    #kbViewerCard .kb-eyebrow::before {
        content: '';
        width: 16px;
        height: 2px;
        background: #6c63ff;
        display: inline-block;
    }

    #kbViewerCard h4 {
        font-weight: 700;
        font-size: 24px;
        color: #1a1f2b;
        margin-bottom: 4px;
    }

    #kbViewerCard .kb-viewer-header p {
        color: #8a92a3;
        font-size: 13.5px;
        margin: 0;
    }

    #kbViewerCard .kb-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 12px;
        border: 1px solid #eeeef5;
        color: #4b49ac;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    #kbViewerCard .kb-back:hover {
        border-color: #d9d6ff;
        text-decoration: none;
    }

    #kbViewerCard .kb-frame {
        width: 100%;
        height: calc(100vh - 260px);
        min-height: 600px;
        border: 1px solid #eeeef5;
        border-radius: 12px;
        background: #f6f7fb;
    }

    #kbViewerCard .kb-docx {
        overflow: auto;
        padding: 0;
    }

    /* docx-preview draws each page on a grey "desk"; keep it inside our frame. */
    #kbViewerCard .kb-docx .docx-wrapper {
        background: transparent;
        padding: 24px;
    }

    #kbViewerCard .kb-image {
        max-width: 100%;
        border-radius: 12px;
        border: 1px solid #eeeef5;
    }

    #kbViewerCard .kb-viewer-message {
        padding: 60px 15px;
        text-align: center;
        color: #8a8a9a;
        font-size: 13.5px;
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card" id="kbViewerCard">
            <div class="card-body">

                <div class="kb-viewer-header">
                    <div>
                        <div class="kb-eyebrow">{{ $section['title'] }}</div>
                        <h4>{{ $item['title'] }}</h4>
                        @if (!empty($item['description']))
                            <p>{{ $item['description'] }}</p>
                        @endif
                    </div>
                    <a href="{{ route('knowledge-base.index') }}" class="kb-back">
                        <i class="mdi mdi-arrow-left"></i>
                        Back to {{ $section['title'] }}
                    </a>
                </div>

                @if ($extension === 'pdf')
                    {{-- toolbar=0 hides the built-in PDF viewer's download/print bar. --}}
                    <iframe class="kb-frame" src="{{ $fileUrl }}#toolbar=0&navpanes=0" title="{{ $item['title'] }}"></iframe>
                @elseif ($extension === 'docx')
                    <div class="kb-frame kb-docx" id="kbDocx">
                        <div class="kb-viewer-message" id="kbDocxStatus">Loading document…</div>
                    </div>
                @elseif (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true))
                    <img class="kb-image" src="{{ $fileUrl }}" alt="{{ $item['title'] }}">
                @else
                    <div class="kb-viewer-message">A preview isn't available for this file type.</div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@if ($extension === 'docx')
    @section('js_scripts')
        <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.5/dist/docx-preview.min.js"></script>
        <script>
            (function () {
                var container = document.getElementById('kbDocx');
                var status = document.getElementById('kbDocxStatus');

                fetch(@json($fileUrl), { credentials: 'same-origin' })
                    .then(function (response) {
                        if (!response.ok) throw new Error(response.status);
                        return response.blob();
                    })
                    .then(function (blob) {
                        return docx.renderAsync(blob, container, null, {
                            inWrapper: true,
                            ignoreLastRenderedPageBreak: true,
                        });
                    })
                    .then(function () {
                        status.remove();
                    })
                    .catch(function () {
                        // renderAsync empties the container first, so
                        // rewrite the message rather than reuse #kbDocxStatus.
                        container.innerHTML = '<div class="kb-viewer-message">This document could not be displayed.</div>';
                    });
            })();
        </script>
    @endsection
@endif
