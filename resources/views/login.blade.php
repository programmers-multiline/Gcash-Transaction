@extends('layouts.simple')



@section('content')
{{-- <div class="bg-image" style="background-image: url('{{asset('media/photos/c-site3.jpg')}}');">
  <div class="row mx-0 bg-black-50">
    <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
      <div class="p-4">
        <p class="fs-3 fw-semibold text-white">
          To Serve More Customer, Better, Faster And At Less Cost.
        </p>
        <p class="text-white-75 fw-medium">
          Copyright &copy; <span data-toggle="year-copy"></span>
        </p>
      </div>
    </div>
    <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-body-extra-light">
      <div class="content content-full">
        <div class="px-4 py-2 mb-4">
          <a class="link-fx fw-bold" href="/">
            <img class="mb-3" src="{{asset('media/logo.png')}}" width="300px" alt="multiline logo">
          </a>
          <h1 class="h3 fw-semibold mt-4 mb-2">Welcome to <span class="text-danger">Gcash Portal</span></h1>
          <h2 class="h5 fw-medium text-muted mb-0">Please sign in</h2>
        </div>
        <form class="js-validation-signin px-4" action="{{route('login')}}" method="POST">
          @csrf
          <div class="form-floating mb-4">
            @if ($errors->has('email'))
              <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif
            <input type="text" class="form-control @error('login_error') is-invalid @enderror" id="login_username" name="login_username" placeholder="Enter your username" required value="{{ old('login_username') }}">
            <label class="form-label" for="login-username">Username</label>
          </div>
          <div class="form-floating mb-4">
            @if ($errors->has('password'))
              <span class="text-danger">{{ $errors->first('password') }}</span>
            @endif
            <input type="password" class="form-control" id="login_password" name="login_password" placeholder="Enter your password" required>
            <label class="form-label" for="login-password">Password</label>
          </div>
          <div class="mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="login-remember-me" name="login-remember-me" checked>
              <label class="form-check-label" for="login-remember-me">Remember Me</label>
            </div>
          </div>
          <div class="mb-4">
            <button type="submit" class="btn btn-lg btn-alt-primary fw-semibold">
              Sign In
            </button>
            <div class="mt-4">
              <a class="fs-sm fw-medium link-fx text-muted me-2 mb-1 d-inline-block" href="op_auth_reminder2.html">
                Forgot Password
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div> --}}
<div class="bg-body-dark">
  <div class="hero-static content content-full px-1">
    <div class="row mx-0 justify-content-center">
      <div class="col-lg-8 col-xl-6">
        <div class="py-4 text-center">
          <a class="link-fx fw-bold" href="/">
            <img class="mb-3" src="{{asset('media/everfirst_logo.png')}}" width="250px" alt="multiline logo">
          </a>
          <h4 class="h5 fw-normal text-muted mt-4 mb-1">
            Good to see you again!
          </h4>
          <h1 class="fs-5 lh-base fw-bold text-pulse mb-0">
            Online Approval of Gcash Transactions
          </h1>
        </div>
        <form class="js-validation-signin" action="{{route('login')}}" method="POST" novalidate="novalidate">
          @csrf
          <div class="block block-themed block-rounded block-fx-shadow">
            <div class="block-header bg-gd-sun">
              <h3 class="block-title">Please Sign In</h3>
            </div>
            <div class="block-content">
              <div class="form-floating mb-4">
                <input type="text" class="form-control @error('login_error') is-invalid @enderror" id="login_username" name="login_username" placeholder="Enter your username" required value="{{ old('login_username') }}">
                <label class="form-label" for="login-username">Username</label>
              </div>
              <div class="form-floating mb-4">
                <input type="password" class="form-control" id="login_password" name="login_password" placeholder="Enter your password" required>
                <label class="form-label" for="login-password">Password</label>
              </div>
              <div class="row">
                <div class="col-sm-6 d-sm-flex align-items-center push">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="login-remember-me" name="login-remember-me">
                    <label class="form-check-label" for="login-remember-me">Remember Me</label>
                  </div>
                </div>
                <div class="col-sm-6 text-sm-end push">
                  <button type="submit" class="btn btn-lg btn-alt-danger fw-medium">
                    Sign In
                  </button>
                </div>
              </div>
            </div>
            <div class="block-content block-content-full bg-body-light text-center d-flex justify-content-between">
              <a class="fs-sm fw-medium link-fx text-muted me-2 mb-1 d-inline-block" href="op_auth_reminder3.html">
                Forgot Password
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
