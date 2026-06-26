<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\MasterData\RoleController;
use App\Http\Controllers\MasterData\JabatanController;
use App\Http\Controllers\MasterData\StatusMemberController;
use App\Http\Controllers\MasterData\StatusProyekController;
use App\Http\Controllers\MasterData\StatusTaskController;
use App\Http\Controllers\MasterData\PriorityController;
use App\Http\Controllers\MasterData\TipeController;
use App\Http\Controllers\MasterData\KategoriController;
use App\Http\Controllers\MasterData\SumberKlienController;
use App\Http\Controllers\MasterData\JenisTransaksiController;
use App\Http\Controllers\MasterData\SkillController;
use App\Http\Controllers\MasterData\ActivityTypeController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\TaskActivityController;
use App\Http\Controllers\SubTaskActivityController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ReportProjectController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\ScrumController;
use App\Http\Controllers\ScrumUpdateController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\WorkSettingController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

// TAMBAHKAN KODE INI
Route::get('/', function () {
    return redirect()->route('login');
});

Route::post(
    '/scrum/start-from-login',
    [ScrumController::class, 'startFromLogin']
)->name('scrum.start.login');

Route::get(
    '/scrum/start/{scrum}',
    [ScrumController::class, 'showGuest']
)->name(
    'scrum.guest.show'
);

Route::get(
    '/forgot-password',
    [AuthController::class, 'showForgotPassword']
)->name('password.forgot');

Route::post(
    '/forgot-password',
    [AuthController::class, 'resetForgotPassword']
)->name('password.forgot.store');

Route::middleware('guest')->group(function () {

    Route::get(
        '/login',
        [AuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [AuthController::class, 'login']
    )->name('login.store');
});

Route::post('/tasks/{task}/note-attachment', [TaskController::class, 'noteAttachment'])
    ->name('tasks.noteAttachment');

Route::get('/tasks/download-file', [App\Http\Controllers\TaskController::class, 'downloadFile'])->name('tasks.download-file');

Route::post('/subtasks/{subtask}/activities', [SubTaskActivityController::class, 'store'])
    ->name('subtasks.activities.store');

// Rute untuk upload file di Subtask
Route::post('/subtasks/{subtask}/note-attachment', [SubtaskController::class, 'noteAttachment'])
    ->name('subtasks.noteAttachment');

// Rute untuk download file di Subtask
Route::get('/subtasks/download-file', [SubtaskController::class, 'downloadFile'])
    ->name('subtasks.downloadFile');

    // Tambahkan rute ini untuk menangani inline-update lewat AJAX
Route::patch('/subtasks/{subtask}/inline-update', [SubtaskController::class, 'inlineUpdate'])
    ->name('subtasks.inline-update');
/*

|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/register/invite/{token}',
    [InvitationController::class, 'registerForm']
)->name('invite.register');

Route::post(
    '/register/invite/{token}',
    [InvitationController::class, 'register']
)->name('invite.register.store');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class,'show']
    )->name(
        'profile.show'
    );

    Route::get(
        '/profile/edit',
        [ProfileController::class,'edit']
    )->name(
        'profile.edit'
    );

    Route::put(
        '/profile',
        [ProfileController::class,'update']
    )->name(
        'profile.update'
    );

    Route::get(
        '/change-password',
        [ProfileController::class,'passwordForm']
    )->name(
        'profile.password'
    );

    Route::put(
        '/change-password',
        [ProfileController::class,'changePassword']
    )->name(
        'profile.password.update'
    );

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Bisa Diakses Semua Role Termasuk Member)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    )->name('logout');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/reset',[ChatController::class,'reset'])->name('chat.reset');

    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show')
        ->where('task', '[0-9]+'); // <-- Tambahkan regex ini agar hanya menerima angka

    Route::get('/subtasks/{subtask}',[SubtaskController::class, 'show'])
        ->name('subtasks.show')
        ->where('subtask', '[0-9]+');
});

/*
|--------------------------------------------------------------------------
| Task Activity
|--------------------------------------------------------------------------
*/
Route::post(
    '/tasks/{task}/activities',
    [TaskActivityController::class,'store']
)->name(
    'tasks.activities.store'
);


/*
|--------------------------------------------------------------------------
| Superadmin Only (Role = 1)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:1'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Invitations
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'invitations',
        InvitationController::class
    )->except([
        'show',
        'edit',
        'update'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Work Settings
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/work-settings',
        [WorkSettingController::class, 'index']
    )->name('work-settings.index');

    Route::put(
        '/work-settings',
        [WorkSettingController::class, 'update']
    )->name('work-settings.update');

    /*
    |--------------------------------------------------------------------------
    | Master Data
    |--------------------------------------------------------------------------
    */

    Route::prefix('master-data')->group(function () {

        Route::resource(
            'roles',
            RoleController::class
        );

        Route::resource(
            'jabatans',
            JabatanController::class
        );

        Route::resource(
            'status-members',
            StatusMemberController::class
        );

        Route::resource(
            'status-proyeks',
            StatusProyekController::class
        );

        Route::resource(
            'status-tasks',
            StatusTaskController::class
        );

        Route::resource(
            'priorities',
            PriorityController::class
        );

        Route::resource(
            'tipes',
            TipeController::class
        );

        Route::resource(
            'kategoris',
            KategoriController::class
        );

        Route::resource(
            'sumber-kliens',
            SumberKlienController::class
        );

        Route::resource(
            'jenis-transaksis',
            JenisTransaksiController::class
        );

        Route::resource(
            'skills',
            SkillController::class
        );

        Route::resource(
            'activity-types',
            ActivityTypeController::class
        );
    });
});

/*
|--------------------------------------------------------------------------
| Superadmin + Project Manager (Role = 1, 2)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:1,2'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'projects',
        ProjectController::class
    );

    Route::resource(
        'clients',
        ClientController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Tasks (Fungsi Show Dikecualikan Karena Sudah Dipindah ke Atas)
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'tasks',
        TaskController::class
    )->except(['show']); // <-- Ditambahkan agar tidak bentrok dengan route show di atas

    Route::patch(
        '/tasks/{task}/inline-update',
        [TaskController::class, 'inlineUpdate']
    )->name(
        'tasks.inline-update'
    );

    Route::resource(
        'subtasks',
        SubtaskController::class
    )->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Finance
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'transactions',
        TransactionController::class
    )->except([
        'show'
    ]);

    Route::resource(
        'payments',
        PaymentController::class
    )->only([
        'index',
        'show'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'members',
        MemberController::class
    );

    Route::get(
        '/attendances',
        [AttendanceController::class, 'index']
    )->name(
        'attendances.index'
    );

    /*
    |--------------------------------------------------------------------------
    | Scrum
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'scrums',
        ScrumController::class
    );

    Route::post(
        '/scrums/{scrum}/save',
        [ScrumUpdateController::class, 'saveScrum']
    )->name(
        'scrums.save'
    );

    Route::get(
        '/scrum',
        [ScrumController::class, 'today']
    )->name(
        'scrum.today'
    );

    Route::post(
        '/scrum/save',
        [ScrumController::class, 'save']
    )->name(
        'scrum.save'
    );

    Route::get(
        '/scrum/history',
        [ScrumController::class, 'history']
    )->name(
        'scrum.history'
    );

    Route::get(
        '/scrum/history/{scrum}',
        [ScrumController::class, 'historyShow']
    )->name(
        'scrum.history.show'
    );

    /*
    |--------------------------------------------------------------------------
    | Overtime Approval
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/overtimes/approvals',
        [OvertimeController::class,'approvals']
    )->name(
        'overtimes.approvals'
    );

    Route::post(
        '/overtimes/{id}/approve',
        [OvertimeController::class,'approve']
    )->name(
        'overtimes.approve'
    );

    Route::post(
        '/overtimes/{id}/reject',
        [OvertimeController::class,'reject']
    )->name(
        'overtimes.reject'
    );

    Route::prefix('reports')->name('reports.')->group(function () {
    // Halaman UI filter laporan project
        Route::get('/projects', [ReportProjectController::class, 'index'])->name('projects.index');
    
    // Proses generate dan download PDF
        Route::get('/projects/download', [ReportProjectController::class, 'download'])->name('projects.download');
    });
});

/*
|--------------------------------------------------------------------------
| Project Manager + Member (Role = 2, 3)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:2,3'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Check In
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/check-in',
        [CheckInController::class,'index']
    )->name(
        'checkin.index'
    );

    Route::post(
        '/check-in/start',
        [CheckInController::class,'checkIn']
    )->name(
        'checkin.start'
    );

    Route::post(
        '/check-in/end',
        [CheckInController::class,'checkOut']
    )->name(
        'checkin.end'
    );

    /*
    |--------------------------------------------------------------------------
    | My Attendance
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/my-attendance',
        [AttendanceController::class,'myAttendance']
    )->name(
        'my-attendance.index'
    );
    
    /*
    |--------------------------------------------------------------------------
    | My Task
    |--------------------------------------------------------------------------
    */
    Route::get(
        '/my-task',
        [TaskController::class,'myTask']
    )->name(
        'my-task.index'
    );

    /*
    |--------------------------------------------------------------------------
    | Overtime
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/overtimes',
        [OvertimeController::class,'index']
    )->name(
        'overtimes.index'
    );

    Route::post(
        '/overtimes/start',
        [OvertimeController::class,'start']
    )->name(
        'overtimes.start'
    );

    Route::post(
        '/overtimes/stop',
        [OvertimeController::class,'stop']
    )->name(
        'overtimes.stop'
    );

    Route::get(
        '/overtimes/history',
        [OvertimeController::class,'history']
    )->name(
        'overtimes.history'
    );

});