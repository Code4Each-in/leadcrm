<!DOCTYPE html>
<html lang="en">
<head>
@include('includes.css')
</head>
<body>
 <div class="container-scroller">
     @include('includes.header')

    <div class="container-fluid page-body-wrapper">
      @include('includes.rightsidebar')
      @include('includes.sidebar')
      <div class="main-panel">
        <div class="content-wrapper">
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
</body>

</html>
