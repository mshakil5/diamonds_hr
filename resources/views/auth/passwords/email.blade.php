@extends('layouts.frontend')

@section('content')
<section class="pricing section light-background">
    <div class="container">
        <div class="row gy-4 justify-content-center">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="pricing-item recommended">
                    <h2 class="text-center">Reset Password</h2>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="text-center">Enter your email address to receive a password reset link.</p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email Address">
                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit"  class="btn bg-gradient-dark w-100">Send Password Reset Link</button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}">Remembered your password? Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection