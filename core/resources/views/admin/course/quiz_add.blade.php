@extends('admin.layouts.app')

@section('panel')
    <div class="container">
        <!-- Add Question Form -->
        <div class="mb-4">
            <h2>Add Question</h2>
            <form method="post">
                @csrf
                <div class="form-group">
                    <label for="question_text">Question:</label>
                    <input type="text" class="form-control" id="question_text" name="question_text" required>
                </div>
                <!-- Answer Section -->
                <div class="form-group" id="answersSection">
                    <label for="answer_text">Answer:</label>
                    <div class="answer-container">
                        <input type="text" class="form-control" name="answer_text[]" required>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_correct[]" value="1">
                            <label class="form-check-label">Is Correct?</label>
                            &nbsp;&nbsp;
                            <!-- Removed onclick attribute from here -->
                            <button type="button" class="btn btn-danger px-3">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mt-2" id="addAnswerBtn">Add New Answer</button>

                <button type="submit" class="btn btn-primary mt-2">
                    <i class="fas fa-save" aria-hidden="true"></i>Submit
                </button>
            </form>
        </div>

        <!-- List of Questions and Answers -->
        <div>
            <h2>List of Questions</h2>
            <table class="table table--light style--two" id="qestTable">
                <thead>
                    <tr>
                        <th>@lang('ID')</th>
                        <th>@lang('Question')</th>
                        <th>@lang('answers count')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addAnswerBtn = document.getElementById('addAnswerBtn');
            const answersSection = document.getElementById('answersSection');
            const qestTable = document.getElementById('qestTable');

            window.deleteAnswer = function (button) {
                const answerContainer = button.closest('.form-group');

                // Check if the answer container is not the first one before removal
                if (answerContainer.previousElementSibling) {
                    answerContainer.remove();
                } else {
                    // Clear the input field and uncheck the "Is Correct" checkbox
                    const inputField = answerContainer.querySelector('input[name="answer_text[]"]');
                    inputField.value = '';

                    const checkbox = answerContainer.querySelector('input[name="is_correct[]"]');
                    checkbox.checked = false;
                }
            };

            window.deleteQuestion = function (button, questionId) {
                const row = button.closest('tr');
                var questionId = row.getAttribute('data-question-id');
                console.log('{{ route('admin.course.quiz.question.remove') }}');
                 fetch("{{ route('admin.course.quiz.question.remove') }}", {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        questionId: questionId,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        notify('success', 'Question removed Successfully');
                        console.log(data);
                        row.remove();
                    } else {
                        console.log(data);

                        notify('error', 'Failed to remove question');
                    }
                })
                .catch(error => {
                    notify('error', 'An error occurred while removing the question');
                });
            };

            addAnswerBtn.addEventListener('click', function () {
                const answerContainer = createAnswerContainer();
                answersSection.appendChild(answerContainer);
            });

            function createAnswerContainer() {
                const answerContainer = document.createElement('div');
                answerContainer.classList.add('form-group');
                answerContainer.innerHTML = `
                    <label for="answer_text">Answer:</label>
                    <div class="answer-container">
                        <input type="text" class="form-control" name="answer_text[]" required>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_correct[]" value="1">
                            <label class="form-check-label">is Correct?</label>
                            &nbsp;&nbsp;
                            <button type="button" class="btn btn-danger px-3" onclick="deleteAnswer(this)">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                `;
                return answerContainer;
            }

            document.querySelector('form').addEventListener('submit', function (event) {
                event.preventDefault();

                // Get the true answers
                const trueAnswers = document.querySelectorAll('.form-check-input[name="is_correct[]"]:checked');

                // Check if there is at least one true answer
                if (trueAnswers.length === 0) {
                    alert('Please select at least one true answer.');
                    return;
                }

                $.ajax({
                    type: "post",
                    url: "{{route('admin.course.quiz.store')}}",
                    data: {
                        courseId : {{$courseId}},
                        question :document.getElementById('question_text').value,
                        answers: getAnswersData(),
                        _token: "{{csrf_token()}}"
                    },
                    success:function(data){
                        if (data.status === 'success') {
                            notify('success', 'Import Data Successfully');
                            console.log(data);

                            // Add a new row to the table with the returned question ID
                            const newRow = qestTable.insertRow(-1);
                            newRow.setAttribute('data-question-id', data.question.id);

                            const cell1 = newRow.insertCell(0);
                            const cell2 = newRow.insertCell(1);
                            const cell3 = newRow.insertCell(2);
                            const cell4 = newRow.insertCell(3);

                            cell1.innerHTML = data.question.id;
                            cell2.innerHTML = data.question.text;
                            cell3.innerHTML = getAnswersData().length;
                            cell4.innerHTML = `
                                <div class="button--group">
                                    <button type="button" class="btn btn-danger px-3" onclick="deleteQuestion(this)">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            `;

                            // Clear the form and answer containers
                            document.getElementById('question_text').value = '';
                            answersSection.innerHTML = '';
                        } else {
                            alert('Failed to store question and answers.');
                        }
                    }
                });

                function getAnswersData() {
                    const answersData = [];
                    // Iterate over answer containers and collect data
                    document.querySelectorAll('.answer-container').forEach(function (container, index) {
                        const answerText = container.querySelector('input[name="answer_text[]"]').value;
                        const isCorrect = container.querySelector('input[name="is_correct[]"]').checked ? 1 : 0;
                        // Add answer data to the array
                        answersData.push({ answerText, isCorrect });
                    });
                    return answersData;
                }

                 // Clear the form and answer containers
                document.getElementById('question_text').value = '';

                // Clear the inner content of the answer containers
                document.querySelectorAll('.answer-container').forEach(function (container, index) {
                    if (index > 0) {
                        // Clear the input field and uncheck the "Is Correct" checkbox
                        container.querySelector('input[name="answer_text[]"]').value = '';
                        container.querySelector('input[name="is_correct[]"]').checked = false;
                    }
                });

            });
        });
    </script>
@endsection

@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('style')
    <style>
        .mt--3:has(.form-group) {
            margin-top: 1rem !important;
        }
    </style>
@endpush
