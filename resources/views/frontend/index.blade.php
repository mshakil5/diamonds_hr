@extends('layouts.frontend')

@section('content')

@guest

<section>
   <div class="container py-4">
     <div class="row">
       <div class="col-lg-7 mx-auto d-flex justify-content-center flex-column">
         <h3 class="text-center">Registration</h3>
 
         @if (isset($message))
             <span class="login-head" role="alert">
                 <strong>
                     <p class="text-danger">{{ $message }}</p>
                 </strong>
             </span>
         @endif
 
 
         <form  method="POST" action="{{ route('register') }}" autocomplete="off">
             @csrf
           <div class="card-body">
             <div class="row">
               <div class="col-md-12">
                 <label>First Name</label>
                 <div class="input-group mb-4">
                   <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Full Name">
                     @error('name')
                         <p class="text-danger">{{ $message }}</p>
                     @enderror
 
                 </div>
               </div>
             </div>
             <div class="mb-4">
               <label>Email Address</label>
               <div class="input-group">
                 <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
               </div>
                 @error('email')
                     <p class="text-danger">{{ $message }}</p>
                 @enderror
             </div>
             <div class="row">
               <div class="col-md-6">
                 <label>Password</label>
                 <div class="input-group mb-4">
                     <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">
                 </div>
                 @error('password')
                     <p class="text-danger">{{ $message }}</p>
                 @enderror
               </div>
               <div class="col-md-6 ps-2">
                 <label>Confirm Password</label>
                 <div class="input-group">
                     <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                 </div>
               </div>
             </div>
             <div class="row">
               <div class="col-md-12">
                 <button type="submit" class="btn bg-gradient-dark w-100">Register</button>
               </div>
             </div>
           </div>
         </form>
       </div>
     </div>
   </div>
 </section>

 @endguest

@endsection