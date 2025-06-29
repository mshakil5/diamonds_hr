<!DOCTYPE html>
<html lang="en">
@php
    use App\Models\CompanyDetails;
    $company = CompanyDetails::select('company_name', 'fav_icon')->first();
@endphp


<head>

  <meta charset="utf-8" />
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/company/' . $company->fav_icon) }}">
  <!-- Favicons -->
  <link href="{{ asset('images/company/' . $company->fav_icon) }}" rel="icon">

  <title>{{ $company->company_name }}</title>

  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />

  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />

  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

  <!-- CSS Files -->

  <link id="pagestyle" href="{{ asset('frontend/assets/css/soft-design-system.css') }}" rel="stylesheet" />
  <style>
    .btn-container-right {
      direction: rtl;
    }
  </style>

</head>

<body class="index-page">
  
   <!-- Navbar -->
   <div class="container position-sticky z-index-sticky top-0 mb-5 d-none">
      <div class="row">
        <div class="col-12">
          <nav class="navbar navbar-expand-lg  blur blur-rounded top-0 z-index-fixed shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
          <div class="container-fluid px-0">
            <a class="navbar-brand font-weight-bolder ms-sm-3" href="{{route('homepage')}}" rel="tooltip">
              {{ $company->company_name }}
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </span>
            </button>
            <div class="collapse navbar-collapse pt-3 pb-2 py-lg-0 w-100" id="navigation">
              <ul class="navbar-nav navbar-nav-hover ms-lg-auto ps-lg-auto btn-container-right w-100">
                
                <!-- <li class="nav-item my-auto ms-3 ms-lg-0">
                  <a href="https://www.creative-tim.com/builder?ref=navbar-soft-design-system" class="btn btn-sm btn-outline-dark btn-round mb-0 me-1 mt-2 mt-md-0">Online Builder</a>
                </li> -->
                <li class="nav-item my-auto ms-3 ms-lg-0">
                  @if (Auth::user())
                  <a href="{{route('home')}}" class="btn btn-sm text-white  bg-dark  btn-round mb-0 me-1 mt-2 mt-md-0">Dashboard</a>
                  @else
                  <a href="{{route('login')}}" class="btn btn-sm text-white  bg-dark  btn-round mb-0 me-1 mt-2 mt-md-0">Login</a>
                  @endif
                  
                </li> 
              </ul>
            </div>
          </div>
          </nav>
          <!-- End Navbar -->
        </div>
      </div>
    </div>
    




<header class="header-2  d-none">
  <div class="page-header min-vh-100 relative" style="background-image: url('./frontend/assets/img/banner.jpg')">

    <div class="position-absolute w-100 z-index-1 bottom-0">
      <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 40" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
          <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
        </defs>
        <g class="moving-waves">
          <use xlink:href="#gentle-wave" x="48" y="-1" fill="rgba(255,255,255,0.40" />
          <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.35)" />
          <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.25)" />
          <use xlink:href="#gentle-wave" x="48" y="8" fill="rgba(255,255,255,0.20)" />
          <use xlink:href="#gentle-wave" x="48" y="13" fill="rgba(255,255,255,0.15)" />
          <use xlink:href="#gentle-wave" x="48" y="16" fill="rgba(255,255,255,0.95" />
        </g>
      </svg>
    </div>
  </div>
</header>


<section class="pt-3 pb-4  d-none" id="count-stats">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 z-index-2 border-radius-xl mt-n10 mx-auto py-3 blur shadow-blur">
        <div class="row">
          <div class="col-md-6 position-relative">
            <div class="p-3 text-center">
              <h1 class="text-gradient text-dark"><span id="state1" countTo="140000">0</span>+</h1>
              <h5 class="mt-3">GOAL</h5>
              
            </div>
            <hr class="vertical dark">
          </div>
          <div class="col-md-6">
            <div class="p-3 text-center">
              <h1 class="text-gradient text-dark" id="state3" countTo="14">0</h1>
              <h5 class="mt-3">Members</h5>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<section class="py-sm-2  d-none" id="download-soft-ui">
  <div class="bg-gradient-dark position-relative m-3 border-radius-xl overflow-hidden">
    <img src="./frontend/assets/img/shapes/waves-white.svg" alt="pattern-lines" class="position-absolute start-0 top-md-0 w-100 opacity-6">
    <div class="container py-7 postion-relative z-index-2 position-relative">
      <div class="row">
        <div class="col-md-7 mx-auto text-center">
          <h3 class="text-white mb-0">Do you love this awesome</h3>
          
        </div>
      </div>
    </div>
  </div>
</section>


@yield('content')

<footer class="footer pt-1 mt-3 d-none">
  <hr class="horizontal dark mb-5">
  <div class="container">
    <div class=" row">
      <div class="col-md-3 mb-4 ms-auto d-none">
        <div>
          <h6 class="text-gradient text-primary font-weight-bolder">Soft</h6>
        </div>
        <div>
          
          <ul class="d-flex flex-row ms-n3 nav">
            <li class="nav-item">
              <a class="nav-link pe-1" href="#" target="_blank">
                <i class="fab fa-facebook text-lg opacity-8"></i>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link pe-1" href="#" target="_blank">
                <i class="fab fa-twitter text-lg opacity-8"></i>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link pe-1" href="#" target="_blank">
                <i class="fab fa-dribbble text-lg opacity-8"></i>
              </a>
            </li>


            <li class="nav-item">
              <a class="nav-link pe-1" href="#" target="_blank">
                <i class="fab fa-github text-lg opacity-8"></i>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link pe-1" href="#" target="_blank">
                <i class="fab fa-youtube text-lg opacity-8"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>



      <div class="col-md-2 col-sm-6 col-6 mb-4 d-none">
        <div>
          <h6 class="text-gradient text-primary text-sm">Company</h6>
          <ul class="flex-column ms-n3 nav">
            <li class="nav-item">
              <a class="nav-link" href="#" target="_blank">
                About Us
              </a>
            </li>

          </ul>
        </div>
      </div>

      <div class="col-md-2 col-sm-6 col-6 mb-4  d-none">
        <div>
          <h6 class="text-gradient text-primary text-sm">Resources</h6>
          <ul class="flex-column ms-n3 nav">
            <li class="nav-item">
              <a class="nav-link" href="#" target="_blank">
                Illustrations
              </a>
            </li>
            
          </ul>
        </div>
      </div>

      <div class="col-md-2 col-sm-6 col-6 mb-4  d-none">
        <div>
          <h6 class="text-gradient text-primary text-sm">Help & Support</h6>
          <ul class="flex-column ms-n3 nav">
            <li class="nav-item">
              <a class="nav-link" href="#" target="_blank">
                Contact Us
              </a>
            </li>

            

          </ul>
        </div>
      </div>

      <div class="col-md-2 col-sm-6 col-6 mb-4 me-auto  d-none">
        <div>
          <h6 class="text-gradient text-primary text-sm">Legal</h6>
          <ul class="flex-column ms-n3 nav">
            <li class="nav-item">
              <a class="nav-link" href="#" target="_blank">
                Terms &amp; Conditions
              </a>
            </li>
            
          </ul>
        </div>
      </div>

      <div class="col-12">
        <div class="text-center">
          <p class="my-4 text-sm">
            All rights reserved. Copyright © <script>document.write(new Date().getFullYear())</script>.
          </p>
        </div>
      </div>
    </div>
  </div>
</footer>

<!--   Core JS Files   -->
<script src="{{ asset('frontend/assets/js/core/popper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('frontend/assets/js/core/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('frontend/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>

<!--  Plugin for TypedJS, full documentation here: https://github.com/inorganik/CountUp.js -->
<script src="{{ asset('frontend/assets/js/plugins/countup.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/plugins/choices.min.js') }}"></script>

<script src="{{ asset('frontend/assets/js/plugins/prism.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/plugins/highlight.min.js') }}"></script>

<!--  Plugin for Parallax, full documentation here: https://github.com/dixonandmoe/rellax -->
<script src="{{ asset('frontend/assets/js/plugins/rellax.min.js') }}"></script>
<!--  Plugin for TiltJS, full documentation here: https://gijsroge.github.io/tilt.js/ -->
<script src="{{ asset('frontend/assets/js/plugins/tilt.min.js') }}"></script>


<!--  Plugin for Parallax, full documentation here: https://github.com/wagerfield/parallax  -->
<script src="{{ asset('frontend/assets/js/plugins/parallax.min.js') }}"></script>

<!-- Control Center for Soft UI Kit: parallax effects, scripts for the example pages etc -->
<!--  Google Maps Plugin    -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTTfWur0PDbZWPr7Pmq8K3jiDp0_xUziI"></script>
<script src="{{ asset('frontend/assets/js/soft-design-system.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

  if (document.getElementById('state1')) {
    const countUp = new CountUp('state1', document.getElementById("state1").getAttribute("countTo"));
    if (!countUp.error) {
      countUp.start();
    } else {
      console.error(countUp.error);
    }
  }
  if (document.getElementById('state2')) {
    const countUp1 = new CountUp('state2', document.getElementById("state2").getAttribute("countTo"));
    if (!countUp1.error) {
      countUp1.start();
    } else {
      console.error(countUp1.error);
    }
  }
  if (document.getElementById('state3')) {
    const countUp2 = new CountUp('state3', document.getElementById("state3").getAttribute("countTo"));
    if (!countUp2.error) {
      countUp2.start();
    } else {
      console.error(countUp2.error);
    };
  }
</script>

</body>

</html>
