@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="subscription-profile">
        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="col s12 m3">
                        <div class="card">
                            <div class="card-content blue darken-3 white-text center-align">
                                <h4 class="card-stats-number">@{{ business.plan.data.name.title_case() }}</h4>
                                <p class="card-stats-compare">Service Plan</p>
                            </div>
                            <div class="card-action blue darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m3">
                        <div class="card">
                            <div class="card-content blue darken-3 white-text center-align">
                                <h4 class="card-stats-number">@{{ moment(business.access_expires_at, 'DD MMM, YY') }}</h4>
                                <p class="card-stats-compare">Service Plan Expiry</p>
                            </div>
                            <div class="card-action blue darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m3">
                        <div class="card">
                            <div class="card-content blue darken-3 white-text center-align">
                                <h4 class="card-stats-number">NGN@{{ pricing.formatted }}</h4>
                                <p class="card-stats-compare">Subscription</p>
                            </div>
                            <div class="card-action blue darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m3">
                        <div class="card">
                            <div class="card-content blue darken-3 white-text center-align">
                                <h4 class="card-stats-number">@{{ nextAutoRenew.format('DD MMM, YY') }}</h4>
                                <p class="card-stats-compare">Next Auto-Renew Date</p>
                            </div>
                            <div class="card-action blue darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <section >
                            <div class="col s12 m6">
                                <h4>Redeem Coupon</h4>
                                <form class="col s12" method="post" action="">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="input-field col s12 m6">
                                            <input name="coupon" id="coupon" type="text" maxlength="30" class="validate">
                                            <label for="coupon">Coupon Code</label>
                                        </div>
                                        <div class="input-field col s12 m6">
                                            <button type="submit" name="redeem_coupon" value="1"
                                                    class="waves-effect waves-light btn">Redeem Upgrade Coupon</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="row">
                    <section class="plans-container" id="plans">
                        <plan-chooser v-for="(plan, index) in plans" class="m4" :key="plan.profile.id"
                                      :index="index"
                                      :footnote="plan.footnote"
                                      :name="plan.name"
                                      :features="plan.features"
                                      :short_description="plan.description.short"
                                      :description="plan.description.long"
                                      :profile="plan.profile"></plan-chooser>
                    </section>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#subscription-profile',
            data: {
                user: {!! json_encode($dorcasUser) !!},
                business: {!! json_encode($business) !!},
                plans: {!! json_encode($plans) !!}
            },
            computed: {
                pricing: function () {
                    if (this.business.plan_type === 'yearly') {
                        return this.business.plan.data.price_yearly;
                    }
                    return this.business.plan.data.price_monthly;
                },
                nextAutoRenew: function () {
                    return moment(this.business.access_expires_at).add(1, 'days');
                }
            },
            methods: {
                moment: function (dateString, format) {
                    return moment(dateString).format(format);
                }
            }
        })
    </script>
@endsection