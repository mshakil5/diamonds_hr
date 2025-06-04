@extends('layouts.frontend')

@section('content')
<section class="pricing section light-background">
    <div class="container">
        <div class="row gy-4 justify-content-center">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="pricing-item recommended">
                    <h3 class="text-center">Sign In</h3>

                    @if (isset($message))
                        <span class="login-head" role="alert">
                            <strong>
                                <p class="text-danger">{{ $message }}</p>
                            </strong>
                        </span>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group mt-5">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Your Email" required autocomplete="email" autofocus>
                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Your Password" required autocomplete="current-password">
                            @error('password')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit"  class="btn bg-gradient-dark w-100">Sign In</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection