@php
    $policyPages = getContent('policy_pages.element', false, null, true);
    $socialIcons = getContent('social_icon.element', orderById: true);
@endphp

<footer class="footer py-80">
    <div class="custom--container">
        <div class="footer-top">
            <a class="footer-logo" href="{{ route('home') }}">
                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('Logo')">
            </a>
            <ul class="footer-menu">
                {{-- <li class="footer-menu-item">
                    <a class="footer-menu-link" href="{{ route('home') }}">@lang('Home')</a>
                </li> --}}
                {{-- <li class="footer-menu-item">
                    <a class="footer-menu-link" href="{{ route('courses') }}">@lang('Courses')</a>
                </li> --}}
                {{-- <li class="footer-menu-item">
                    <a class="footer-menu-link" href="{{ route('contact') }}">@lang('Contact')</a>
                </li> --}}

                @guest
                    <li class="footer-menu-item">
                        <a class="footer-menu-link" href="{{ route('user.login') }}">@lang('Login')</a>
                    </li>
                    <li class="footer-menu-item">
                        <a class="footer-menu-link" href="{{ route('user.register') }}">@lang('Register')</a>
                    </li>
                @endguest
            </ul>

            @if ($general->multi_language)
                @php
                    $language = App\Models\Language::all();
                @endphp
                <div class="language-box">
                    <select class="select langSel">
                        @foreach ($language as $item)
                            <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>{{ __($item->name) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="footer-bottom justify-content-between">
            <div class="flex-align gap-4">
                <p class="footer-rights-text">
                    &copy; {{ date('Y') }} <a class="text--base" href="{{ route('home') }}">{{ __($general->site_name) }}</a>. @lang('All Rights Reserved')
                </p>

                <ul class="footer-links">
                    @foreach ($policyPages as $policy)
                        <li>
                            <a href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}">{{ __(@$policy->data_values->title) }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <ul class="footer-social">
                @foreach ($socialIcons as $socialIcon)
                    <li class="footer-social-item">
                        <a class="footer-social-link" href="{{ $socialIcon->data_values->url }}" target="_blank">
                            @php echo $socialIcon->data_values->social_icon @endphp
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>
</footer>
