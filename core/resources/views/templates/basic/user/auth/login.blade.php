@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $login = getContent('login.content', true);
        $socialLogin = getContent('social_login.content', true);
    @endphp

    <main class="main-wrapper">
        <section class="container custom-header flex-between">
            <a class="site-logo" href="{{ route('home') }}">
                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('Logo')">
            </a>
            <p class="account-alt">
                @lang('Don\'t have account?') <a class="text--base border-bottom border--base" href="{{ route('user.register') }}">@lang('Sign Up')</a>
            </p>
        </section>

        <section class="account py-80">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-heading text-cener">
                            <h4 class="section-heading__title">{{ __(@$login->data_values->title) }}</h4>
                            <p class="section-heading__desc fs-18">{{ __(@$login->data_values->subtitle) }}</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4 section-heading text-cener account-form">
                        <form action="{{ route('user.login') }}" method="POST" class="verify-gcaptcha">
                            @csrf
                            <div class="form-group">
                                <input class="form--control" type="text" name="username" value="{{ old('username') }}" placeholder="Email">
                            </div>
                            <div class="form-group position-relative">
                                <input class="form--control" type="password" name="password" placeholder="Password">
                                <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password">
                                </div>
                            </div>
                            <x-captcha :label="false" />
                            <div class="form-group form--check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    @lang('Remember Me')
                                </label>
                            </div>
                            <button class="btn btn--base w-100" type="submit" id="recaptcha">@lang('Login')</button>
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
                        <a href="{{ route('user.password.request') }}" class="forgot-pass">@lang('Forgot your password?')</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
