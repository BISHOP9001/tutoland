@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <main class="main-wrapper">
        <section class="py-60">
            <div class="custom--container">
                <div class="row justify-content-center">
                    <div class="col-sm-10 col-md-8 col-lg-7 col-xl-5">
                        <div class="card custom--card">
                            <div class="card-header">
                                <h5 class="card-title">{{ __($pageTitle) }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="section-heading__desc fs-18">@lang('To recover your account please provide your email or username to find your account.')</p>
                                <form method="POST" action="{{ route('user.password.email') }}" class="verify-gcaptcha">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="form--control" name="value" value="{{ old('value') }}" required autofocus="off">
                                    </div>

                                    <x-captcha :label="false" />

                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
