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
<div
    id="chat-unread-toast"
    style="
        position: fixed;
        top: 75px;
        right: 25px;
        z-index: 99999;
        display: none;
        width: 360px;
        max-width: calc(100vw - 40px);
    "
>
    <div
        style="
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0,0,0,.18);
            padding: 18px 20px;
            border-left: 4px solid #dc3545;
        "
    >

        {{-- Header --}}
        <div
            style="
                display:flex;
                align-items:center;
                gap:12px;
            "
        >

            {{-- Icon --}}
            <div
                style="
                    width:42px;
                    height:42px;
                    min-width:42px;
                    border-radius:50%;
                    background:#f8d7da;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                "
            >
                <i
                    class="ti-comments"
                    style="
                        font-size:20px;
                        color:#dc3545;
                    "
                ></i>
            </div>

            {{-- Text --}}
            <div style="flex:1;">

                <div
                    style="
                        font-size:15px;
                        font-weight:600;
                        color:#343a40;
                    "
                >
                    New Chat Message
                </div>

                <div
                    id="chat-unread-toast-text"
                    style="
                        font-size:13px;
                        color:#6c757d;
                        margin-top:4px;
                    "
                >
                    You have unread messages.
                </div>

            </div>

            {{-- Close --}}
            <button
                type="button"
                id="chat-unread-toast-close"
                aria-label="Close"
                style="
                    border:0;
                    background:transparent;
                    color:#6c757d;
                    font-size:22px;
                    line-height:1;
                    cursor:pointer;
                    padding:0;
                "
            >
                &times;
            </button>

        </div>
        </div>

    </div>
</div>
@if(auth()->check())

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


        if (count === 1) {

            toastText.textContent =
                'You have 1 unread message.';

        } else {

            toastText.textContent =
                'You have ' + count + ' unread messages.';
        }


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
    checkUnreadMessages();

    setInterval(
        checkUnreadMessages,
        10000
    );


})();
</script>

@endif
</body>

</html>
