@extends('layouts.tabler')
@section('body')
    <div class="page-single">
        <div class="container">

            <div class="row justify-content-center">
                <div class="text-center mb-6">
                    <!-- <img src="{{ cdn('/images/dorcas.jpeg') }}" class="h-6" alt=""> -->
                    <img src="{{ !empty($appUiSettings['product_logo']) ? $appUiSettings['product_logo'] : cdn('images/logo/login-logo_dorcas.png') }}" alt="" class="h-6" style="height: auto !important; width:auto !important; max-width: 250px !important; max-height: 150px !important;">
                </div>
            </div>

            <div class="row justify-content-center">

          <div class="col-md-6 col-lg-4">

                @include('layouts.blocks.tabler.alert')
                <form class="card" action="{{ url('/reset-password') }}/{{ $token }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="card-body p-6">
                        <div class="card-title text-center">
                            PASSWORD RESET
                        </div>
                        <p class="text-muted text-center"><strong>Welcome back!</strong> Enter your new password and confirm to complete the reset.</p>
                        <div class="form-group">
                            <label class="form-label">Email address</label>
                            <input type="email" autocomplete="off" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                   id="email" name="email" value="{{ $email or old('email') }}" autofocus placeholder="Enter email" required>
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Enter Password" required>
                            @if ($errors->has('password'))
                                <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                   placeholder="ConfirmPassword" required>
                            @if ($errors->has('password_confirmation'))
                                <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                            @endif
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                        </div>
                    </div>
                </form>
                <div class="text-center text-muted">
                    Want to login instead? <a href="{{ route('login') }}">Login</a>
                </div>
                </div>


                <div class="col-md-6 col-lg-8">
                    <div class="card p-3">
                      <a href="javascript:void(0)" class="mb-3">
                        <img src="{{ cdn('/images/background/all_in_one_3.png') }}" alt="Dorcas Hub" class="rounded">
                    </a>
                    <div class="d-flex align-items-center px-2">
                        
                        <div>
                          <div>All-In-One Business Management Software Platform</div>
                          <small class="d-block text-muted">Learn More</small>
                      </div>
                  </div>
              </div>
          </div>
            </div>
        </div>
    </div>
@endsection
