<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HerbDictionaryController;
use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| AmaTrung — Web Routes
|--------------------------------------------------------------------------
*/

// ── Trang chủ ──────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');


// ── Giao diện công khai ──────────────────────────────────────────

Route::get('/bai-viet', [ArticleController::class, 'index'])->name('articles.index');

// Routes cho 5 bài viết ngũ hành
Route::get('/bai-viet/ngu-hanh-kim', function () {
    return view('bai-viet.ngu-hanh', ['elementName' => 'Kim']);
});
Route::get('/bai-viet/ngu-hanh-moc', function () {
    return view('bai-viet.ngu-hanh', ['elementName' => 'Mộc']);
});
Route::get('/bai-viet/ngu-hanh-thuy', function () {
    return view('bai-viet.ngu-hanh', ['elementName' => 'Thủy']);
});
Route::get('/bai-viet/ngu-hanh-hoa', function () {
    return view('bai-viet.ngu-hanh', ['elementName' => 'Hỏa']);
});
Route::get('/bai-viet/ngu-hanh-tho', function () {
    return view('bai-viet.ngu-hanh', ['elementName' => 'Thổ']);
});

Route::get('/bai-viet/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/tu-dien-thuoc-nam', [HerbDictionaryController::class, 'index'])->name('herb-dictionary.index');
Route::get('/chinh-sach-bao-mat', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/dieu-khoan', function () {
    return view('terms');
})->name('terms');

Route::get('/ve-thay-thuoc-amatrung', function () {
    return view('about.doctor');
})->name('about.doctor');

Route::post('/lien-he', [\App\Http\Controllers\ContactMessageController::class, 'store'])->name('contact.store');

// ── AI Chatbot ──────────────────────────────────────────────────

Route::post('/api/chatbot/chat', [ChatbotController::class, 'chat'])
    ->middleware('throttle:20,1')
    ->name('chatbot.chat');


// ── Auth: Guest only (chưa đăng nhập) ─────────────────────────

Route::middleware('guest')->group(function () {
    // Đăng nhập
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Đăng ký
    Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Quên mật khẩu (OTP)
    Route::get('/forgot-password',            [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password/send-otp',  [ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp');
    Route::get('/forgot-password/verify',     [ForgotPasswordController::class, 'showVerifyOtpForm'])->name('password.verify.form');
    Route::post('/forgot-password/verify',    [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify');
    Route::post('/forgot-password/resend',    [ForgotPasswordController::class, 'resendOtp'])->name('password.resend-otp');
    Route::get('/reset-password',             [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('/reset-password',            [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});


// ── Auth: Đã đăng nhập ────────────────────────────────────────

Route::middleware('auth')->group(function () {
    // Đăng xuất
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard user
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');

    // Quản lý tài khoản cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/yeu-thich', [ProfileController::class, 'favorites'])->name('profile.favorites');
    Route::post('/profile/patient-link', [ProfileController::class, 'requestPatientLink'])->name('profile.patient-link');

    // Bình luận bài viết
    Route::post('/bai-viet/{article_id}/comments', [CommentController::class, 'store'])->name('comments.store');

    // Thích bài viết
    Route::post('/bai-viet/{article_id}/like', [ArticleController::class, 'toggleLike'])->name('articles.like');

    // Từ điển thuốc nam
    Route::get('/tu-dien-thuoc-nam/yeu-thich', [HerbDictionaryController::class, 'favorites'])->name('herb-dictionary.favorites');
    Route::get('/tu-dien-thuoc-nam/{entry:slug}', [HerbDictionaryController::class, 'show'])->name('herb-dictionary.show');
    Route::post('/tu-dien-thuoc-nam/{entry:slug}/favorite', [HerbDictionaryController::class, 'toggleFavorite'])->name('herb-dictionary.favorite');
});


// ── Admin/Staff Panel ──────────────────────────────────────────

Route::prefix('admin')
    ->middleware(['auth', 'staff'])
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Giai đoạn 4.1: Quản lý Bệnh nhân và Dược liệu
        Route::delete('patients/bulk-destroy', [\App\Http\Controllers\Admin\PatientController::class, 'bulkDestroy'])->name('patients.bulk-destroy');
        Route::get('patients/print-list', [\App\Http\Controllers\Admin\PatientController::class, 'printList'])->name('patients.print-list');
        Route::get('patients/export-excel', [\App\Http\Controllers\Admin\PatientController::class, 'exportExcel'])->name('patients.export-excel');
        Route::resource('patients', \App\Http\Controllers\Admin\PatientController::class);
        
        // Nhập dữ liệu từ hồ sơ giấy
        Route::get('patients-legacy/create', [\App\Http\Controllers\Admin\PatientController::class, 'legacyCreate'])->name('patients.legacy-create');
        Route::post('patients-legacy', [\App\Http\Controllers\Admin\PatientController::class, 'legacyStore'])->name('patients.legacy-store');
        Route::post('patients-check-duplicate', [\App\Http\Controllers\Admin\PatientController::class, 'checkDuplicate'])->name('patients.check-duplicate');
        
        // Import CSV
        Route::get('patients-import', [\App\Http\Controllers\Admin\PatientController::class, 'csvImportForm'])->name('patients.csv-import');
        Route::post('patients-import', [\App\Http\Controllers\Admin\PatientController::class, 'csvImportProcess'])->name('patients.csv-import-process');
        Route::get('patients-csv-template', [\App\Http\Controllers\Admin\PatientController::class, 'downloadCsvTemplate'])->name('patients.csv-template');

        // Quản lý kho (gộp Dược liệu + Sản phẩm hỗ trợ/Trà thảo mộc)
        Route::get('warehouse', [\App\Http\Controllers\Admin\WarehouseController::class, 'index'])->name('warehouse.index');

        // Quản lý kho mới (Phase 3)
        Route::get('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
        Route::delete('inventory/bulk-destroy', [\App\Http\Controllers\Admin\InventoryController::class, 'bulkDestroy'])->name('inventory.bulk-destroy');
        Route::get('inventory/{id}', [\App\Http\Controllers\Admin\InventoryController::class, 'show'])->name('inventory.show');
        Route::put('inventory/batch/{id}', [\App\Http\Controllers\Admin\InventoryController::class, 'updateBatch'])->name('inventory.batch.update');
        Route::post('inventory/{item_id}/batch', [\App\Http\Controllers\Admin\InventoryController::class, 'storeBatch'])->name('inventory.batch.store');
        Route::patch('inventory/batch/{id}/toggle', [\App\Http\Controllers\Admin\InventoryController::class, 'toggleBatchStatus'])->name('inventory.batch.toggle');
        Route::post('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'storeItem'])->name('inventory.store');


        Route::delete('medicinal-herbs/bulk-destroy', [\App\Http\Controllers\Admin\MedicinalHerbController::class, 'bulkDestroy'])->name('medicinal-herbs.bulk-destroy');
        Route::get('medicinal-herbs/export-excel', [\App\Http\Controllers\Admin\MedicinalHerbController::class, 'exportExcel'])->name('medicinal-herbs.export-excel');
        Route::get('medicinal-herbs/download-template', [\App\Http\Controllers\Admin\MedicinalHerbController::class, 'downloadTemplate'])->name('medicinal-herbs.download-template');
        Route::post('medicinal-herbs/import-excel', [\App\Http\Controllers\Admin\MedicinalHerbController::class, 'importExcel'])->name('medicinal-herbs.import-excel');
        Route::get('medicinal-herbs/print-list', [\App\Http\Controllers\Admin\MedicinalHerbController::class, 'printList'])->name('medicinal-herbs.print-list');
        Route::get('medicinal-herbs/{medicinal_herb}/stock-logs', [\App\Http\Controllers\Admin\MedicinalHerbController::class, 'stockLogs'])->name('medicinal-herbs.stock-logs');
        Route::resource('medicinal-herbs', \App\Http\Controllers\Admin\MedicinalHerbController::class);

        // Sản phẩm hỗ trợ/Trà thảo mộc
        Route::resource('packaged-products', \App\Http\Controllers\Admin\PackagedProductController::class);

        // Giai đoạn 4.2: Bệnh án và Kê đơn thuốc
        Route::delete('medical-records/bulk-destroy', [\App\Http\Controllers\Admin\MedicalRecordController::class, 'bulkDestroy'])->name('medical-records.bulk-destroy');
        Route::resource('medical-records', \App\Http\Controllers\Admin\MedicalRecordController::class);
        Route::get('medical-records/{medicalRecord}/print', [\App\Http\Controllers\Admin\MedicalRecordController::class, 'print'])->name('medical-records.print');
        Route::get('medical-records/{medicalRecord}/xray', [\App\Http\Controllers\Admin\MedicalRecordController::class, 'downloadXray'])->name('medical-records.xray');
        Route::get('patients/{patient}/medical-records/create', [\App\Http\Controllers\Admin\MedicalRecordController::class, 'create'])->name('patients.medical-records.create');
        
        // Nhập bệnh án cũ từ hồ sơ giấy
        Route::get('medical-records-legacy/create', [\App\Http\Controllers\Admin\MedicalRecordController::class, 'legacyCreate'])->name('medical-records.legacy-create');
        Route::post('medical-records-legacy', [\App\Http\Controllers\Admin\MedicalRecordController::class, 'legacyStore'])->name('medical-records.legacy-store');
        
        // File đính kèm bệnh án
        Route::post('medical-records/{medicalRecord}/attachments', [\App\Http\Controllers\Admin\MedicalRecordController::class, 'uploadAttachments'])->name('medical-records.attachments.upload');
        Route::get('medical-records/attachments/{attachment}/download', [\App\Http\Controllers\Admin\MedicalRecordController::class, 'downloadAttachment'])->name('medical-records.attachments.download');
        
        Route::resource('prescriptions', \App\Http\Controllers\Admin\PrescriptionController::class)->except(['edit', 'update']);
        Route::get('medical-records/{medicalRecord}/prescriptions/create', [\App\Http\Controllers\Admin\PrescriptionController::class, 'create'])->name('medical-records.prescriptions.create');
        Route::get('prescriptions/{prescription}/print', [\App\Http\Controllers\Admin\PrescriptionController::class, 'print'])->name('prescriptions.print');
        Route::post('prescriptions/{prescription}/dispense', [\App\Http\Controllers\Admin\PrescriptionController::class, 'dispense'])->name('prescriptions.dispense');

        // Quản lý Dịch vụ trị liệu
        Route::get('treatment-templates', [\App\Http\Controllers\Admin\TreatmentTemplateController::class, 'index'])->name('treatment-templates.index');

        // Quản lý Bài thuốc mẫu
        Route::delete('sample-prescriptions/bulk-destroy', [\App\Http\Controllers\Admin\SamplePrescriptionController::class, 'bulkDestroy'])->name('sample-prescriptions.bulk-destroy');
        Route::resource('sample-prescriptions', \App\Http\Controllers\Admin\SamplePrescriptionController::class);

        // Quản lý Dịch vụ trị liệu
        Route::resource('therapy-services', \App\Http\Controllers\Admin\TherapyServiceController::class);


        // Giai đoạn 4.3: Quản lý Bài viết và Bình luận
        Route::delete('articles/bulk-destroy', [\App\Http\Controllers\Admin\ArticleController::class, 'bulkDestroy'])->name('articles.bulk-destroy');
        Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);
        Route::get('herb-dictionary/download-template', [\App\Http\Controllers\Admin\HerbDictionaryController::class, 'downloadTemplate'])->name('herb-dictionary.download-template');
        Route::post('herb-dictionary/import', [\App\Http\Controllers\Admin\HerbDictionaryController::class, 'import'])->name('herb-dictionary.import');
        Route::delete('herb-dictionary/bulk-destroy', [\App\Http\Controllers\Admin\HerbDictionaryController::class, 'bulkDestroy'])->name('herb-dictionary.bulk-destroy');
        Route::post('herb-dictionary/{herbDictionary}/images', [\App\Http\Controllers\Admin\HerbDictionaryController::class, 'storeImage'])->name('herb-dictionary.images.store');
        Route::delete('herb-dictionary/{herbDictionary}/images/{image}', [\App\Http\Controllers\Admin\HerbDictionaryController::class, 'destroyImage'])->name('herb-dictionary.images.destroy');
        Route::get('herb-dictionary/export-excel', [\App\Http\Controllers\Admin\HerbDictionaryController::class, 'exportExcel'])->name('herb-dictionary.export-excel');
        Route::resource('herb-dictionary', \App\Http\Controllers\Admin\HerbDictionaryController::class)
             ->only(['index', 'create', 'store', 'edit', 'update'])
             ->parameters(['herb-dictionary' => 'herbDictionary']);
        Route::resource('comments', \App\Http\Controllers\Admin\CommentController::class)->only(['index', 'update', 'destroy']);
        Route::resource('contact-messages', \App\Http\Controllers\Admin\ContactMessageController::class)->only(['index', 'update', 'destroy']);

        // Quản lý Lịch hẹn
        Route::get('appointments', [\App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('appointments/day/{date}', [\App\Http\Controllers\Admin\AppointmentController::class, 'dayView'])->name('appointments.day-view');
        Route::get('appointments/date/{date}', [\App\Http\Controllers\Admin\AppointmentController::class, 'getPatientsByDate'])->name('appointments.by-date');
        Route::post('appointments', [\App\Http\Controllers\Admin\AppointmentController::class, 'store'])->name('appointments.store');
        Route::delete('appointments/{appointment}', [\App\Http\Controllers\Admin\AppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::patch('appointments/{appointment}/status', [\App\Http\Controllers\Admin\AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

        // Giai đoạn 4: AI gợi ý tham khảo (API nội bộ - an toàn)
        Route::middleware('permission:use_ai_suggestion')->group(function () {
            Route::post('api/ai-preliminary-assessment/follow-up-questions', [\App\Http\Controllers\Admin\AiSuggestionController::class, 'followUpQuestions'])
                 ->name('ai.preliminary-assessment.follow-up-questions');
            Route::post('api/ai-preliminary-assessment', [\App\Http\Controllers\Admin\AiSuggestionController::class, 'preliminaryAssessment'])
                 ->name('ai.preliminary-assessment');
            Route::post('api/ai-preliminary-assessment/apply-diagnosis', [\App\Http\Controllers\Admin\AiSuggestionController::class, 'applyDiagnosis'])
                 ->middleware('permission:medical_records.edit')
                 ->name('ai.preliminary-assessment.apply-diagnosis');
            Route::post('api/ai-suggest', [\App\Http\Controllers\Admin\AiSuggestionController::class, 'suggest'])
                 ->name('ai.suggest');
            Route::post('api/ai-suggest/log-status', [\App\Http\Controllers\Admin\AiSuggestionController::class, 'updateLogStatus'])
                 ->name('ai.suggest.log-status');
        });

        // Quản lý người dùng & Phân quyền
        Route::middleware('admin')->group(function () {
            Route::delete('users/bulk-destroy', [\App\Http\Controllers\Admin\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
            Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::patch('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
        });
    });
