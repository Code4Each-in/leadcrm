<!DOCTYPE html>
<html lang="en">
<style>
    .card {
  backdrop-filter: blur(10px);
}

body {
  background: linear-gradient(135deg, #4e73df, #224abe);
}

.card input:focus {
  box-shadow: none;
  border-color: #4e73df;
}

.btn-primary {
  background: linear-gradient(45deg, #4e73df, #224abe);
  border: none;
}
</style>
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Lead Bridge</title>
  <!-- plugins:css -->

  <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css') }}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
        <div class="col-lg-4 mx-auto">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-body p-5">

            <!-- Logo -->
            <div class="text-center mb-4">
                <img src="{{ asset('assets/images/leadbridge_logo.svg') }}" style="height:60px;">
            </div>

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="form-group mb-3">
                <label>Email Address</label>
                <input type="email" name="email"
                        class="form-control form-control-lg rounded-pill"
                        placeholder="Enter your email">
                </div>

                <!-- Password -->
                <div class="form-group mb-3">
                <label>Password</label>
                <input type="password" name="password"
                        class="form-control form-control-lg rounded-pill"
                        placeholder="Enter your password">
                </div>

                <!-- Error -->
                @if ($errors->has('email'))
                <div class="text-danger mb-2">
                    {{ $errors->first('email') }}
                </div>
                @endif

                <!-- Button -->
                <div class="mt-4">
                <button type="submit"
                    class="btn btn-primary btn-lg btn-block rounded-pill shadow-sm">
                    Sign In
                </button>
                </div>
                <!-- <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input">
                      Keep me signed in
                    </label>
                  </div>
                  <a href="#" class="auth-link text-black">Forgot password?</a>
                </div> -->
                <!-- <div class="mb-2">
                  <button type="button" class="btn btn-block btn-facebook auth-form-btn">
                    <i class="ti-facebook mr-2"></i>Connect using facebook
                  </button>
                </div> -->
                <!-- <div class="text-center mt-4 font-weight-light">
                  Don't have an account? <a href="register.html" class="text-primary">Create</a>
                </div> -->
                <!-- Extra Links -->
                <!-- <div class="d-flex justify-content-between mt-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label text-muted">Remember me</label>
                </div>
                <a href="#" class="text-primary">Forgot password?</a>
                </div> -->

            </form>

            </div>
        </div>
        </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('assets/js/template.js') }}"></script>
  <script src="{{ asset('assets/js/settings.js') }}"></script>
  <script src="{{ asset('assets/js/todolist.js') }}"></script>
  <!-- endinject -->
</body>

</html>
