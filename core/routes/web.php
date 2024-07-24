<?php

use App\Http\Controllers\User\Auth\LoginController;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
});

Route::controller('QuizController')->group(function () {
    Route::get('/quiz/{courseId}', 'index')->name('quiz.page');
    Route::get('/quiz/next/{answerId}/{questionId}', 'next')->name('quiz.next');
    Route::post('/quiz/submit', 'submit')->name('quiz.submit');
    // Route::put('/quiz/certification/download', 'downloadCertification')->name('quiz.certification.download');
});
Route::controller('QuizController')->group(function () {
    Route::get('/certification/download/{course_id}', 'downloadCertification')->name('certification.download');
    Route::get('user/certifications', 'userCertifications')->name('user.certifications');
});
Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    Route::get('courses', 'courses')->name('courses');
    Route::get('course/details/{slug}/{id}', 'courseDetails')->name('course.details');
    Route::post('course/reviews', 'loadReview')->name('course.reviews');

    Route::get('course/lessons/{slug}/{id}', 'courseLessons')->name('course.lesson');
    Route::get('lesson/asset/download/{id}', 'downloadLessonAsset')->name('lesson.asset.download');

    Route::get('category/{slug}/{id}', 'courseByCategory')->name('category.course');
    Route::post('coupon-check', 'checkCoupon')->name('coupon.check');

    Route::post('subscribe', 'subscribe')->name('subscribe');

    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');
    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
    Route::post('/update-lesson-progress', 'updateLessonProgress')->name('update.lesson.progress');
    //
});

// Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
