@extends('user.layouts.user')
  
@section('content')


  
<div class="container-fluid py-4 d-none">
    <div class="row">
      
      <div class="col-12 col-xl-4">
        <div class="card h-100">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-md-8 d-flex align-items-center">
                <h6 class="mb-0">Profile Information</h6>
              </div>
              <div class="col-md-4 text-end">
                <a href="javascript:;">
                  <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Profile"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="card-body p-3">
            
            <div class="position-relative w-100">
              @if (Auth::user()->profileimage)
              <img src="{{ asset( Auth::user()->profileimage) }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
              @else
              <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
              @endif

            </div>

            <hr class="horizontal gray-light my-4">
            <ul class="list-group">
              <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; {{ Auth::user()->name }}</li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Mobile:</strong> &nbsp; {{ Auth::user()->phone }}</li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; {{ Auth::user()->email }}</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-8">
        <div class="card h-100">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-md-8 d-flex align-items-center">
                <h6 class="mb-0">Profile Information</h6>
              </div>
              <div class="col-md-4 text-end">
                <a href="javascript:;">
                  <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Profile"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="card-body p-3">

            <hr class="horizontal gray-light ">
            
            @if(session()->has('success'))
              <section class="px-4">
                  <div class="row">
                      <div class="alert alert-success text-light" id="successMessage">{{ session()->get('success') }}</div>
                  </div>
              </section>
              @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class=" text-light">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
    
    
            <form  method="POST" action="{{ route('user.profileUpdate') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                  <div class="col-md-6">
                    <label>First Name</label>
                    <div class="input-group mb-4">
                      <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ Auth::user()->name }}" required autocomplete="name" autofocus placeholder="Full Name">
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label>Phone</label>
                    <div class="input-group mb-4">
                      <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ Auth::user()->phone }}" required autocomplete="phone" autofocus placeholder="Phone">
                        @error('phone')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label>Profile Image</label>
                    <div class="input-group mb-4">
                      <input id="profileimage" type="file" class="form-control" name="profileimage">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label>Cover Image</label>
                    <div class="input-group mb-4">
                      <input id="coverimage" type="file" class="form-control" name="coverimage">
                    </div>
                  </div>

                </div>
                <div class="mb-4">
                  <label>Email Address</label>
                  <div class="input-group">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ Auth::user()->email }}" required autocomplete="email" placeholder="Email">
                  </div>
                    @error('email')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label>Password</label>
                    <div class="input-group mb-4">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" placeholder="Password">
                    </div>
                    @error('password')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="col-md-6 ps-2">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" placeholder="Confirm Password">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <button type="submit" class="btn bg-gradient-dark w-100">Update</button>
                  </div>
                </div>
                
            </form>
          </div>
        </div>
      </div>
      
      <div class="col-12 mt-4">
        <div class="card mb-4">
          <div class="card-header pb-0 p-3">
            <h6 class="mb-1">Projects</h6>
          </div>
          <div class="card-body p-3">
            <div class="row">
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="position-relative">
                    <a class="d-block shadow-xl border-radius-xl">
                      <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="img-blur-shadow" class="img-fluid shadow border-radius-xl">
                    </a>
                  </div>
                  <div class="card-body px-1 pb-0">
                    <p class="text-gradient text-dark mb-2 text-sm">Project #2</p>
                    <a href="javascript:;">
                      <h5>
                        Modern
                      </h5>
                    </a>
                    <p class="mb-4 text-sm">
                      As Uber works through a huge amount of internal management turmoil.
                    </p>
                    <div class="d-flex align-items-center justify-content-between">
                      <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="position-relative">
                    <a class="d-block shadow-xl border-radius-xl">
                      <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                    </a>
                  </div>
                  <div class="card-body px-1 pb-0">
                    <p class="text-gradient text-dark mb-2 text-sm">Project #1</p>
                    <a href="javascript:;">
                      <h5>
                        Scandinavian
                      </h5>
                    </a>
                    <p class="mb-4 text-sm">
                      Music is something that every person has his or her own specific opinion about.
                    </p>

                    
                    <div class="d-flex align-items-center justify-content-between">
                      <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button>
                    </div>
                    
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="position-relative">
                    <a class="d-block shadow-xl border-radius-xl">
                      <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="img-blur-shadow" class="img-fluid shadow border-radius-xl">
                    </a>
                  </div>
                  <div class="card-body px-1 pb-0">
                    <p class="text-gradient text-dark mb-2 text-sm">Project #3</p>
                    <a href="javascript:;">
                      <h5>
                        Minimalist
                      </h5>
                    </a>
                    <p class="mb-4 text-sm">
                      Different people have different taste, and various types of music.
                    </p>
                    <div class="d-flex align-items-center justify-content-between">
                      <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card h-100 card-plain border">
                  <div class="card-body d-flex flex-column justify-content-center text-center">
                    <a href="javascript:;">
                      <i class="fa fa-plus text-secondary mb-3"></i>
                      <h5 class=" text-secondary"> New project </h5>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      
      <div class="col-md-7 mt-4">
        <div class="card">
          <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Billing Information</h6>
          </div>
          <div class="card-body pt-4 p-3">
            <ul class="list-group">
              <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="mb-3 text-sm">Oliver Liam</h6>
                  <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Viking Burrito</span></span>
                  <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">oliver@burrito.com</span></span>
                  <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                </div>
                <div class="ms-auto text-end">
                  <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                  <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      
      <div class="col-md-5 mt-4">
        <div class="card h-100 mb-4">
          <div class="card-header pb-0 px-3">
            <div class="row">
              <div class="col-md-6">
                <h6 class="mb-0">Your Transaction's</h6>
              </div>
              <div class="col-md-6 d-flex justify-content-end align-items-center">
                <i class="far fa-calendar-alt me-2"></i>
                <small>23 - 30 March 2020</small>
              </div>
            </div>
          </div>
          <div class="card-body pt-4 p-3">
            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
            <ul class="list-group">
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-down"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Netflix</h6>
                    <span class="text-xs">27 March 2020, at 12:30 PM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                  - $ 2,500
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Apple</h6>
                    <span class="text-xs">27 March 2020, at 04:30 AM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                  + $ 2,000
                </div>
              </li>
            </ul>
            
          </div>
        </div>
      </div>


    </div>
    <footer class="footer pt-3  ">
      <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
          <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="copyright text-center text-sm text-muted text-lg-start">
              © <script>
                document.write(new Date().getFullYear())
              </script>,
              made with <i class="fa fa-heart"></i> by
              <a href="#" class="font-weight-bold" target="_blank">Shakil</a>
              for a better web.
            </div>
          </div>
          
        </div>
      </div>
    </footer>
</div>

<div class="container-fluid py-4">
    <div class="row">
      <div class="col-12 col-xl-4 d-none">
        <div class="card h-100">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-md-8 d-flex align-items-center">
                <h6 class="mb-0">Profile Information</h6>
              </div>
              <div class="col-md-4 text-end">
                <a href="javascript:;">
                  <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Profile"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="card-body p-3">
            
            <div class="position-relative w-100">
              @if (Auth::user()->profileimage)
              <img src="{{ asset( Auth::user()->profileimage) }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
              @else
              <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
              @endif

            </div>

            <hr class="horizontal gray-light my-4">
            <ul class="list-group">
              <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; {{ Auth::user()->name }}</li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Mobile:</strong> &nbsp; {{ Auth::user()->phone }}</li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; {{ Auth::user()->email }}</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-12 d-none" id="logoutBox">
        <div class="card h-100">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-md-8 d-flex align-items-center">
                <h6 class="mb-0">Log Out Activity</h6>
              </div>
              <div class="col-md-4 text-end">
                <a href="javascript:;">
                  <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Log your activity before logout"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="card-body p-3">
            <form method="POST" action="{{ route('clearSession') }}">
              @csrf

              <div class="mb-3">
                <label for="logoutDetails" class="form-label fw-bold">What have you done today?</label>
                <textarea name="details" id="logoutDetails" class="form-control" rows="4" required></textarea>
                <small id="logoutError" class="text-danger d-none">Please enter your activity.</small>
              </div>

              <div class="row justify-content-center">
                <div class="col-md-6">
                  <button type="submit" class="btn bg-gradient-dark w-100">Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-12 mt-4 d-none">
        <div class="card mb-4">
          <div class="card-header pb-0 p-3">
            <h6 class="mb-1">Projects</h6>
          </div>
          <div class="card-body p-3">
            <div class="row">
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="position-relative">
                    <a class="d-block shadow-xl border-radius-xl">
                      <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="img-blur-shadow" class="img-fluid shadow border-radius-xl">
                    </a>
                  </div>
                  <div class="card-body px-1 pb-0">
                    <p class="text-gradient text-dark mb-2 text-sm">Project #2</p>
                    <a href="javascript:;">
                      <h5>
                        Modern
                      </h5>
                    </a>
                    <p class="mb-4 text-sm">
                      As Uber works through a huge amount of internal management turmoil.
                    </p>
                    <div class="d-flex align-items-center justify-content-between">
                      <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="position-relative">
                    <a class="d-block shadow-xl border-radius-xl">
                      <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                    </a>
                  </div>
                  <div class="card-body px-1 pb-0">
                    <p class="text-gradient text-dark mb-2 text-sm">Project #1</p>
                    <a href="javascript:;">
                      <h5>
                        Scandinavian
                      </h5>
                    </a>
                    <p class="mb-4 text-sm">
                      Music is something that every person has his or her own specific opinion about.
                    </p>

                    
                    <div class="d-flex align-items-center justify-content-between">
                      <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button>
                    </div>
                    
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="position-relative">
                    <a class="d-block shadow-xl border-radius-xl">
                      <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="img-blur-shadow" class="img-fluid shadow border-radius-xl">
                    </a>
                  </div>
                  <div class="card-body px-1 pb-0">
                    <p class="text-gradient text-dark mb-2 text-sm">Project #3</p>
                    <a href="javascript:;">
                      <h5>
                        Minimalist
                      </h5>
                    </a>
                    <p class="mb-4 text-sm">
                      Different people have different taste, and various types of music.
                    </p>
                    <div class="d-flex align-items-center justify-content-between">
                      <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card h-100 card-plain border">
                  <div class="card-body d-flex flex-column justify-content-center text-center">
                    <a href="javascript:;">
                      <i class="fa fa-plus text-secondary mb-3"></i>
                      <h5 class=" text-secondary"> New project </h5>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-7 mt-4 d-none">
        <div class="card">
          <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Billing Information</h6>
          </div>
          <div class="card-body pt-4 p-3">
            <ul class="list-group">
              <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="mb-3 text-sm">Oliver Liam</h6>
                  <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Viking Burrito</span></span>
                  <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">oliver@burrito.com</span></span>
                  <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                </div>
                <div class="ms-auto text-end">
                  <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                  <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-5 mt-4 d-none">
        <div class="card h-100 mb-4">
          <div class="card-header pb-0 px-3">
            <div class="row">
              <div class="col-md-6">
                <h6 class="mb-0">Your Transaction's</h6>
              </div>
              <div class="col-md-6 d-flex justify-content-end align-items-center">
                <i class="far fa-calendar-alt me-2"></i>
                <small>23 - 30 March 2020</small>
              </div>
            </div>
          </div>
          <div class="card-body pt-4 p-3">
            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
            <ul class="list-group">
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-down"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Netflix</h6>
                    <span class="text-xs">27 March 2020, at 12:30 PM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                  - $ 2,500
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Apple</h6>
                    <span class="text-xs">27 March 2020, at 04:30 AM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                  + $ 2,000
                </div>
              </li>
            </ul>
            
          </div>
        </div>
      </div>
    </div>
</div>


@endsection