@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $policyPages = getContent('policy_pages.element', false, null, true);
        $register = getContent('register.content', true);
        $socialLogin = getContent('social_login.content', true);
    @endphp

    <section class="container custom-header flex-between">
        <a class="site-logo" href="{{ route('home') }}">
            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('Logo')">
        </a>
        <p class="account-alt">
            @lang('Already have account?') <a class="text--base border-bottom border--base" href="{{ route('user.login') }}">@lang('Login Now')</a>
        </p>
    </section>

    <section class="account py-80">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-heading text-cener">
                        <h4 class="section-heading__title">{{ __(@$register->data_values->title) }}</h4>
                        <p class="section-heading__desc fs-18">{{ __(@$register->data_values->subtitle) }}</p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-6 section-heading text-cener account-form">
                    <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 col-xsm-6">
                                <div class="form-group">
                                    <input class="form--control checkUser" type="text" name="username" value="{{ old('username') }}" placeholder="@lang('Username')" autocomplete="username">
                                    <small class="text--danger usernameExist"></small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xsm-6">
                                <div class="form-group">
                                    <input class="form--control checkUser" type="email" name="email" placeholder="@lang('E-Mail Address')">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xsm-6">
                                <div class="form-group">
                                    <select name="country" class="form--control">
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xsm-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="hidden" name="mobile_code">
                                        <input type="hidden" name="country_code">
                                        <input class="form-control form--control checkUser" type="number" name="mobile" value="{{ old('mobile') }}" placeholder="@lang('Mobile')">
                                    </div>
                                    <small class="text-danger mobileExist"></small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xsm-6">
                                <div class="form-group position-relative">
                                    <input class="form--control @if ($general->secure_password) secure-password @endif" type="password" name="password" placeholder="@lang('Password')">
                                    <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xsm-6">
                                <div class="form-group position-relative">
                                    <input class="form--control" type="password" name="password_confirmation" placeholder="Confirm Password">
                                    <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password_confirmation">
                                    </div>
                                </div>
                            </div>
                            
                            <x-captcha :label="false" />

                            @if ($general->agree)

                            <div class="form-group form--check">
                                <input class="form-check-input" type="checkbox" @checked(old('agree')) name="agree" id="agree" required>
                                <span class="form-check-label">
                                    <label for="agree">@lang('I agree with')</label>
                                    <span>
                                        @foreach ($policyPages as $policy)
                                            <a href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}" target="_blank">{{ __($policy->data_values->title) }}</a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </span>
                                </span>
                            </div>
                            @endif
                        </div>
                        <button class="btn btn--base w-100" id="recaptcha" type="submit">@lang('Register')</button>
                    </form>
                    <hr class="account-hr">
                    @php
                        $credentials = $general->socialite_credentials;
                    @endphp
                    @if ($credentials->google->status == Status::ENABLE || $credentials->facebook->status == Status::ENABLE || $credentials->linkedin->status == Status::ENABLE)
                        <ul class="alt-signup">
                            @if ($credentials->google->status == Status::ENABLE)
                                <li class="alt-signup__list">
                                    <a href="{{ route('user.social.login', 'google') }}" class="alt-signup__btn text-center fs-18">
                                        <img class="social-icon" src="{{ getImage('assets/images/frontend/social_login/' . @$socialLogin->data_values->google_image, '25x25') }}" alt="@lang('image')">
                                        {{ __(@$socialLogin->data_values->google_text) }}
                                    </a>
                                </li>
                            @endif
                            @if ($credentials->facebook->status == Status::ENABLE)
                                <li class="alt-signup__list">
                                    <a href="{{ route('user.social.login', 'facebook') }}" class="alt-signup__btn text-center fs-18">
                                        <img class="social-icon" src="{{ getImage('assets/images/frontend/social_login/' . @$socialLogin->data_values->facebook_image, '25x25') }}" alt="@lang('image')">
                                        {{ __(@$socialLogin->data_values->facebook_text) }}
                                    </a>
                                </li>
                            @endif
                            @if ($credentials->linkedin->status == Status::ENABLE)
                                <li class="alt-signup__list">
                                    <a href="{{ route('user.social.login', 'linkedin') }}" class="alt-signup__btn text-center fs-18">
                                        <img class="social-icon" src="{{ getImage('assets/images/frontend/social_login/' . @$socialLogin->data_values->linkedin_image, '25x25') }}" alt="@lang('image')">
                                        {{ __(@$socialLogin->data_values->linkedin_text) }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-0">@lang('You already have an account please Login ')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--base btn--sm">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .country-code .input-group-text {
            background: #fff !important;
        }

        .country-code select {
            border: none;
        }

        .country-code select:focus {
            border: none;
            outline: none;
        }
    </style>
@endpush
@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
@push('script')
    <script>
        "use strict";
        (function($) {
            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
