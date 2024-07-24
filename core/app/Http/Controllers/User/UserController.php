<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\CoursePurchase;
use App\Models\Review;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $myCourses = CoursePurchase::with(['course' => function ($course) {
            $course->withCount(['lessons' => function ($lesson) {
                $lesson->active();
            }])->withSum(['lessons as total_duration' => function ($lesson) {
                $lesson->active();
            }], 'video_duration');
        }, 'course.lessons' => function ($lesson) {
            $lesson->active();
        }])->where('user_id', auth()->id())->orderBy('id', 'desc')->limit(10)->get();
        // }])->where('user_id', auth()->id())->where('status', CoursePurchase::APPROVED)->orderBy('id', 'desc')->limit(10)->get();
        $widget['total_purchased']      = CoursePurchase::where('user_id', auth()->id())->count();
        $widget['total_review']         = Review::where('user_id', auth()->id())->count();
        $widget['total_support_ticket'] = SupportTicket::where('user_id', auth()->id())->count();

        $myReviews = Review::where('user_id', auth()->id())->pluck('course_id')->toArray();

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'myCourses', 'widget', 'myReviews'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Payment History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function attachmentDownload($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general   = gs();
        $title     = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype  = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function reviews()
    {
        $pageTitle = 'My Reviews';
        $myReviews = Review::with('course')->where('user_id', auth()->id())->paginate(getPaginate());
        return view($this->activeTemplate . 'user.reviews', compact('pageTitle', 'myReviews'));
    }
}
