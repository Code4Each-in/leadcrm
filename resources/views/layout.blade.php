<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.css')
    @stack('styles')
</head>
<body>
 <div class="container-scroller">
     @include('includes.header')

    <div class="container-fluid page-body-wrapper">
      @include('includes.rightsidebar')
      @include('includes.sidebar')
      <div class="main-panel">
        <div class="content-wrapper chatify-page-wrapper">
          @yield('content')
        </div>
        </div>
    </div>
</div>
    @include('includes.jss')
    @include('includes.modal-overlay')
    <script type="text/javascript">
    $(document).ready(function() {});
    </script>
    @yield('js_scripts')
    <script>
    $(document).ready(function () {
        $('.select2-basic').select2({
            width: '100%'
        });
    });


</script>
@if(auth()->check())

<script>
// Session heartbeat. The session (and the inactivity timeout, when one is
// configured) only sees requests, so someone filling in a long form or
// reading a lead without clicking anything could be logged out on their
// next click. Real interaction (keys, clicks, scrolling, mouse) pings the
// server at most every few minutes; a page left untouched sends nothing.
(function () {
    const heartbeatUrl = @json(route('session.heartbeat'));
    const loginUrl = @json(route('login'));
    const MIN_INTERVAL_MS = 5 * 60 * 1000;

    let lastPing = Date.now();
    let inFlight = false;

    function ping() {
        if (inFlight || Date.now() - lastPing < MIN_INTERVAL_MS) return;

        inFlight = true;
        lastPing = Date.now();

        fetch(heartbeatUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            cache: 'no-store'
        }).then(function (res) {
            if (res.status === 401) {
                window.location.href = loginUrl;
            }
        }).catch(function () {
            // Offline / network blip - retry on interaction in ~30s.
            lastPing = Date.now() - MIN_INTERVAL_MS + 30000;
        }).finally(function () {
            inFlight = false;
        });
    }

    ['keydown', 'mousedown', 'mousemove', 'wheel', 'touchstart', 'scroll'].forEach(function (evt) {
        window.addEventListener(evt, ping, { passive: true, capture: true });
    });
})();
</script>

<script>
(function () {

    let lastUnreadCount = null;
    let latestConversationId = null;
    let popupClosed = false;

    const countElement =
        document.getElementById('chat-unread-count');

    const toastElement =
        document.getElementById('chat-unread-toast');

    const toastText =
        document.getElementById('chat-unread-toast-text');

    const toastClose =
        document.getElementById('chat-unread-toast-close');

    const openChatButton =
        document.getElementById('chat-open-unread-btn');


    if (!countElement || !toastElement) {
        return;
    }

    function showUnreadPopup(count) {

        if (count <= 0) {
            return;
        }

        if (popupClosed) {
            return;
        }


        const countText =
            document.getElementById('chat-unread-count-text');

        if (countText) {
            countText.textContent = count;
        }

        toastText.innerHTML =
            count === 1
                ? 'You have <span id="chat-unread-count-text" style="color:#dc3545; font-weight:700;">' + count + '</span> unread message.'
                : 'You have <span id="chat-unread-count-text" style="color:#dc3545; font-weight:700;">' + count + '</span> unread messages.';


        toastElement.style.display = 'block';
    }

    function hideUnreadPopup() {

        toastElement.style.display = 'none';

        popupClosed = true;
    }

    function updateSidebarCount(count) {

        count = parseInt(count, 10) || 0;


        if (count > 0) {

            countElement.textContent = count;

            countElement.style.display =
                'inline-block';

        } else {

            countElement.textContent = '0';

            countElement.style.display =
                'none';
        }
    }
    async function checkUnreadMessages() {

        try {

            const response = await fetch(
                '{{ route('chat.unread.count') }}',
                {
                    method: 'GET',

                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    credentials: 'same-origin'
                }
            );


            if (!response.ok) {
                return;
            }


            const data = await response.json();


            const count =
                parseInt(data.count, 10) || 0;


            latestConversationId =
                data.conversation_id || null;

            updateSidebarCount(count);

            if (count > 0) {

                if (lastUnreadCount === null) {

                    popupClosed = false;

                    showUnreadPopup(count);
                }

                else if (count > lastUnreadCount) {

                    popupClosed = false;

                    showUnreadPopup(count);
                }

            }

            if (count === 0) {

                popupClosed = false;

                toastElement.style.display = 'none';
            }


            lastUnreadCount = count;


        } catch (error) {

            console.error(
                'Unable to check unread chat messages:',
                error
            );

        }
    }

    if (toastClose) {

        toastClose.addEventListener(
            'click',
            function () {

                hideUnreadPopup();

            }
        );
    }

    if (openChatButton) {

        openChatButton.addEventListener(
            'click',
            function () {

                if (latestConversationId) {

                    const chatUrl =
                        '{{ url('/chatify') }}' +
                        '?conversation=' +
                        encodeURIComponent(
                            latestConversationId
                        );


                    window.open(
                        chatUrl,
                        '_blank'
                    );

                } else {

                    window.open(
                        '{{ url('/chatify') }}',
                        '_blank'
                    );
                }


                hideUnreadPopup();
            }
        );
    }
    checkUnreadMessages();
    setInterval(
        checkUnreadMessages,
        10000
    );

})();
</script>

@endif
@if(session('success'))
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: @json(session('success')),
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
});
</script>
@endif
@if(session('error'))
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'error',
    title: @json(session('error')),
    showConfirmButton: false,
    timer: 2800,
    timerProgressBar: true,
});
</script>
@endif


</body>

</html>
