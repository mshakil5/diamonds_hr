@extends('layouts.frontend')

@section('content')
<section class="pricing section light-background mt-5">
    <div class="container mt-5">
        <div class="row gy-4 justify-content-center">
            <div class="col-lg-5 col-md-7" data-aos="fade-up" data-aos-delay="200">
                <div class="card shadow mt-5 border">
                    <div class="card-header d-flex justify-content-between" style="background-color:rgb(14, 17, 20); color: #ffffff;">
                        <small>{{ \Carbon\Carbon::now()->format('l, d F Y') }}</small>
                        <small id="currentTime">Time: --:--:--</small>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center">Staff Login</h3>

                        @if (isset($message))
                            <p class="text-danger text-center mt-3"><strong>{{ $message }}</strong></p>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group mt-4">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" placeholder="Email" required autofocus>
                                @error('email')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group mt-3">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" placeholder="Password" required>
                                @error('password')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                              <div class="d-flex justify-content-center mt-4">
                                  <button type="submit" class="btn bg-gradient-dark">Log In</button>
                              </div>
                        </form>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <button type="button" onclick="showLogoutBox()" class="btn btn-outline-danger btn-sm w-100">
                                Logout Here
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="logoutBackdrop" class="d-none" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(4px);
    background-color: rgba(0,0,0,0.2);
    z-index: 9998;
"></div>

<div id="logoutBox" class="d-none d-flex justify-content-center" style="
    position: fixed;
    top: 50px;
    left: 0;
    width: 100%;
    z-index: 9999;
">
    <div class="bg-white border p-4 shadow-lg rounded col-10 col-md-8 col-lg-6">
        <h5 class="text-center mb-4" style="font-weight: bold; color: #333;">Employee Log Details & Activities</h5>

      <form id="logoutForm">
          @csrf
          <div class="form-group mb-3">
              <label for="logoutDetails">Today's Activities:</label>
              <textarea name="details" id="logoutDetails" class="form-control" rows="5" required></textarea>
              <small class="text-danger d-none" id="logoutError">Please enter your activity.</small>
          </div>
          <div class="row mb-2">
              <div class="col-6">
                  <label for="email">Email:</label>
                  <input type="email" name="email" id="email" class="form-control" required>
              </div>
              <div class="col-6">
                  <label for="password">Password:</label>
                  <input type="password" name="password" id="password" class="form-control" required>
              </div>
          </div>
          <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" onclick="hideLogoutBox()">Close</button>
              <button type="submit" class="btn btn-danger">Confirm & Log Out</button>
          </div>
      </form>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  function showLogoutBox() {
      $('#logoutBox, #logoutBackdrop').removeClass('d-none');
  }

  function hideLogoutBox() {
      $('#logoutBox, #logoutBackdrop').addClass('d-none');
  }

  $('#logoutForm').on('submit', function(e) {
      e.preventDefault();

      const details = $('#logoutDetails').val().trim();
      const email = $('#email').val().trim();
      const password = $('#password').val().trim();
      const $error = $('#logoutError');

      if (!details) {
          $error.removeClass('d-none');
          return;
      } else {
          $error.addClass('d-none');
      }
    $.ajax({
      url: '{{ route("logout.with.activity") }}',
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      data: {
        details: details,
        email: email,
        password: password
      },
      success: function(data) {
        alert(data.message);
        window.location.href = '{{ route("login") }}';
      },
      error: function(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          const errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
          alert(errorMessage);
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          alert(xhr.responseJSON.message);
        } else {
          alert('An unknown error occurred');
        }
      }
    });
  });
</script>

<script>
    function updateTime() {
        const now = new Date();
        const formatted = now.toLocaleTimeString('en-GB', { hour12: false });
        document.getElementById('currentTime').textContent = 'Time: ' + formatted;
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>

@endsection