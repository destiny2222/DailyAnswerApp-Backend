<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Favicon icon-->
  <link rel="shortcut icon" type="image/png" href="/favicon.png">

  <!-- Core Css -->
  <link rel="stylesheet" href="/assets/css/styles.css">

  <title>Daily Answer</title>
  <!-- Owl Carousel  -->
  <link rel="stylesheet" href="/assets/libs/owl.carousel/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css"/>
  <style>
    .sidebar-link.active{
      background-color: #5d87ff !important;
      color: #ffffff !important;
    }
  </style>
  <style>
    .is-invalid,  .is-invalid:focus ,  .invalid-feedback{
        border-color: #dc3545 !important;
        color: #dc3545 !important;
    }
</style>
</head>

<body>
  
  <!-- Preloader -->
  <div class="preloader">
    <img src="/icon.png" alt="loader" class="lds-ripple img-fluid">
  </div>
  <div id="main-wrapper">
    <!-- Sidebar Start -->
    @include('layouts.sidebar')
    <!--  Sidebar End -->
    <div class="page-wrapper">
      <!--  Header Start -->
      @include('layouts.topbar')
      <!--  Header End -->
      {{-- @include('layouts.aside') --}}
      <div class="body-wrapper">
        <div class="container-fluid">
          @yield('content')
        </div>
      </div>
    </div>
  </div>
  <div class="dark-transparent sidebartoggler"></div>
  <script src="/assets/js/vendor.min.js"></script>
  <!-- Import Js Files -->
  <script src="/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/libs/simplebar/dist/simplebar.min.js"></script>
  <script src="/assets/js/theme/app.init.js"></script>
  <script src="/assets/js/theme/theme.js"></script>
  <script src="/assets/js/theme/app.min.js"></script>
  <script src="/assets/js/theme/sidebarmenu.js"></script>

  <!-- solar icons -->
  <script src="../../../npm/iconify-icon%401.0.8/dist/iconify-icon.min.js"></script>
  <script src="/assets/libs/owl.carousel/dist/owl.carousel.min.js"></script>
  <script src="/assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="/assets/js/dashboards/dashboard.js"></script>
  {{-- <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script> --}}
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
  @stack('scripts')
  @stack('styles')
  @include('partials.message')
</body>
</html>