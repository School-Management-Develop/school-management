<?php

use App\Http\Controllers\Auth\SimpleLoginController;
use App\Http\Controllers\Backend\BorrowController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\GroupController;
use App\Http\Controllers\Backend\ItemController;
use App\Http\Controllers\Backend\StudentController;
use App\Http\Controllers\Backend\StudentRegisterController;
use App\Http\Controllers\Backend\SubmissionController;
use App\Http\Controllers\Backend\TeacherController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('student.register');
});
Route::get('/register/check-group', function () {
    $exists = \App\Models\Group::where('group_id', request('group_id'))->exists();
    return response()->json(['exists' => $exists]);
})->name('register.checkGroup');



Route::get('/language/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'kh'], true)) {
        abort(400);
    }

    session()->put('locale', $locale);

    return redirect()->back();
})->name('language.switch');

Route::get('/loading', function () {
    return view('backend.loading'); // redirects to /dashboard after loading
})->name('loading.page');
/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::get('/login', [SimpleLoginController::class, 'show'])->name('login');
Route::post('/login', [SimpleLoginController::class, 'login'])->name('login.post');

Route::post('/logout', [SimpleLoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Student registration (public)
|--------------------------------------------------------------------------
*/

Route::get('/policy', function () {
    return view('backend.page.Policy.index');
})->name('policy.index');

Route::get('/register-student', [StudentRegisterController::class, 'create'])->name('student.register');
Route::post('/register-student', [StudentRegisterController::class, 'store'])->name('student.register.store');
Route::post('/register-student/submit', [SubmissionController::class, 'store'])->name('submissions.store.public');

Route::get('/register/check-phone', [StudentRegisterController::class, 'checkPhone'])
    ->name('register.checkPhone');

Route::get('/register/check-student-name', [StudentRegisterController::class, 'checkStudentName'])
    ->name('register.checkStudentName');

/*
|--------------------------------------------------------------------------
| Dashboard redirect
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/dashboard', function () {
    $role = strtolower(auth()->user()->role ?? 'student');

    return in_array($role, ['admin', 'staff'])
        ? redirect()->route('admin.dashboard')
        : redirect()->route('student.register');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/profile/photo', [ProfileController::class, 'storePhoto'])->name('profile.photo.store');
    Route::post('/profile/photo/update', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin + Staff
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,staff'])
    ->prefix('admin')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        /*
        |--------------------------------------------------------------------------
        | Borrows
        |--------------------------------------------------------------------------
        */
        Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows.index');
        Route::post('/borrows/borrow', [BorrowController::class, 'storeBorrow'])->name('borrows.borrow');
        Route::post('/borrows/return', [BorrowController::class, 'storeReturn'])->name('borrows.return');
        Route::put('/borrows/update', [BorrowController::class, 'update'])->name('borrows.update');
        Route::post('/borrows/{borrow}/undo-return', [BorrowController::class, 'undoReturn'])->name('borrows.undoReturn');
        Route::delete('/borrows/{borrow}', [BorrowController::class, 'destroy'])->name('borrows.destroy');
        Route::get('/borrows/trashed', [BorrowController::class, 'trashed'])->name('borrows.trashed');
        Route::post('/borrows/{id}/restore', [BorrowController::class, 'restore'])->name('borrows.restore');
        Route::patch('/borrows/{borrow}/call-status', [BorrowController::class, 'updateCallStatus'])
            ->name('borrows.overdue.call-status');

        Route::get('/late-returns', [BorrowController::class, 'lateReturns'])->name('borrows.late_returns');
        Route::get('/overdue-borrows', [BorrowController::class, 'overdueBorrows'])->name('borrows.overdue');
        Route::get('/item-history', [BorrowController::class, 'itemHistory'])->name('borrows.history');

        /*
        |--------------------------------------------------------------------------
        | Items
        |--------------------------------------------------------------------------
        */
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::get('/items/trashed', [ItemController::class, 'trashed'])->name('items.trashed');
        Route::get('/items/action-logs', [ItemController::class, 'actionLogs'])->name('items.action_logs');
        Route::get('/items/{itemid}', [ItemController::class, 'show'])->name('items.show');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::put('/items/{itemid}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{itemid}', [ItemController::class, 'destroy'])->name('items.destroy');
        Route::patch('/items/{itemid}/restore', [ItemController::class, 'restore'])->name('items.restore');

        /*
        |--------------------------------------------------------------------------
        | Groups
        |--------------------------------------------------------------------------
        */
        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
        Route::delete('/groups/{group_id}', [GroupController::class, 'destroy'])->name('groups.destroy');

        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student_id}', [StudentController::class, 'show'])->name('students.show');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{student_id}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student_id}', [StudentController::class, 'destroy'])->name('students.destroy');

        /*
        |--------------------------------------------------------------------------
        | teachers
        |--------------------------------------------------------------------------
        */
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

        /*
        |--------------------------------------------------------------------------
        | Submissions
        |--------------------------------------------------------------------------
        */
        Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');

        Route::post('/submissions/{submission}/approve', [SubmissionController::class, 'approve'])->name('submissions.approve');
        Route::delete('/submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
        Route::post('/submissions/{submission}/go-manage', [SubmissionController::class, 'goManage'])->name('submissions.go_manage');

        Route::post('/submissions/{id}/change-group', [SubmissionController::class, 'confirmGroupChange'])
            ->name('submissions.changeGroup');

        Route::post('/submissions/{id}/skip-group-change', [SubmissionController::class, 'skipGroupChange'])
            ->name('submissions.skipGroupChange');

        Route::post('/submissions/{id}/add-student', [SubmissionController::class, 'addStudent'])
            ->name('submissions.addStudent');

        Route::post('/submissions/{id}/approve-borrow', [SubmissionController::class, 'approveBorrow'])
            ->name('submissions.approveBorrow');

        Route::delete('/submissions/{id}/remove', [SubmissionController::class, 'remove'])
            ->name('submissions.remove');

        Route::post('/submissions/cancel-all', [SubmissionController::class, 'cancelAll'])
            ->name('submissions.cancelAll');

        Route::patch('users/{user}/telegram', [UserController::class, 'updateTelegram'])->name('users.telegram.update');
        Route::delete('users/{user}/telegram', [UserController::class, 'removeTelegram'])->name('users.telegram.remove');
    });

/*
|--------------------------------------------------------------------------
| Admin only
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
        Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    });
