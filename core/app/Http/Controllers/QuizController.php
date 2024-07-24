<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Answer;
use App\Models\Course;
use App\Models\GatewayCurrency;
use App\Models\Page;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\UserCertifications;
use App\Models\UserLessonProgress;
use App\Models\UserQuiz;
use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use \PDF;

class QuizController extends Controller
{
    private  $quiz;
    public function index($courseId)
    {
        $pageTitle  = 'quiz';

        $progres = UserLessonProgress::with('lesson')
            ->where('user_id', auth()->id())
            ->where('lesson_status', 'pending')
            ->whereRelation('lesson', 'course_id', $courseId)->get();
        if ($progres->isNotEmpty()) {

            throw ValidationException::withMessages(['Course not completed' => 'Please complete all your lessons']);
        }

        $course = Course::find($courseId);
        $certification = UserCertifications::where('user_id', '=', auth()->id())->where('course_name', '=', $course->title)->first();
        if ($certification != null) {
            $pageTitle = 'Quiz Completed';
            $user = auth()->user();
            return view($this->activeTemplate . 'certification', compact('pageTitle', 'user', 'course'));
        }



        $this->quiz = Quiz::find($courseId);

        $quizId = $this->quiz->id;

        // $question = Quiz::with('questions', 'questions.answers')
        //     ->find($courseId);
        $questions = Question::with('answers')
            ->where('quiz_id', $this->quiz->id)
            ->get();
        $pageTitle = 'Quiz Completed';
        return view($this->activeTemplate . 'quiz', compact('pageTitle', 'questions', 'quizId', 'course'));
    }

    ///

    public function submit(Request $request)
    {
        try {
            $response = []; // Initialize an empty array to store the response data

            $userQuiz = UserQuiz::where('user_id',  Auth::user()->id)
                ->where('quiz_id', $request['quizId'])
                ->first();
            $course = Course::find($request['course_id']);

            if ($userQuiz == null) {
                $userQuiz = UserQuiz::create([
                    'user_id' =>  Auth::user()->id,
                    'quiz_id' => $request['quizId'],
                    'score' => 0,
                    'attempts' => 2,
                ]);
            }

            $lessonProgress = UserLessonProgress::with('lesson')
                ->where('user_id', Auth::user()->id)
                ->whereHas('lesson', function ($query) use ($request) {
                    $query->where('course_id', $request['course_id']);
                })
                ->first();
            $response['score'] = 0;
            $response['attempts'] = $userQuiz->attempts;

            // Check if the user has attempts left
            if ($userQuiz->attempts > 0) {
                $userQuiz->attempts--;
                $submittedAnswers = $request->input('answers');
                $answerIds = array_column($submittedAnswers, 'answerId'); // Assuming input name is 'selected_answers'
                $score = $this->calculateScore($request['quizId'], $answerIds);

                // Update UserQuiz with score and attempts
                $userQuiz->update([
                    'score' => $score,
                    'attempts' => $userQuiz->attempts,
                ]);


                // Check if the user's score is 60% or higher
                if ($score >= 60) {
                    // Retrieve the corresponding lesson progress

                    // Check if the lesson progress exists

                    // Mark the lesson as completed

                    // Check for pending certification
                    $pendingCertification = UserCertifications::where('user_id', Auth::user()->id)
                        ->where('course_id', $request['course_id'])
                        ->whereNull('completion_date')
                        ->first();

                    // If there is a pending certification, mark it as completed
                    if ($pendingCertification) {
                        $pendingCertification->update(['completion_date' => now()]);
                    }

                    // Create a new certification
                    $cert =  UserCertifications::create([
                        'user_id' =>  Auth::user()->id,
                        'course_id' => $course->id,
                        'course_name' => $course->title,
                        'completion_date' => now(),
                    ]);



                    // Reset pause time to 0 to allow starting the course again
                    $lessonProgress->update(['pause_time' => 0]);

                    // Add score and attempts to the response array
                    $response['score'] = $score;
                    $response['attempts'] = $userQuiz->attempts;

                    $pageTitle = 'Quiz Completed';
                    $user = auth()->user();

                    return $response;
                } else {
                    // User didn't pass, return score and attempts without redirection
                    $response['score'] = $score;
                    $response['attempts'] = $userQuiz->attempts;

                    return $response;
                }
            } else {
                // No attempts left, handle accordingly (e.g., display a message or redirect)
                // throw ValidationException::withMessages(['No attempts left' => 'No attempts left - Restart Course']);
                return $response;
            }
        } catch (\Throwable $th) {
            // Handle the exception, if needed
        }
    }



    private function calculateScore($quizId, $submittedAnswers)
    {
        // Retrieve the correct answers for the submitted quiz
        $correctAnswers = Answer::whereIn('id', $submittedAnswers)
            ->where('is_correct', true)
            ->pluck('id')
            ->toArray();

        // Retrieve the total number of questions in the lesson
        $totalQuestions = Question::where('quiz_id', $quizId)->count();

        // Calculate the user's score based on correct answers
        $userScore = (count($correctAnswers) / $totalQuestions) * 100;

        return $userScore;
    }

    public function downloadCertification(Request $request, $course_r)
    {
        $pageTitle = 'Certification';
        $user = auth()->user();
        $courseId = $request->input('course_id');
        $course = Course::find($course_r);

        // dd($courseId);
        $certification = UserCertifications::where('user_id', '=', auth()->id())->where('course_name', '=', $course->title)->first();
        if ($certification == null) {
            $certification = new UserCertifications([
                'user_id' => auth()->id(),
                'course_id' =>   $course->id,
                'course_name' =>   $course->title,
                'completion_date' =>  Carbon::now()

            ]);

            $certification->save();
        }


        $pdf = \PDF::loadView('templates/basic/generated_certification', compact('pageTitle', 'user', 'certification'));
        $pdf->setOption(['defaultFont' => 'RobotoMono-Regular']);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
        $pdfContents = $pdf->output();
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certification_Us' . $user->id . '_Crs' . $courseId . '.pdf"',
        ];

        return $pdf->stream('certification Us' . $user->id . '_Crs' . $courseId . '.pdf');
        // return response($pdfContents, 200, $headers);
    }
    public function userCertifications()
    {
        $pageTitle = 'My certifications';
        $userId = auth()->id();
        $certifications = UserCertifications::where('user_id', '=', $userId)->get();
        return view($this->activeTemplate . 'user.certifications', compact('pageTitle', 'certifications'));
    }
}
