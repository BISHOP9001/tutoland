@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="dashboard-body">
        <div class="card p-0">
            @if ($certifications->count())
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('id')</th>
                                    <th>@lang('course name')</th>
                                    <th>@lang('completion date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($certifications as $cert)
                                    <tr>
                                       
                                        <td>{{ $cert->id }}</td>
                                        <td>{{ $cert->course_name }}</td>
                                        <td>{{ $cert->completion_date }}</td>
                                        <td>
                                            <a href='{{ route("certification.download", [$cert->course_id]) }}'>
                                                <button  class="btn btn--base" type="button">Download Certification</button>
                                                </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <span class="empty-slip-message">
                    <span class="d-flex justify-content-center align-items-center">
                        <img src="{{ asset($activeTemplateTrue . '/images/empty_list.png') }}" alt="image">
                    </span>
                    {{ __($emptyMessage) }}
                </span>
            @endif

        </div>
    </div>


@endsection

@push('script')
    <script>
     function setupQuizScript() {
    document.getElementById('submitButton').addEventListener('click', function() {
        let data = {
            _token: `{{ csrf_token() }}`,
            user_id: userId,
            course_id: courseId,
        };
        
   
        }) ;
}
    document.addEventListener('DOMContentLoaded', setupQuizScript);
    </script>
@endpush
