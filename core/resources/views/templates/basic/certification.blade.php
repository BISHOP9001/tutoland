<!-- resources/views/quiz/completed.blade.php -->

@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <main class="main-wrapper">
        <div class="quiz-completed-page">
            <div class="custom--container">
                <div class="quiz-completed-container">
                    <h2 class="quiz-completed-message">Congratulations! You have successfully completed the quiz.</h2>

                    <!-- You can customize this message based on your requirements -->
                    <form  class="form-group" method="GET" id="certFrom">

                    <div class="quiz-download-certification">
                        <p>You can download your certification PDF below:</p>
                        <a href='{{ route("certification.download", [$course->id]) }}'>
                        <button  class="btn btn--base" type="button">Download Certification</button>
                        </a>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </main>
@endsection
@push('script')
<script>
       //original script
       (function($) {
        "use strict";
        $(document).ready(function() {
            var scrollHereElement = $(".scroll-here").first();

            if (scrollHereElement.length) {
                var container = $(".course-view-inner-right");
                container.animate({
                    scrollTop: scrollHereElement.offset().top - container.offset().top + container.scrollTop()
                }, 1000);
            }
        });
    })(jQuery);

   
    var userId = '{{ $user->id }}';
    var courseId = '{{ $course->id }}';
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