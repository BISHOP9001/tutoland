<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\CoursePurchase;
use App\Models\Frontend;
use App\Models\GatewayCurrency;
use App\Models\Language;
use App\Models\Lesson;
use App\Models\Page;
use App\Models\Review;
use App\Models\Subscriber;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\UserLessonProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function index()
    {
        return Redirect::away('/user/login');
        $pageTitle = 'Home';
        $sections  = Page::where('tempname', $this->activeTemplate)->where('slug', '/')->first();
        return view($this->activeTemplate . 'home', compact('pageTitle', 'sections'));
    }

    public function courses()
    {
        $pageTitle = 'All Courses';

        $premiumCourses = Course::active()->whereHas('category', function ($q) {
            $q->active();
        })->premium()->withSum(['lessons as total_duration' => function ($lesson) {
            $lesson->active();
        }], 'video_duration')->whereHas('lessons')->orderBy('id', 'desc')->get();

        $freeCourses = Course::active()->whereHas('category', function ($q) {
            $q->active();
        })->free()->withSum(['lessons as total_duration' => function ($lesson) {
            $lesson->active();
        }], 'video_duration')->whereHas('lessons')->orderBy('id', 'desc')->get();

        $page     = Page::where('tempname', $this->activeTemplate)->where('slug', 'courses')->first();
        $sections = $page->secs;

        return view($this->activeTemplate . 'courses', compact('pageTitle', 'premiumCourses', 'freeCourses', 'sections'));
    }

    public function courseDetails($slug, $id)
    {
        $pageTitle = 'Course Details';
        $coursePurchase = null;
        $course    = Course::with(['reviews.user', 'sections' => function ($section) {
            $section->active();
        }, 'sections.lessons' => function ($lesson) {
            $lesson->active();
        }]);

        if (!(auth()->check() && auth()->user()->coursePurchases()->where('course_id', $id)->exists())) {
            $course = $course->whereHas('category', function ($q) {
                $q->active();
            });
        }
        if (!auth()->user()->coursePurchases()->where('course_id', $id)->exists()) {
            /*  Enrolled courses should appear on the user dashboard */
            $coursePurchase                   = new CoursePurchase();
            $coursePurchase->user_id          = auth()->user()->id;
            $coursePurchase->course_id        = $id;
            $coursePurchase->status        = CoursePurchase::PENDING;
            $coursePurchase->purchased_amount = 0;
            $coursePurchase->coupon_amount    = 0;
            $coursePurchase->save();
        } else {
            $coursePurchase = auth()->user()->coursePurchases()->where('course_id', $id)->first();
        }

        $course = $course->whereHas('lessons')->findOrFail($id);

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        $seoContents['keywords']           = $course->meta_keyword;
        $seoContents['social_title']       = $course->title;
        $seoContents['description']        = $course->short_description;
        $seoContents['social_description'] = $course->short_description;
        $seoContents['image']              = getImage(getFilePath('course') . '/' . $course->image);
        $seoContents['image_size']         = getFileSize('course');

        return view($this->activeTemplate . 'course_details', compact('pageTitle', 'course', 'seoContents', 'gatewayCurrency', 'coursePurchase'));
    }

    public function loadReview(Request $request)
    {
        $rules = [
            'course_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()]);
        }

        $reviews = Review::with('user')->where('course_id', $request->course_id);

        if (!$request->last_id == 0) {
            $reviews->where('id', '<', $request->last_id);
        }

        $reviews = $reviews->orderBy('id', 'desc')->limit(5)->get();

        $firstReview = Review::where('course_id', $request->course_id)->first();

        return view($this->activeTemplate . 'partials.student_review', compact('reviews', 'firstReview'));
    }

    public function courseByCategory($slug, $id)
    {
        $category  = Category::active()->findOrFail($id);
        $pageTitle = $category->name . ' courses';

        $courses = Course::active()->whereHas('category', function ($q) {
            $q->active();
        })->where('category_id', $category->id)->withSum(['lessons as total_duration' => function ($lesson) {
            $lesson->active();
        }], 'video_duration')->whereHas('lessons')->orderBy('id', 'desc')->get();

        return view($this->activeTemplate . 'courses_by_category', compact('pageTitle', 'category', 'courses'));
    }

    public function checkCoupon(Request $request)
    {
        $rules = [
            'coupon_code'  => 'required',
            'course_price' => 'required|numeric|gt:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()]);
        }

        $coupon = Coupon::active()->where('code', $request->coupon_code)->first();

        if (!$coupon) {
            return response()->json(['status' => 'error', 'message' => 'Invalid coupon code!']);
        }

        $general = gs();

        if ($coupon->minimum_spend > $request->course_price) {
            return response()->json(['status' => 'error', 'message' => 'You need to spend at least ' . showAmount($coupon->minimum_spend) . " $general->cur_text to use this coupon."]);
        }

        if ($coupon->maximum_spend < $request->course_price) {
            return response()->json(['status' => 'error', 'message' => 'You can use this coupon for purchases up to a maximum of ' . showAmount($coupon->maximum_spend) . " $general->cur_text"]);
        }

        $discount      = $coupon->discount_type ? $coupon->discount_amount * $request->course_price / 100 : $coupon->discount_amount;
        $payableAmount = $request->course_price - $discount;

        $message = 'Coupon applied ' . getAmount($coupon->discount_amount) . ($coupon->discount_type ? '%' : " $general->cur_text");

        return response()->json(['status' => 'success', 'message' => $message, 'payable_amount' => $payableAmount > 0 ? getAmount($payableAmount) : 0, 'coupon_discount' => $discount]);
    }

    public function courseLessons($slug, $id)
    {
        $pageTitle     = 'Watch';
        $currentLesson = Lesson::active()->with('course.sections.lessons')->whereHas('course', function ($course) {
            $course->active();
        })->with('course')->findOrFail($id);

        if (!lessonPermission($currentLesson)) {
            $notify[] = ['error', 'Please purchase the course for watch the video!'];
            return back()->withNotify($notify);
        }

        $course = $currentLesson->course()->with(['sections' => function ($section) {
            $section->active();
        }, 'sections.lessons' => function ($lesson) {
            $lesson->active();
        }])->first();

        $userReview = '';
        if (auth()->check()) {
            $userReview = Review::where('course_id', $course->id)->where('user_id', auth()->id())->first();
        }
        // Look for user lesson progress
        $userLessonProgress = UserLessonProgress::where('user_id', auth()->id())->where('lesson_id', $currentLesson->id)->first();
        $user_id = auth()->id();
        if (!$userLessonProgress) {
            $userLessonProgress = new UserLessonProgress([
                'user_id'    => $user_id,
                'lesson_id'  => $currentLesson->id,
                'pause_time' => 0,
                'lesson_status' => 'pending',
            ]);
            $userLessonProgress->save();
        }
        $currentLesson->increment('views');

        return view($this->activeTemplate . 'watch', compact('pageTitle', 'currentLesson', 'user_id', 'course', 'userReview', 'userLessonProgress'));
    }

    public function downloadLessonAsset($id)
    {
        $lesson = Lesson::active()->findOrFail($id);

        if (!$lesson->asset_path) {
            abort(404);
        }

        if (!lessonPermission($lesson)) {
            $notify[] = ['error', 'Please purchase the course for download asset'];
            return back()->withNotify($notify);
        }

        $file = getAssetPath($lesson);

        $title = slug($lesson->title . ' assets');
        $ext   = pathinfo($file, PATHINFO_EXTENSION);
        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . 'zip');
        return readfile($file);
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|unique:subscribers,email',
            ],
            [
                "email.unique" => "You've already joined our subscriber list",
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'code'    => 200,
                'status'  => 'error',
                'message' => $validator->errors()->all(),
            ]);
        }

        $subscribe        = new Subscriber();
        $subscribe->email = $request->email;
        $subscribe->save();

        $notify = 'Thank you, we will notice you our latest news';

        return response()->json([
            'code'    => 200,
            'status'  => 'success',
            'message' => $notify,
        ]);
    }

    public function pages($slug)
    {
        $page      = Page::where('tempname', $this->activeTemplate)->where('slug', $slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections  = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle', 'sections'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        $user      = auth()->user();
        return view($this->activeTemplate . 'contact', compact('pageTitle', 'user'));
    }

    public function contactSubmit(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug, $id)
    {
        $policy    = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        return view($this->activeTemplate . 'policy', compact('policy', 'pageTitle'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function cookieAccept()
    {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy()
    {
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();
        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view($this->activeTemplate . 'maintenance', compact('pageTitle', 'maintenance'));
    }

    public function updateLessonProgress(Request $request)
    {
        $lessonId = $request->input('lesson_id');
        $pauseTime = $request->input('pause_time');
        $status = $request->input('lesson_status');
        $user_id = $request->input('user_id');

        $userLessonProgress = UserLessonProgress::where('user_id', $user_id)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$userLessonProgress) {
            $userLessonProgress = new UserLessonProgress();
            $userLessonProgress->user_id = $user_id;
            $userLessonProgress->lesson_id = $lessonId;
            $userLessonProgress->pause_time = $pauseTime;
            $userLessonProgress->lesson_status = $status;

            $userLessonProgress->save();
        } else {
            $userLessonProgress->pause_time = $pauseTime;
            $userLessonProgress->lesson_status = $status;
            $userLessonProgress->save();
        }


        return response()->json(['status' => 'success', 'message' => 'Lesson progress updated successfully']);
    }
}
