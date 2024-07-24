@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="dashboard-body my-course">
        <div class="bg-white p-0 mt-5">
            @if ($myCourses->count())
                <div class="course-list">
                    @foreach ($myCourses as $myCourse)
                        @include($activeTemplate . 'user.course.item')
                    @endforeach
                </div>
            @else
                <span class="empty-slip-message">
                    <span class="d-flex justify-content-center align-items-center">
                        <img src="{{ asset($activeTemplateTrue . '/images/empty_list.png') }}" alt="image">
                    </span>
                    @lang('No purchased course found!')
                </span>
            @endif

            @if ($myCourses->hasPages())
                <div class="card-footer">
                    {{ $myCourses->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-buttons')
    <div class="button-list">
        <li class="button-list__item">
            <a href="{{ route('courses') }}" class="btn btn--base flex-align btn--sm"> <span class="icon"><i class="las la-plus-circle"></i></span>@lang('Enroll new')</a>{{-- Purchase new --}}
        </li>
    </div>
@endpush
