<!-- resources/views/quiz.blade.php -->

@extends($activeTemplate . 'layouts.frontend')

@section('content')
<main class="main-wrapper">
    <div class="quiz-page">
        <div class="custom--container">
            <br /><br /><br />
            {{ $quizId }}
            <div class="quiz-container">
                <form action="{{ route("quiz.submit") }}" class="form-group quiz-form" method="post" data-quiz-id="{{ $quizId }}">
                    @csrf
                    @foreach ($questions as $question)
                        <h6 class="quiz-question" data-question-id="{{ $question->id }}">{{ $question->text }}</h6>
                        <div class="quiz-options">
                            @foreach ($question->answers as $answer)
                                <div class="form-check me-3">
                                    <input class="form-check-input" name="selected_answers[{{ $question->id }}]" type="radio" value="{{ $answer->id }}">
                                    <label class="form-check-label" for="selected_answers[{{ $question->id }}]">{{ $answer->text }}</label>
                                </div><br/>
                            @endforeach
                        </div>
                        <br /><br /><br />
                    @endforeach

                    <button class="btn btn--base submitButton" type="button">Submit</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('script')
<script>
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
function setupQuizScript() {
    document.querySelector('.submitButton').addEventListener('click', function () {
        var form = document.querySelector('.quiz-form');

        if (form) {
            var quizId = {{ $quizId }};
            var selectedAnswers = form.querySelectorAll('input[name^="selected_answers"]:checked');

            if (selectedAnswers.length > 0) {
                var formData = {
                    quizId: quizId,
                    course_id: {{ $course->id }},
                    answers: []
                };

                selectedAnswers.forEach(function (selectedAnswer) {
                    var questionIdElement = selectedAnswer.closest('.quiz-options').previousElementSibling;
                    var questionId = questionIdElement ? questionIdElement.getAttribute('data-question-id') : null;

                    if (questionId !== null) {
                        console.log('Question ID:', questionId);

                        var answerId = selectedAnswer.value;

                        formData.answers.push({
                            questionId: questionId,
                            answerId: answerId,
                        });
                    } else {
                        console.error('Could not find the parent element with class "quiz-question"');
                    }
                });

                submitQuiz(formData);
            } else {
                alert('Please answer at least one question before proceeding.');
            }
        } else {
            console.error('Form not found.');
        }
    });
}


document.addEventListener('DOMContentLoaded', setupQuizScript);

function submitQuiz(formData) {
    // Use your preferred method (e.g., fetch or axios) to send data to the server
    // and handle the submission logic here.
    console.log('Quiz ID:', formData.quizId);
    console.log('Answers:', formData.answers);
    console.log('submit:');


    // Example using fetch
    fetch('{{ route('quiz.submit') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify(formData),
    })
    .then(response => response.json())
    .then(data => handleQuizResponse(data))
    .catch(error => console.log('Error:', error));
}
function handleQuizResponse(data) {
    // Check if the response contains score and attempts
    if (data.hasOwnProperty('score') && data.hasOwnProperty('attempts')) {
        var score = data.score;
        var attempts = data.attempts;
        console.log('Score:', score);
        console.log('attempts:', attempts);

        // Show a popup to the user based on score and attempts
        if (attempts > 0)  {
            if (score >= 60 )   {
             alert('Congratulations! You passed the quiz. You can now proceed to the certification page.');

             window.location.href = '{{ route("quiz.page", ["courseId" => ":courseId"]) }}'.replace(':courseId', {{ $course->id }});
            
        } else {
            showFailedPopup(score);
        }
    } else {
        showRestartPopup();
    }
    }
}
function showCertificationPopup() {
    // You can use your preferred modal or popup library to display the popup
}function showFailedPopup(score) {
    // You can use your preferred modal or popup library to display the popup
    alert('Sorry, you did not pass the quiz. Please try again.\n'+score);
    // Redirect the user to the dashboard
}
function showRestartPopup() {
    // You can use your preferred modal or popup library to display the popup
    alert('Sorry, you did not pass the quiz. Please watch the course again.');
    window.location.href = '{{ route("course.details", ["slug" => ":slug", "id" => ":id"]) }}'.replace(':slug', '{{ $course->title }}').replace(':id', '{{ $course->id }}');

}
</script>
@endpush
