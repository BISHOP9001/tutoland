@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <main class="main-wrapper">
        <!-- ========== Course Details Start ========== -->
        <div class="course-detail-page">
            <div class="custom--container">
                <div class="course-detail-page-row">
                    <div class="course-detail-page-column course-detail-page-column--content">
                        <h1 class="course-title">{{ __($course->title) }}</h1>

                        <p class="course-description">{{ __($course->short_description) }}</p>

                        @if ($course->premium && $course->lessons->where('premium', 0)->count() > 0)
                            @php
                                $freeLesson = $course->lessons->where('premium', 0)->first();
                            @endphp
                            <div class="course-demo">
                                <div class="course-demo-details">
                                    <img class="icon" src="{{ asset($activeTemplateTrue . 'images/icons/multiplayer-green.png') }}">
                                    <h6 class="title">@lang('Free demo class')</h6>
                                </div>
                                <a href="{{ route('course.lesson', [slug($freeLesson->title), $freeLesson->id]) }}" class="course-demo-btn btn btn--rounded" type="button">
                                    <i class="far fa-play-circle"></i>
                                    <span>@lang('Watch the video')</span>
                                </a>
                            </div>
                        @endif

                        @if ($course->learns)
                            <div class="course-wywl">
                                <h5 class="course-wywl-title">@lang('What you\'ll learn:')</h5>
                                <div class="course-wywl-inner">
                                    <div class="course-wywl-content">
                                        <ul class="course-wywl-list">
                                            @foreach ($course->learns ?? [] as $learn)
                                                <li class="course-wywl-list-item">
                                                    <span class="icon"><i class="fas fa-check"></i></span>
                                                    <p class="text">{{ __($learn) }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <button class="course-wywl-collapse" type="button">
                                        <span class="text">@lang('Show More')</span>
                                        <span class="icon"><i class="fas fa-chevron-down"></i></span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="course-content">
                            <h5 class="title">@lang('Course content')</h5>
                            <ul class="course-lessons">
                                @php
                                    $sectionClass = ['bg--orange', 'bg--light-blue', 'bg--purple', 'bg--dark-blue', 'bg--pink'];
                                @endphp
                                @foreach ($course->sections as $section)
                                    <li class="course-lesson has-lesson">
                                        <div class="course-lesson-header" data-bs-toggle="collapse" data-bs-target="#section-{{ $section->id }}" aria-expanded="false">
                                            <div class="course-lesson-number {{ $sectionClass[$loop->index % 5] }}">
                                                <p>@lang('Section') <br> <span>{{ $loop->iteration }}</span></p>
                                            </div>

                                            <div class="course-lesson-info">
                                                <h5 class="course-lesson-title">{{ __($section->title) }}</h5>

                                                <div class="course-lesson-meta-container">
                                                    <ul class="course-lesson-meta course-lesson-meta--one">
                                                        <li class="course-lesson-meta-item">
                                                            <span class="icon"><i class="las la-video"></i></span>
                                                            <span class="text">{{ $section->lessons->count() }} @lang('live classes')</span>
                                                        </li>

                                                        <li class="course-lesson-meta-item">
                                                            <span class="text">{{ secondsToHMS($section->lessons->sum('video_duration')) }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="collapse" id="section-{{ $section->id }}">
                                            <div class="course-lesson-body">
                                                <ul class="course-lesson-content">
                                                    @foreach ($section->lessons as $lesson)
                                                        @php
                                                            $lessonUrl = lessonPermission($lesson) ? route('course.lesson', [slug($lesson->title), $lesson->id]) : 'javascript:void(0)';
                                                        @endphp

                                                        <li class="course-lesson-content-item">
                                                            <a class="course-lesson-content-video-link" href="{{ $lessonUrl }}">
                                                                <span class="icon"><i class="las la-camera"></i></span>
                                                                <span class="text">{{ __($lesson->title) }}</span>
                                                                <span class="duration">{{ secondsToHMS($lesson->video_duration) }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @php echo $course->description; @endphp


                        {{-- Rating Review Start --}}
                        <div class="course-reviews mt-5">
                            <div class="course-reviews-box border-bottom-0 py-4">
                                <div class="row g-4 align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-end">
                                            <span class="d-inline-block as-rating">{{ $course->avg_rating }}</span>
                                            <span class="d-inline-block as-rating-divider">/</span>
                                            <span class="d-inline-block as-rating-total">@lang('5')</span>
                                        </div>
                                        <div class="review-card-ratings big-star">
                                            @php
                                                echo ratingStar($course->avg_rating);
                                            @endphp
                                        </div>
                                        <span class="d-inline-block as-rating-text">{{ $course->total_review }} @lang('Ratings')</span>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="as-ratings">
                                            @for ($i = 5; $i >= 1; $i--)
                                                <li class="as-ratings__item">
                                                    <div class="review-card-ratings">
                                                        @php
                                                            echo ratingStar($i);
                                                            $rating = $course->reviews->where('rating', $i)->count();
                                                            $ratingRatio = $rating ? ($rating / $course->total_review) * 100 : 0;
                                                        @endphp
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress rounded-0 flex-shrink-0 flex-grow-1 me-2">
                                                            <div class="progress-bar customWidth" role="progressbar" data-custom_width="{{ $ratingRatio }}" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="d-inline-block as-rating-text mt-0"> {{ $rating }}</span>
                                                    </div>
                                                </li>
                                            @endfor
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- course-reviews-box end -->
                            @if ($course->reviews->count())
                                <div class="py-2 border-bottom border-top">
                                    <small>@lang('Students Review')</small>
                                </div>
                                <div class="review">

                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="course-detail-page-column course-detail-page-column--sidebar">
                        <aside class="course-sidebar">
                            <div class="course-sidebar-header">
                                <img class="course-thumb" src="{{ getImage(getFilePath('course') . '/' . $course->image, getFileSize('course')) }}" alt="@lang('course')">
                            </div>

                            <div class="course-sidebar-body">
                                @php
                                    $firstLesson = $course->sections[0]->lessons->first();
                                @endphp
                                @if ($course->premium)
                                    <div class="course-sidebar-box course-sidebar-box--two">
                                        <div class="course-details">
                                            <div class="course-details-wrapper">
                                                <h4 class="course-price">
                                                    @if ($course->discount_price > 0)
                                                        <small><del class="text-muted fs-18">{{ $general->cur_sym }}{{ showAmount($course->price) }}</del></small>
                                                        {{ $general->cur_sym }}{{ showAmount($course->discount_price) }}
                                                    @else
                                                        {{ $general->cur_sym }}{{ showAmount($course->price) }}
                                                    @endif
                                                </h4>
                                                @if (!lessonPermission($firstLesson))
                                                    <button class="course-promo-apply-btn" type="button">
                                                        <span class="icon"><i class="las la-ticket-alt"></i></span>
                                                        <span class="text">@lang('Promo Code')</span>
                                                    </button>
                                                @endif

                                                <button class="course-share-btn shareBtn" type="button">
                                                    <span class="icon"><i class="las la-share"></i></span>
                                                    <span class="text">@lang('Share')</span>
                                                </button>
                                            </div>

                                            <div class="course-cupon-form d-none">
                                                <button type="button" class="course-cupon-form-close">
                                                    <i class="far fa-times-circle"></i>
                                                </button>
                                                <input class="course-cupon-form-input couponCode" type="text" placeholder="@lang('Promo code')">
                                                <button type="button" class="btn btn--rounded btn--base applyCoupon">@lang('Apply')</button>
                                            </div>
                                            <span class="couponResponse mt-2"></span>
                                            @if (lessonPermission($firstLesson))
                                                <a href="{{ route('course.lesson', [slug(@$firstLesson->title), @$firstLesson->id]) }}" class="btn btn--rounded btn--base w-100 course-join-btn">
                                                    <span class="text">@lang('Watch Now')</span>
                                                </a>
                                            @else
                                                <button class="btn btn--rounded btn--base w-100 course-join-btn payBtn" role="button">
                                                    <span class="text">@lang('Pay') {{ $general->cur_sym }}<span class="payableAmount">{{ $course->discount_price > 0 ? showAmount($course->discount_price) : showAmount($course->price) }}</span></span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                @if ($coursePurchase->status == 1)
                                <div class="course-sidebar-box course-sidebar-box--two">
                                    <div class="course-details">
                                        <a class="btn btn--rounded btn--base w-100 course-join-btn" href="{{ route('course.lesson', [slug(@$firstLesson->title), @$firstLesson->id]) }}">
                                            <span class="text">@lang('Watch Now')</span>
                                        </a>
                                    </div>
                                </div>
                            @else
                                @if ($coursePurchase->status == 2)
                                    <div class="course-sidebar-box course-sidebar-box--two">
                                        <div class="course-details">
                                            <span class="badge badge--danger">@lang('Your purchase request has been rejected.</br>Please contact administration for more information.')</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="course-sidebar-box course-sidebar-box--two">
                                        <div class="course-details">
                                            <span class="badge badge--warning">@lang('Your enrollment for this course has been submitted successfully.</br>Please wait for admin approval.')</span>
                                        </div>
                                    </div>
                                @endif
                            @endif

                                @endif

                                <div class="course-sidebar-box course-sidebar-box--three">
                                    <h5 class="title">@lang('This course includes:')</h5>
                                    <ul class="course-includes">
                                        @foreach ($course->includes['icon'] ?? [] as $icon)
                                            <li class="course-includes-item">
                                                <span>@php echo $icon; @endphp</span>
                                                <span class="text">{{ __($course->includes['text'][$loop->index]) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========== Course Details End =========== -->
    </main>

    <div id="shareModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Course Share')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <div class="modal-body custom-modal__form share">
                    <a class="share-link facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" title="@lang('Facebook')" target="_blank">
                        <i class="fab fa-facebook-f"></i>
                    </a>

                    <a class="share-link pinterest" href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __($course->title) }}&media={{ getImage(getFilePath('course') . '/' . $course->image, getFileSize('course')) }}" title="@lang('Pinterest')"
                        target="_blank">
                        <i class="fab fa-pinterest-p"></i>
                    </a>

                    <a class="share-link linkedin" href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ $course->title }}&amp;summary={{ $course->short_description }}" title="@lang('Linkedin')" target="_blank">
                        <i class="fab fa-linkedin-in"></i>
                    </a>

                    <a class="share-link twitter" href="https://twitter.com/intent/tweet?text={{ __($course->title) }}%0A{{ url()->current() }}" title="@lang('Twitter')" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>

                    <div class="copy-link position-relative">
                        <input type="text" class="copyURL form-control form--control" id="copyURL" value="{{ url()->current() }}" readonly="">
                        <span class="copyBoard" id="copyBtn"><i class="las la-copy"></i> <strong class="copyText">@lang('Copy')</strong></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Payment')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{ route('user.deposit.insert') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <input type="hidden" name="coupon_code">
                        <input type="hidden" name="currency">

                        <div class="form-group">
                            <label class="form-label">@lang('Select Gateway')</label>
                            <select class="form--control form-select" name="gateway" required>
                                <option value="">@lang('Select One')</option>
                                @foreach ($gatewayCurrency as $data)
                                    <option value="{{ $data->method_code }}" @selected(old('gateway') == $data->method_code) data-gateway="{{ $data }}">{{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control form--control" value="{{ $course->discount_price > 0 ? getAmount($course->discount_price) : getAmount($course->price) }}" autocomplete="off" readonly>
                                <span class="input-group-text">{{ $general->cur_text }}</span>
                            </div>
                        </div>
                        <div class="mt-3 preview-details d-none">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Limit')</span>
                                    <span><span class="min fw-bold">0</span> {{ __($general->cur_text) }} - <span class="max fw-bold">0</span> {{ __($general->cur_text) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Charge')</span>
                                    <span><span class="charge fw-bold">0</span> {{ __($general->cur_text) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Payable')</span> <span><span class="payable fw-bold"> 0</span> {{ __($general->cur_text) }}</span>
                                </li>
                                <li class="list-group-item justify-content-between d-none rate-element">

                                </li>
                                <li class="list-group-item justify-content-between d-none in-site-cur">
                                    <span>@lang('In') <span class="method_currency"></span></span>
                                    <span class="final_amo fw-bold">0</span>
                                </li>
                                <li class="list-group-item justify-content-center crypto_currency d-none">
                                    <span>@lang('Conversion with') <span class="method_currency"></span> @lang('and final value will Show on next step')</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--base w-100">@lang('Pay now')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            let coursePrice = `{{ getAmount($course->discount_price > 0 ? $course->discount_price : $course->price) }}`;
            $('.applyCoupon').on('click', function() {
                let couponCode = $('.couponCode').val();

                let data = {
                    _token: `{{ csrf_token() }}`,
                    coupon_code: couponCode,
                    course_price: coursePrice
                };

                $.ajax({
                    url: `{{ route('coupon.check') }}`,
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.status == 'error') {
                            let message = '';
                            if (typeof response.message == 'object') {
                                $.each(response.message, function(i, mess) {
                                    message += mess + ' ';
                                });
                            } else {
                                message = response.message;
                            }

                            $('.payableAmount').text(coursePrice);
                            $('[name=amount]').val(coursePrice);
                            $('.couponResponse').removeClass('text--primary').addClass('text--danger').text(message);
                            $('[name=coupon_code]').val('');
                        } else {
                            $('.payableAmount').text(response.payable_amount);
                            $('[name=amount]').val(response.payable_amount);
                            $('[name=coupon_code]').val(couponCode);
                            $('.couponResponse').addClass('text--primary').removeClass('text--danger').text(response.message);
                        }
                    }
                })

            });

            $('.shareBtn').on('click', function() {
                let modal = $('#shareModal');

                modal.modal('show');
            });

            $('#copyBtn').on('click', function() {
                var copyText = document.getElementById("copyURL");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);
            });

            $('.payBtn').on('click', function() {
                let modal = $('#paymentModal');

                modal.modal('show');
            });

            $('select[name=gateway]').change(function() {
                if (!$('select[name=gateway]').val()) {
                    $('.preview-details').addClass('d-none');
                    return false;
                }
                var resource = $('select[name=gateway] option:selected').data('gateway');
                var fixed_charge = parseFloat(resource.fixed_charge);
                var percent_charge = parseFloat(resource.percent_charge);
                var rate = parseFloat(resource.rate)
                if (resource.method.crypto == 1) {
                    var toFixedDigit = 8;
                    $('.crypto_currency').removeClass('d-none');
                } else {
                    var toFixedDigit = 2;
                    $('.crypto_currency').addClass('d-none');
                }
                $('.min').text(parseFloat(resource.min_amount).toFixed(2));
                $('.max').text(parseFloat(resource.max_amount).toFixed(2));
                var amount = parseFloat($('input[name=amount]').val());
                if (!amount) {
                    amount = 0;
                }
                if (amount <= 0) {
                    $('.preview-details').addClass('d-none');
                    return false;
                }
                $('.preview-details').removeClass('d-none');
                var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                $('.charge').text(charge);
                var payable = parseFloat((parseFloat(amount) + parseFloat(charge))).toFixed(2);
                $('.payable').text(payable);
                var final_amo = (parseFloat((parseFloat(amount) + parseFloat(charge))) * rate).toFixed(toFixedDigit);
                $('.final_amo').text(final_amo);
                if (resource.currency != '{{ $general->cur_text }}') {
                    var rateElement = `<span class="fw-bold">@lang('Conversion Rate')</span> <span><span  class="fw-bold">1 {{ __($general->cur_text) }} = <span class="rate">${rate}</span>  <span class="method_currency">${resource.currency}</span></span></span>`;
                    $('.rate-element').html(rateElement)
                    $('.rate-element').removeClass('d-none');
                    $('.in-site-cur').removeClass('d-none');
                    $('.rate-element').addClass('d-flex');
                    $('.in-site-cur').addClass('d-flex');
                } else {
                    $('.rate-element').html('')
                    $('.rate-element').addClass('d-none');
                    $('.in-site-cur').addClass('d-none');
                    $('.rate-element').removeClass('d-flex');
                    $('.in-site-cur').removeClass('d-flex');
                }
                $('.method_currency').text(resource.currency);
                $('input[name=currency]').val(resource.currency);
                $('input[name=amount]').on('input');
            });
            $('input[name=amount]').on('input', function() {
                $('select[name=gateway]').change();
                $('.amount').text(parseFloat($(this).val()).toFixed(2));
            });

            $('.customWidth').each(function(element) {
                $(this).css('width', `${$(this).data('custom_width')}%`);
            });

            loadReview();

            var lastId = 0;

            $(document).on('click', '.load-more', function() {
                lastId = $(this).data('last_id');
                $('.loading').removeClass('d-none');
                loadReview();
            });

            function loadReview() {

                let data = {
                    _token: `{{ csrf_token() }}`,
                    course_id: `{{ $course->id }}`,
                    last_id: lastId
                }

                $.ajax({
                    url: `{{ route('course.reviews') }}`,
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.status == 'error') {
                            return false;
                        }
                        if (lastId == 0) {
                            $('.review').html(response);
                        } else {
                            $('.load-more').remove();
                            $('.review').append(response);
                        }
                    }
                });
            }

        })(jQuery);
    </script>
@endpush
