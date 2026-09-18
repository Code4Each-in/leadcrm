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
