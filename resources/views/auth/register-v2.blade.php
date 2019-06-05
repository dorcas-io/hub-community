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

          <div class="col-md-12 col-lg-8">

                @include('layouts.blocks.tabler.alert')
                    <form class="card" action="{{ route('register') }}" method="post">
                        {{ csrf_field() }}
                        <div class="card-body p-6">
                            <div class="card-title">Get Started with {{ !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name') }}</div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Business Name</label>
                                    <input type="text" class="form-control {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company" id="company" value="{{ old('company') }}"
                                           placeholder="Business Name" maxlength="30" required>
                                    @if ($errors->has('company'))
                                        <div class="invalid-feedback">{{ $errors->first('company') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Mobile Phone</label>
                                    <input type="text" class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" name="phone" id="phone"
                                           placeholder="Mobile Phone" maxlength="30" value="{{ old('phone') }}" required>
                                    @if ($errors->has('phone'))
                                        <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                           id="email" name="email" aria-describedby="emailHelp" placeholder="Enter Email"
                                           maxlength="80" value="{{ old('email') }}" required>
                                    @if ($errors->has('email'))
                                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">
                                        Password
                                    </label>
                                    <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                           id="password" name="password" placeholder="Password" minlength="6" required>
                                    @if ($errors->has('password'))
                                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control {{ $errors->has('firstname') ? 'is-invalid' : '' }}" name="firstname" id="firstname" value="{{ old('firstname') }}"
                                           placeholder="First Name" maxlength="30" required>
                                    @if ($errors->has('firstname'))
                                        <div class="invalid-feedback">{{ $errors->first('firstname') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control {{ $errors->has('lastname') ? 'is-invalid' : '' }}" name="lastname" id="lastname"
                                           placeholder="Last Name" maxlength="30" value="{{ old('lastname') }}" required>
                                    @if ($errors->has('lastname'))
                                        <div class="invalid-feedback">{{ $errors->first('lastname') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-footer">
                                <input type="hidden" name="plan" value="{{ $plan }}" />
                                <input type="hidden" name="plan_type" value="{{ $plan_type }}" />
                                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                            </div>
                        </div>
                    </form>
                <div class="text-center text-muted">
                    Already have an account? <a href="{{ route('login') }}">Sign In</a>
                </div>
                </div>


                <div class="col-md-12 col-lg-4">
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
