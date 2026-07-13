<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\GroqChatController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminScholarshipController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminAdminController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\ScholarshipRichTextUploadController;

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => url('/'), 'priority' => '1.0'],
        ['loc' => route('guest.scholarships'), 'priority' => '0.9'],
        ['loc' => route('guest.about'), 'priority' => '0.7'],
        ['loc' => route('guest.services'), 'priority' => '0.7'],
    ];

    return response()->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::get('/', function () {
    $testimonials = \App\Models\Testimonial::active()->latest()->take(3)->get();
    $teamMembers = \App\Models\User::admins()->where('status', 'active')->get();
    return view('home', compact('testimonials', 'teamMembers'));
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', function (Request $request) {
    if ($request->has('ref')) {
        // تخزين ID الشخص الداعي في السيشين لمدة نصف ساعة مثلاً
        session(['referrer_id' => $request->query('ref')]);
    }
    return view('auth.register');
})->name('register')->middleware('guest');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Forgot / reset password
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');
});

// Email verification (Signed URL - without storing tokens)
Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\VerifyEmailController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('signed')
    ->middleware('throttle:6,1');

// Email verification sent page
Route::get('/email/verification-sent', function () {
    return view('auth.verification-sent');
})->name('verification.sent');


// Google OAuth
require __DIR__ . '/google.php';

// Guest pages (Pre-login)
Route::get('/scholarships', [App\Http\Controllers\GuestController::class, 'scholarships'])->name('guest.scholarships');
Route::get('/scholarships/{scholarship}', [App\Http\Controllers\GuestController::class, 'scholarshipShow'])->name('guest.scholarships.show');
Route::get('/about', [App\Http\Controllers\GuestController::class, 'about'])->name('guest.about');
Route::get('/services', [App\Http\Controllers\GuestController::class, 'services'])->name('guest.services');


/*
|--------------------------------------------------------------------------
| Admin Routes (لوحة تحكم المدراء والصلاحيات المخصصة)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {

    // 1️⃣ لوحة التحكم الرئيسية (متاحة لكل من يملك صلاحية dashboard)
    Route::middleware(['check.permission:dashboard'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    // إشعارات الأدمن والبحث الشامل (متاحة لأي أدمن مسجّل دخول)
    Route::get('/notifications/latest', [AdminDashboardController::class, 'getLatestNotifications'])->name('notifications.latest');
    Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('notifications.index');
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'globalSearch'])->name('search');

    // 2️⃣ إدارة المنح الدراسية
    Route::middleware(['check.permission:scholarships'])->group(function () {
        Route::resource('scholarships', AdminScholarshipController::class);
        Route::post('/scholarships/generate-sections', [AdminScholarshipController::class, 'generateAllSections'])->name('scholarships.generate-sections');
        Route::post('/scholarships/rich-text/upload-image', [ScholarshipRichTextUploadController::class, 'uploadImage'])->name('scholarships.rich-text.upload-image');
    });

    // 3️⃣ إدارة الطلاب
    Route::middleware(['check.permission:students'])->group(function () {
        Route::resource('students', AdminStudentController::class);
        Route::patch('students/{user}/toggle', [AdminStudentController::class, 'toggleStatus'])->name('students.toggle');
        Route::post('students/{id}/verify-email', [AdminStudentController::class, 'verifyEmail'])->name('students.verify-email');
    });

    // 4️⃣ طلبات التقديم (Orders)
    Route::middleware(['check.permission:applications'])->group(function () {
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::patch('orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    });

    // 5️⃣ الدعم الفني وتذاكر المساعدة
    Route::middleware(['check.permission:support'])->group(function () {
        Route::resource('tickets', AdminTicketController::class);
        Route::post('tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    });

    // 6️⃣ رسائل اتصل بنا وآراء العملاء (Contacts & Testimonials)
    Route::middleware(['check.permission:contacts'])->group(function () {
        Route::resource('testimonials', AdminTestimonialController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('testimonials/{testimonial}/toggle', [AdminTestimonialController::class, 'toggleStatus'])->name('testimonials.toggle');
    });

    // 7️⃣ إدارة المدراء والصلاحيات + الإعدادات العامة
    Route::middleware(['check.permission:admins'])->group(function () {
        Route::resource('admins', AdminAdminController::class);
        Route::resource('settings', AdminSettingsController::class)->only(['index', 'update']);
    });
});


/*
|--------------------------------------------------------------------------
| Student Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard')->middleware(['auth', 'verified.ensure'])->name('dashboard.')->group(function () {

    // Student Dashboard APIs
    Route::get('/my-tickets/api', [\App\Http\Controllers\DashboardApiController::class, 'myTickets'])->name('my-tickets-api');
    Route::get('/my-favorites/api', [\App\Http\Controllers\DashboardApiController::class, 'myFavorites'])->name('my-favorites-api');
    Route::get('/my-notifications/api', [\App\Http\Controllers\DashboardApiController::class, 'myNotifications'])->name('my-notifications-api');
    Route::post('/notifications/read-all', [\App\Http\Controllers\DashboardApiController::class, 'markAllNotificationsRead'])->name('notifications.read-all');

    Route::get('/student', [StudentDashboardController::class, 'student'])->name('student');
    Route::get('/scholarships', [StudentDashboardController::class, 'scholarships'])->name('scholarships');
    Route::get('/scholarships/{scholarship}', [StudentDashboardController::class, 'show'])->name('scholarships.show');
    Route::post('/scholarships/{scholarship}/favorite', [StudentDashboardController::class, 'toggleFavorite'])->name('scholarships.favorite');
    Route::get('/my-applications', [\App\Http\Controllers\UserApplicationController::class, 'index'])->name('applications');

    Route::get('/services', [StudentDashboardController::class, 'services'])->name('services');
    Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/favorites', [StudentDashboardController::class, 'favorites'])->name('favorites');
    Route::get('/notifications', [StudentDashboardController::class, 'notifications'])->name('notifications');
    Route::get('/settings', [StudentDashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [StudentDashboardController::class, 'updateSettings'])->name('settings.update');
    Route::get('/my-tickets', [StudentDashboardController::class, 'myTickets'])->name('tickets');
    Route::get('/my-tickets/{ticket}', [StudentDashboardController::class, 'ticketShow'])->name('tickets.show');
    Route::get('/communications', [StudentDashboardController::class, 'communications'])->name('communications');

    // Scholarship Premium Checkout (orders)
    Route::get('/scholarships/{scholarship}/pay', [\App\Http\Controllers\StudentScholarshipCheckoutController::class, 'show'])
        ->middleware(\App\Http\Middleware\AllowFreeModeForPayments::class)
        ->name('scholarships.pay');
    Route::post('/scholarships/{scholarship}/pay', [\App\Http\Controllers\StudentScholarshipCheckoutController::class, 'store'])
        ->middleware(\App\Http\Middleware\AllowFreeModeForPayments::class);
});


/*
|--------------------------------------------------------------------------
| Communications & Chat API Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('api/communications')->name('communications.')->group(function () {
    Route::get('/chats', [App\Http\Controllers\CommunicationsController::class, 'chats']);
    Route::get('/{id}/{type}/messages', [App\Http\Controllers\CommunicationsController::class, 'getMessages']);
    Route::post('/{id}/{type}/send', [App\Http\Controllers\CommunicationsController::class, 'sendMessage']);
    Route::post('/ai/new-chat', [App\Http\Controllers\CommunicationsController::class, 'createNewAiChat']);
    Route::post('/tickets/create', [App\Http\Controllers\CommunicationsController::class, 'createNewTicket']);
});

Route::middleware('auth')->group(function () {
    // Admin ticket actions
    Route::post('admin/tickets/{ticket}/reply', [ChatController::class, 'adminReply'])->name('admin.tickets.reply');
    Route::post('admin/tickets/{ticket}/resolve', [ChatController::class, 'resolveTicket'])->name('admin.tickets.resolve');
    Route::post('admin/tickets/{ticket}/close', [ChatController::class, 'closeTicket'])->name('admin.tickets.close');

    // Student ticket actions
    Route::post('tickets/{ticket}/reply', [ChatController::class, 'studentReply'])->name('student.tickets.reply');
    Route::post('tickets/{ticket}/ai-reply', [ChatController::class, 'aiReply'])->name('tickets.ai-reply');

    // Shared: get ticket messages (for both admin and student)
    Route::get('tickets/{ticket}/messages', [ChatController::class, 'ticketMessages'])->name('tickets.messages');
});


/*
|--------------------------------------------------------------------------
| Groq AI Chat API
|--------------------------------------------------------------------------
*/
Route::post('/api/chat', [GroqChatController::class, 'chat'])
    ->name('api.chat')
    ->middleware('throttle:groq-chat');
Route::post('/api/chat/clear', [GroqChatController::class, 'clearHistory'])
    ->name('api.chat.clear')
    ->middleware('throttle:groq-chat');
Route::post('/api/chat/clear-ban', [GroqChatController::class, 'clearBan'])
    ->name('api.chat.clear-ban')
    ->middleware('throttle:groq-chat');