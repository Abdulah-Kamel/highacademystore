<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamReportController;
use App\Http\Controllers\Admin\ExamStudentController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\MatreialPdfController;
use App\Http\Controllers\Admin\MatreialVideoController;
use App\Http\Controllers\Admin\MoneyController;
use App\Http\Controllers\Admin\OldStudentController;
use App\Http\Controllers\Admin\OnlineStudentsContoller;
use App\Http\Controllers\Admin\OnlineStudentsController;
use App\Http\Controllers\Admin\ReadNotificationController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Student\StudentMatreialPdfController;
use App\Http\Controllers\Student\StudentMatreialVideoController;
use App\Http\Controllers\Student\StudentMatreialVideoDetailsController;
use App\Http\Controllers\Student\StudentSettingsController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Middleware\MyAuth;
use App\Http\Middleware\StudentAuth;
use Illuminate\Support\Facades\Route;

Route::middleware(MyAuth::class)->group(function () {
    Route::resource('dashboard', DashBoardController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('grades', GradeController::class);
    Route::resource('sessions', SessionController::class);
    Route::resource('students', StudentController::class);
    Route::resource('exams', ExamController::class);
    Route::resource('exams-students', ExamStudentController::class);
    Route::resource('exams-reports', ExamReportController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('money', MoneyController::class);
    Route::resource('old-students', OldStudentController::class);
    Route::resource('chapters', ChapterController::class);
    Route::resource('material-pdfs', MatreialPdfController::class);
    Route::resource('material-videos', MatreialVideoController::class);
    Route::resource('online-students', OnlineStudentsContoller::class);
});
Route::middleware(StudentAuth::class)->group(function () {
    Route::resource('student_dashboard', StudentDashboardController::class);
    Route::resource('students_material-pdfs', StudentMatreialPdfController::class);
    Route::resource('students_material-videos', StudentMatreialVideoController::class);
    Route::resource('students_material-videos-details', StudentMatreialVideoDetailsController::class);
    Route::post('student/settings', [StudentSettingsController::class, 'change_password'])->name('change_password');
    Route::get('student/settings/form', [StudentSettingsController::class, 'get_settings_form'])->name('get_settings_form');
    Route::get('notifications/read', [ReadNotificationController::class, 'read_notifications'])->name('read_notifications');
    Route::get('notifications/increase', [ReadNotificationController::class, 'increase_notification_icon'])->name('increase_notification_icon');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login/check/admin', 'checkLoginAdmin')->name('loginAdmin');
    Route::post('/login/check/student', 'checkLoginStudent')->name('loginStudent');
    Route::get('/logout', 'logoutAdmin')->name('logout');
    Route::get('/', 'getFormLoginAdmin')->name('loginFormAdmin');
    Route::get('/login/student', 'getFormLoginStudent')->name('loginFormStudent');
});
