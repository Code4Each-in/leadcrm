{{--
    One modal overlay for the whole app. Pages open dialogs three ways:
      - Bootstrap modals (.modal + .modal-backdrop) - Users, Roles, Agency,
        Attendance
      - SweetAlert2 (Swal.fire) - confirmations everywhere, including the
        header's Log out
      - hand-rolled overlays (.reminder-modal-overlay, .pricing-modal-overlay,
        .csv-modal-overlay) - Lead details, Leads list
    Each came with its own backdrop colour (or a very faint one), so this
    gives them all the same dimmed, slightly blurred backdrop, keeps them
    above the fixed header, and locks the page behind while one is open.

    Included at the end of <body> (layout.blade.php) so it wins over the
    page-level styles without needing extra specificity.
--}}
<style>
    :root {
        --app-overlay-bg: rgba(15, 23, 42, 0.55);
        --app-overlay-blur: 2px;
    }

    /* Bootstrap: fade the backdrop's opacity 0 -> 1 over a translucent
       colour, instead of a solid black at 0.5. */
    .modal-backdrop {
        background-color: var(--app-overlay-bg);
    }

    .modal-backdrop.show {
        opacity: 1;
        -webkit-backdrop-filter: blur(var(--app-overlay-blur));
        backdrop-filter: blur(var(--app-overlay-blur));
    }

    /* Hand-rolled overlays. */
    .reminder-modal-overlay,
    .pricing-modal-overlay,
    .csv-modal-overlay {
        background: var(--app-overlay-bg) !important;
        -webkit-backdrop-filter: blur(var(--app-overlay-blur));
        backdrop-filter: blur(var(--app-overlay-blur));
    }

    /* SweetAlert2: always above every other modal (the hand-rolled ones
       sit at 99999) and the header, on every page - not just Leads. */
    .swal2-container {
        z-index: 100000 !important;
    }

    /* Dialogs only - toasts (.swal2-toast) keep their transparent,
       click-through container. */
    .swal2-container.swal2-backdrop-show:has(> .swal2-popup.swal2-modal) {
        background: var(--app-overlay-bg) !important;
        -webkit-backdrop-filter: blur(var(--app-overlay-blur));
        backdrop-filter: blur(var(--app-overlay-blur));
    }

    /* Bootstrap (.modal-open) and SweetAlert2 (.swal2-shown) lock the page
       themselves; this does the same for the hand-rolled overlays. */
    body.app-modal-open {
        overflow: hidden;
    }
</style>

<script>
(function () {
    const SELECTOR = '.reminder-modal-overlay, .pricing-modal-overlay, .csv-modal-overlay';
    let queued = false;

    function isOpen(el) {
        return el.classList.contains('show') || window.getComputedStyle(el).display !== 'none';
    }

    function sync() {
        queued = false;
        const open = Array.prototype.some.call(document.querySelectorAll(SELECTOR), isOpen);
        document.body.classList.toggle('app-modal-open', open);
    }

    function queue() {
        if (queued) return;
        queued = true;
        window.requestAnimationFrame(sync);
    }

    if (!document.querySelector(SELECTOR)) return;

    new MutationObserver(function (mutations) {
        for (let i = 0; i < mutations.length; i++) {
            const t = mutations[i].target;
            if (t.nodeType === 1 && t.matches(SELECTOR)) {
                queue();
                return;
            }
        }
    }).observe(document.body, { subtree: true, attributes: true, attributeFilter: ['class', 'style'] });

    sync();
})();
</script>
