@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 bg--transparent shadow-none">
                @if ($quiz)
                    <div class="card-body p-0">
                        <div class="table-responsive--md table-responsive table-solved">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Quiz ID')</th>
                                        <th>@lang('Course name')</th>
                                        <th>@lang('Questions')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $quiz->id }}</td>
                                        <td>{{ $quiz->course->title }}</td>
                                        <td>{{ $quiz->questions->count() }}</td>
                                        <td>
                                           
                                             <button type="button" class="btn btn-danger px-3" onclick="deleteQuiz(this);"><i class="fas fa-trash" aria-hidden="true"></i></button>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <span class="empty-slip-message">
                        <span class="d-flex justify-content-center align-items-center">
                            <img src="{{ asset($activeTemplateTrue . '/images/empty_list.png') }}" alt="image">
                        </span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@if(!$quiz)
@push('breadcrumb-plugins')
    <a href="#" class="btn btn-outline--primary btn-sm" onclick="event.preventDefault(); document.getElementById('addNewQuizForm').submit();">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
@endpush

<!-- Add this form at the bottom of your Blade file -->
<form id="addNewQuizForm" action="{{ route('admin.course.quiz.add', $courseId) }}" method="post" style="display: none;">
    @csrf
    <!-- Include any additional form fields if necessary -->
</form>
@endif

<!-- Add this form at the bottom of your Blade file -->
{{-- <form id="removeQuizForm" action="{{ route('admin.course.quiz.remove' ,$quiz->id) }}" method="post" style="display: none;">
    @csrf
    <!-- Include any additional form fields if necessary -->
</form> --}}

<script>
    deleteQuiz = function (button) {
        @if ($quiz)
            const row = button.closest('tr');
            $.ajax({
                type: "post",
                url: "{{ route('admin.course.quiz.remove', ['id' => $quiz->id]) }}",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function (data) {
                    if (data.status === 'success') {
                        notify('success', 'Quiz removed Successfully');
                        console.log(data);
                        row.remove();
                    } else {
                        notify('error', 'Failed to remove question');
                    }
                },
                error: function (xhr, status, error) {
                    notify('error', 'An error occurred while removing the question');
                }
            });
        @endif
    };
</script>

