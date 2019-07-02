@extends('layouts.tabler')
@section('body')
    <div class="page-single">
        <div class="container">

            <div class="row justify-content-center">
                <div class="text-center mb-6">
                    <img src="{{ !empty($domain) && !empty($domain->owner['data']['logo']) ? $domain->owner['data']['logo'] : cdn('images/logo/login-logo_dorcas.png') }}" alt="" class="h-6" style="height: auto !important; width:auto !important; max-width: 250px !important; max-height: 150px !important;">
                </div>
            </div>

            <div class="row justify-content-center" id="respond-invite">

          <div class="col-md-12 col-lg-8">

                @include('layouts.blocks.tabler.alert')
                    <form class="card" action="" method="post" v-if="invite.status === 'pending'">
                        {{ csrf_field() }}
                        <div class="card-body p-6">
                            <div class="card-title">Respond to Invite to {{ !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name') }}</div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <input autocomplete="off" required name="firstname" type="text" maxlength="100"
                                           v-model="invite.firstname"
                                           class="form-control {{ $errors->has('firstname') ? ' is-invalid' : '' }}" id="firstname">
                                    <label for="firstname" class="form-label center-align">Firstname</label>
                                    @if ($errors->has('firstname'))
                                        <div class="invalid-feedback">{{ $errors->first('firstname') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <input autocomplete="off" required name="lastname" type="text" maxlength="30"
                                           v-model="invite.lastname"
                                           class="form-control {{ $errors->has('lastname') ? ' is-invalid' : '' }}" id="lastname">
                                    <label for="lastname" class="form-label center-align">Lastname</label>
                                    @if ($errors->has('lastname'))
                                        <div class="invalid-feedback">{{ $errors->first('lastname') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                <input autocomplete="off" required name="email" type="email" maxlength="80"
                                       v-model="invite.email"
                                       class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="email">
                                <label for="email" class="form-label center-align">Email</label>
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <input autocomplete="off" required name="password" type="password"
                                           class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="password">
                                    <label class="form-label" for="password">Password</label>
                                    @if ($errors->has('password'))
                                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <input autocomplete="off" required name="phone" type="text" maxlength="30" value="{{ old('phone') }}"
                                           class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}" id="phone">
                                    <label for="phone" class="form-label center-align">Phone</label>
                                    @if ($errors->has('phone'))
                                        <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <input autocomplete="off" name="business" type="text" maxlength="100"
                                           v-model="invite.config_data.business"
                                           class="form-control {{ $errors->has('business') ? ' is-invalid' : '' }}" id="business" autofocus>
                                    <label for="company" class="form-label center-align">Business Name</label>
                                    @if ($errors->has('business'))
                                        <div class="invalid-feedback">{{ $errors->first('business') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <button class="btn btn-success btn-block" type="submit">Accept Invite</button>
                                </div>
                                <div class="form-group col-md-6">
                                    <a class="btn btn-danger btn-block" href="{{ route('invite', [$invite->id]) }}?reject_invite=1">Reject Invite</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="col s12" v-if="invite.status !== 'pending'">
                        @component('layouts.blocks.tabler.empty-card')
                            @slot('buttons')
                            @endslot
                            Invite @{{ invite.status }}
                        @endcomponent
                    </div>

                <div class="text-center text-muted">
                    <!-- Already have an account? <a href="{{ route('login') }}">Sign In</a> -->
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
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#respond-invite',
            data: {
                invite: {!! json_encode($invite) !!}
            }
        });
    </script>
@endsection
