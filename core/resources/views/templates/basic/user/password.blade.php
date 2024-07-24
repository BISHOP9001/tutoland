@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="dashboard-body">
        <div class="row gy-4 dashboard-card-wrapper justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5>@lang('Change your password')</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">@lang('Current Password')</label>
                                <input type="password" class="form-control form--control" name="current_password" required autocomplete="current-password">
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Password')</label>
                                <input type="password" class="form-control form--control @if ($general->secure_password) secure-password @endif" name="password" required autocomplete="current-password">
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Confirm Password')</label>
                                <input type="password" class="form-control form--control" name="password_confirmation" required autocomplete="current-password">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
