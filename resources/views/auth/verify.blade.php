@extends('layouts.frontend')

@section('content')
<section class="pricing section light-background">
    <div class="container">
        <div class="row gy-4 justify-content-center">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="pricing-item recommended">
                    <h2>Verify Your Email Address</h2>

                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
                    <p>{{ __('If you did not receive the email') }},</p>

                    <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                        @csrf
                        <div class="text-center mt-4">
                            <button type="submit" class="btn-buy">{{ __('Click here to request another') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection