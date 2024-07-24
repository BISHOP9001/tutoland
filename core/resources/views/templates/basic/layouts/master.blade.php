<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ $general->siteName(__($pageTitle)) }}</title>

    @include('partials.seo')

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/global/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'dashboard/css/icon-moon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'dashboard/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">

    @stack('style-lib')
    @stack('style')

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ $general->base_color }}">

</head>

<body>

    <div class="preloader">
        <div class="preloader__favicon">
            <img src="{{ getImage(getFilePath('logoIcon') . '/favicon.png') }}" alt="">
        </div>
        <div class="preloader__circle">
        </div>
    </div>

    <div class="body-overlay"></div>

    <div class="sidebar-overlay"></div>

    <a class="scroll-top"><i class="fas fa-angle-double-up"></i></a>



    <div class="dashboard position-relative">
        <div class="dashboard__inner">

            @include($activeTemplate . 'partials.sidebar')

            <div class="dashboard__right">

                <div class="dashboard-header">
                    <div class="dashboard-header__inner">
                        <div class="dashboard-header__left">
                            <h1 class="dashboard-header__grettings mb-0">{{ __($pageTitle) }}</h1>
                        </div>
                        <ul class="d-flex align-items-center dashboard-topbar">
                            @stack('breadcrumb-buttons')
                            <div class="dashboard__bar d-lg-none d-block">
                                <span class="dashboard-body__bar-icon"><i class="far fa-bars"></i></span>
                            </div>
                        </ul>
                    </div>
                </div>
                @yield('content')

            </div>
            <!-- Dashboar Body End -->
        </div>
    </div>


    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset($activeTemplateTrue . 'js/jquery.validate.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'dashboard/js/main.js') }}"></script>

    @stack('script-lib')

    @include('partials.notify')

    @include('partials.plugins')


    @stack('script')


    <script>
        (function($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

        })(jQuery);
    </script>


    <script>
        (function($) {
            "use strict";

            $('form').on('submit', function() {
                if ($(this).valid()) {
                    $(':submit', this).attr('disabled', 'disabled');
                }
            });

            var inputElements = $('[type=text],[type=password],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {

                if (element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').addClass('required');
                }

            });

            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });

        })(jQuery);
    </script>

</body>

</html>
