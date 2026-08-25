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
    icon: 'success',
    title: 'Success!',
    text: @json(session('success')),
    timer: 2500,
    showConfirmButton: false,
    background: '#ffffff',
    color: '#2c3e50',
    iconColor: '#28a745',
    customClass: {
        popup: 'swal-rounded'
    }
});
</script>
@endif
@if(session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Access Denied',
    text: @json(session('error')),
    confirmButtonText: 'Okay',
    confirmButtonColor: '#e74c3c',
    background: '#fff',
    color: '#2c3e50',
    iconColor: '#e74c3c',
    customClass: {
        popup: 'swal-rounded'
    }
});
</script>
@endif
</body>

</html>
