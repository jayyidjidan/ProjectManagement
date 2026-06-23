<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\Proyeks;
use App\Models\Members;
use App\Models\Priority;
use App\Models\StatusTasks;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // 1. LOGIKA AUTO-UPDATE OVERDUE
        Task::whereNotNull('deadline_task')
            ->whereDate('deadline_task', '<', Carbon::today())
            ->whereNotIn('id_status', [6, 8, 9])
            ->update(['id_status' => 9]);

        // Mengambil parameter input dari request / URL query string
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $statusFilter = $request->input('status');
        $projectId = $request->input('project_id'); 
        
        // TANGKAP KEYWORD PENCARIAN GLOBAL DI SINI
        $keyword = $request->input('q');

        // 2. MENCARI DATA PROJECT JIKA SEDANG DI-FILTER (Untuk Headline Dinamis)
        $currentProject = null;
        if ($projectId) {
            $currentProject = Proyeks::find($projectId); 
        }

        // 3. QUERY UTAMA TASKS DENGAN FILTER + SEARCH
        $tasks = Task::with(['project', 'priority', 'status', 'assignees'])
            ->when($projectId, function ($query) use ($projectId) {
                $query->where('id_proyek', $projectId);
            })
            ->when($statusFilter, function ($query) use ($statusFilter) {
                $query->whereHas('status', function ($q) use ($statusFilter) {
                    $q->where('status_name', $statusFilter);
                });
            })
            // TAMBAHKAN LOGIKA SEARCH DI SINI
            ->when($keyword, function ($query, $keyword) {
                // Sesuaikan 'nama_task' dengan nama kolom judul tugas di databasemu
                return $query->where('nama_task', 'like', "%{$keyword}%");
                
                // Opsional: Jika ingin cari berdasarkan deskripsi task juga, gunakan ini:
                // return $query->where('nama_task', 'like', "%{$keyword}%")
                //              ->orWhere('deskripsi_task', 'like', "%{$keyword}%");
            })
            ->orderBy($sort, $direction)
            ->get(); // Karena menggunakan get(), semua filter koleksi di bawah otomatis ikut tersaring

        // 4. KOLEKSI TASK UNTUK SUMMARY CARD & TABEL (Otomatis ikut ter-filter)
        $planning = $tasks->filter(fn ($task) => $task->status?->status_name === 'Planning');
        $ongoing  = $tasks->filter(fn ($task) => $task->status?->status_name === 'On Going');
        $reviewed = $tasks->filter(fn ($task) => $task->status?->status_name === 'Reviewed');
        $finished = $tasks->filter(fn ($task) => $task->status?->status_name === 'Finished');
        $canceled = $tasks->filter(fn ($task) => $task->status?->status_name === 'Canceled');
        $overdue  = $tasks->filter(fn ($task) => $task->status?->status_name === 'Overdue');

        // 5. DATA LOOKUP UNTUK DROPDOWN FORM & INLINE EDIT
        $projects = Proyeks::orderBy('nama_proyek')->get();
        $priorities = Priority::orderBy('priority_name')->get();
        $members = Members::orderBy('member_name')->get();

        $statuses = StatusTasks::where('status_name', '!=', 'Overdue')
            ->orderBy('status_name')
            ->get();

        // 6. MENGIRIM DATA KE VIEW
        return view('tasks.index', compact(
            'planning', 'ongoing', 'reviewed', 'finished', 'canceled', 'overdue', 
            'projects', 'priorities', 'statuses', 'members', 'sort', 'direction',
            'currentProject'
        ));
    }

    public function create()
    {
        $projects = Proyeks::orderBy('nama_proyek')->get();
        $members = Members::orderBy('member_name')->get();
        $priorities = Priority::all();
        $selectedProject = request('project');

        // EXCLUDE OVERDUE PADA CREATE
        $statuses = StatusTasks::where('status_name', '!=', 'Overdue')->get();

        return view('tasks.create', compact(
            'projects', 'members', 'statuses', 'priorities', 'selectedProject'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_task'      => 'required|max:200',
            'deadline_task'  => 'nullable|date',
            'id_priority'    => 'nullable',
            'id_status'      => 'nullable',
            'id_proyek'      => 'nullable',
            'note'           => 'nullable',
            'assignees'      => 'nullable|array'
        ]);

        if ($request->id_proyek) {

            $project = Proyeks::find(
                $request->id_proyek
            );

            if (
                $project &&
                $project->deadline &&
                $request->deadline_task &&
                $request->deadline_task > $project->deadline
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'deadline_task' =>
                        'Task deadline tidak boleh melebihi deadline project'
                    ]);
            }
        }

        $task = Task::create([
            'nama_task'     => $request->nama_task,
            'deadline_task' => $request->deadline_task,
            'id_priority'   => $request->id_priority,
            'id_status'     => $request->id_status,
            'id_proyek'     => $request->id_proyek,
            'note'          => $request->note,
        ]);

        if ($request->assignees) {

            $task->assignees()->sync(
                $request->assignees
            );
        }

        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                'Task berhasil dibuat'
            );
    }

    public function show(Task $task)
    {
        $user = auth()->user();

        if ($user->id_role == 3) {
            
            $member = $user->member;

            // Load dulu relasinya sebelum kita ekstrak datanya
            $task->load('assignees');

            // Ambil semua 'id_member' dari assignees dan jadikan array biasa
            $assigneeIds = $task->assignees->pluck('id_member')->toArray();

            // Gunakan in_array untuk mengecek (lebih aman untuk berbagai tipe data)
            $allowed = in_array($member->id_member, $assigneeIds);

   
            
            // dd([
            //     '1_ID_Member_Login' => $member->id_member,
            //     '2_Daftar_ID_Assignees' => $assigneeIds,
            //     '3_Apakah_Boleh_Akses?' => $allowed
            // ]);

            abort_unless(
                $allowed,
                403, 
                'Akses Ditolak: Anda tidak di-assign pada task ini.'
            );
        }

        // Load semua relasi yang dibutuhkan view
        $task->load([
            'project',
            'priority',
            'status',
            'assignees',
            'subtasks.priority',
            'subtasks.status',
            'subtasks.assignees',
            'activities.member'
        ]);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        if(auth()->user()->id_role == 3){
            abort(403);
        }

        $projects = Proyeks::orderBy('nama_proyek')->get();
        $members = Members::orderBy('member_name')->get();
        $priorities = Priority::orderBy('priority_name')->get();

        // EXCLUDE OVERDUE PADA EDIT (Agar tidak bisa diset manual)
        $statuses = StatusTasks::where('status_name', '!=', 'Overdue')
            ->orderBy('status_name')
            ->get();

        return view('tasks.edit', compact(
            'task', 'projects', 'members', 'statuses', 'priorities'
        ));
    }

public function update(Request $request,Task $task) {
    $request->validate([
        'nama_task'      => 'required|max:200',
        'deadline_task'  => 'nullable|date',
        'id_priority'    => 'nullable',
        'id_status'      => 'nullable',
        'id_proyek'      => 'nullable',
        'note'           => 'nullable',
        'assignees'      => 'nullable|array'
    ]);

    if ($request->id_proyek) {

        $project = Proyeks::find(
            $request->id_proyek
        );

        if (
            $project &&
            $project->deadline &&
            $request->deadline_task &&
            $request->deadline_task > $project->deadline
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'deadline_task' =>
                    'Task deadline tidak boleh melebihi deadline project'
                ]);
        }
    }

    $member =
        auth()->user()->member;

    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA LAMA
    |--------------------------------------------------------------------------
    */

    $oldStatus =
        optional(
            $task->status
        )->status_name;

    $oldPriority =
        optional(
            $task->priority
        )->priority_name;

    $oldDeadline =
        $task->deadline_task;

    $oldAssignees =
        $task->assignees
            ->pluck('member_name')
            ->implode(', ');

    /*
    |--------------------------------------------------------------------------
    | UPDATE TASK
    |--------------------------------------------------------------------------
    */

    // Cek jika status diubah ke Finished (id: 8)
    if ($request->id_status == 8) {
        $unfinishedSubtasks = $task->subtasks()
            ->where('id_status', '!=', 8)
            ->count();

        if ($unfinishedSubtasks > 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'id_status' => "Tidak bisa Finished: masih ada {$unfinishedSubtasks} subtask yang belum selesai."
                ]);
        }
    }

    $task->update([
        'nama_task'     => $request->nama_task,
        'deadline_task' => $request->deadline_task,
        'id_priority'   => $request->id_priority,
        'id_status'     => $request->id_status,
        'id_proyek'     => $request->id_proyek,
        'note'          => $request->note,
    ]);

    $task->assignees()->sync(
        $request->assignees ?? []
    );

    $task->refresh();

    /*
    |--------------------------------------------------------------------------
    | DATA BARU
    |--------------------------------------------------------------------------
    */

    $newStatus =
        optional(
            $task->status
        )->status_name;

    $newPriority =
        optional(
            $task->priority
        )->priority_name;

    $newDeadline =
        $task->deadline_task;

    $newAssignees =
        $task->assignees
            ->pluck('member_name')
            ->implode(', ');

    /*
    |--------------------------------------------------------------------------
    | LOG STATUS
    |--------------------------------------------------------------------------
    */

    if ($oldStatus != $newStatus) {

        TaskActivity::create([

            'id_task' =>
                $task->id_task,

            'id_member' =>
                $member->id_member,

            'id_type' =>
                2,

            'message' =>
                'Status changed',

            'old_value' =>
                $oldStatus,

            'new_value' =>
                $newStatus
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOG PRIORITY
    |--------------------------------------------------------------------------
    */

    if ($oldPriority != $newPriority) {

        TaskActivity::create([

            'id_task' =>
                $task->id_task,

            'id_member' =>
                $member->id_member,

            'id_type' =>
                3,

            'message' =>
                'Priority changed',

            'old_value' =>
                $oldPriority,

            'new_value' =>
                $newPriority
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOG DEADLINE
    |--------------------------------------------------------------------------
    */

    if ($oldDeadline != $newDeadline) {

        TaskActivity::create([

            'id_task' =>
                $task->id_task,

            'id_member' =>
                $member->id_member,

            'id_type' =>
                4,

            'message' =>
                'Deadline changed',

            'old_value' =>
                $oldDeadline,

            'new_value' =>
                $newDeadline
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOG ASSIGNEE
    |--------------------------------------------------------------------------
    */

    if ($oldAssignees != $newAssignees) {

        TaskActivity::create([

            'id_task' =>
                $task->id_task,

            'id_member' =>
                $member->id_member,

            'id_type' =>
                5,

            'message' =>
                'Assignee changed',

            'old_value' =>
                $oldAssignees,

            'new_value' =>
                $newAssignees
        ]);
    }

    return redirect()
        ->route('tasks.index')
        ->with(
            'success',
            'Task berhasil diperbarui'
        );
}

    public function destroy(Task $task)
    {
        $task->assignees()->detach();

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                'Task berhasil dihapus'
            );
    }

public function inlineUpdate(Request $request, Task $task)
{
    $request->validate([
        'field' => 'required',
        'value' => 'nullable'
    ]);

    // Tambahkan 'assignees' agar tidak ditolak oleh validasi in_array
    $allowedFields = [
        'nama_task',
        'deadline_task',
        'id_priority',
        'id_status',
        'id_proyek',
        'note',
        'assignees' 
    ];

    if (!in_array($request->field, $allowedFields)) {
        return response()->json([
            'success' => false,
            'message' => 'Field tidak diizinkan.'
        ], 400);
    }

    $member = auth()->user()->member;
    $field = $request->field;
    $newValue = $request->value;

    /*
    |--------------------------------------------------------------------------
    | VALIDASI SEBELUM UPDATE (CEGAH FINISHED JIKA SUBTASK BELUM SELESAI)
    |--------------------------------------------------------------------------
    */
    if ($field == 'id_status' && $newValue == 8) {
        $unfinishedSubtasks = $task->subtasks()
            ->where('id_status', '!=', 8)
            ->count();

        if ($unfinishedSubtasks > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tidak bisa diubah ke Finished: masih ada {$unfinishedSubtasks} subtask yang belum selesai."
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE ASSIGNEES (Karena ini relasi / pivot table, bukan kolom biasa)
    |--------------------------------------------------------------------------
    */
    if ($field === 'assignees') {
        $oldAssignees = $task->assignees
            ->pluck('member_name')
            ->implode(', ');

        $task->assignees()->sync($newValue ?? []);
        $task->refresh();

        $newAssignees = $task->assignees
            ->pluck('member_name')
            ->implode(', ');

        if ($oldAssignees != $newAssignees) {
            TaskActivity::create([
                'id_task'   => $task->id_task,
                'id_member' => $member->id_member,
                'id_type'   => 5,
                'message'   => 'Assignee changed',
                'old_value' => $oldAssignees,
                'new_value' => $newAssignees
            ]);
        }

        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE KOLOM BIASA (Status, Priority, Deadline, Nama Task, dll)
    |--------------------------------------------------------------------------
    */
    $oldValue = $task->{$field};

    $task->update([
        $field => $newValue
    ]);

    $task->refresh();

    /*
    |--------------------------------------------------------------------------
    | LOG ACTIVITY UNTUK KOLOM BIASA
    |--------------------------------------------------------------------------
    */
    if ($oldValue != $newValue) {
        
        // LOG STATUS
        if ($field == 'id_status') {
            $oldStatus = StatusTasks::find($oldValue);
            $newStatus = StatusTasks::find($newValue);

            TaskActivity::create([
                'id_task'   => $task->id_task,
                'id_member' => $member->id_member,
                'id_type'   => 2,
                'message'   => 'Status changed',
                'old_value' => $oldStatus?->status_name,
                'new_value' => $newStatus?->status_name
            ]);
        }

        // LOG PRIORITY
        if ($field == 'id_priority') {
            $oldPriority = Priority::find($oldValue);
            $newPriority = Priority::find($newValue);

            TaskActivity::create([
                'id_task'   => $task->id_task,
                'id_member' => $member->id_member,
                'id_type'   => 3,
                'message'   => 'Priority changed',
                'old_value' => $oldPriority?->priority_name,
                'new_value' => $newPriority?->priority_name
            ]);
        }

        // LOG DEADLINE
        if ($field == 'deadline_task') {
            TaskActivity::create([
                'id_task'   => $task->id_task,
                'id_member' => $member->id_member,
                'id_type'   => 4,
                'message'   => 'Deadline changed',
                'old_value' => $oldValue,
                'new_value' => $newValue
            ]);
        }

        // LOG TASK NAME
        if ($field == 'nama_task') {
            TaskActivity::create([
                'id_task'   => $task->id_task,
                'id_member' => $member->id_member,
                'id_type'   => 6,
                'message'   => 'Task name changed',
                'old_value' => $oldValue,
                'new_value' => $newValue
            ]);
        }
    }

    return response()->json([
        'success' => true
    ]);
}

    public function myTask(Request $request)
    {
        // 1. LOGIKA AUTO-UPDATE OVERDUE
        Task::whereNotNull('deadline_task')
            ->whereDate('deadline_task', '<', Carbon::today())
            ->whereNotIn('id_status', [6, 8, 9])
            ->update(['id_status' => 9]);

        $member = auth()->user()->member;

        // ... (Kode query milik myTask sebelumnya tetap sama) ...
        $query = Task::with(['project', 'priority', 'status', 'assignees'])
            ->whereHas('assignees', function ($q) use ($member) {
                $q->where('members.id_member', $member->id_member);
            });

        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('status_name', $request->status);
            });
        }

        // ==========================================
        // PERBAIKAN SORTING DI SINI
        // ==========================================
        // Tangkap parameter sort dan direction dari URL
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        // Gunakan $direction agar bisa dinamis asc/desc
        $query->orderBy($sort, $direction);
        $tasks = $query->get();

        // Filter task yang sudah ada...
        $planning = $tasks->filter(fn($task) => $task->status?->status_name === 'Planning');
        $ongoing  = $tasks->filter(fn($task) => $task->status?->status_name === 'On Going');
        $reviewed = $tasks->filter(fn($task) => $task->status?->status_name === 'Reviewed');
        $finished = $tasks->filter(fn($task) => $task->status?->status_name === 'Finished');
        $canceled = $tasks->filter(fn($task) => $task->status?->status_name === 'Canceled');
        
        // 2. TAMBAHKAN FILTER OVERDUE
        $overdue  = $tasks->filter(fn($task) => $task->status?->status_name === 'Overdue');

        $projects = \App\Models\Proyeks::all();
        $priorities = \App\Models\Priority::all();
        $statuses = \App\Models\StatusTasks::where('status_name', '!=', 'Overdue')->get(); // Hide Overdue

        return view('tasks.my-index', compact(
            'planning', 'ongoing', 'reviewed', 'finished', 'canceled', 'overdue',
            'projects', 'priorities', 'statuses', 
            'sort', 'direction' // <--- Tambahkan lemparan variabel ini ke view
        ));
    }
    

    public function noteAttachment(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:5120' // max 5MB
        ]);

        $file = $request->file('file');
        $path = $file->store('task-attachments', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            // TAMBAHKAN BARIS DI BAWAH INI:
            'name' => $file->getClientOriginalName() 
        ]);
    }

    public function downloadFile(Request $request)
    {
        $path = $request->query('path');
        $name = $request->query('name', 'download-file');

        // Ambil path relatif file dari URL-nya
        // Contoh: http://localhost/storage/task-attachments/abc.jpg -> task-attachments/abc.jpg
        $relativePath = parse_url($path, PHP_URL_PATH);
        $relativePath = str_replace('/storage/', '', $relativePath);

        // Cek apakah file benar-benar ada di folder storage public
        if (Storage::disk('public')->exists($relativePath)) {
            // Return download akan memaksa browser mengunduh file dengan nama aslinya
            return Storage::disk('public')->download($relativePath, $name);
        }

        abort(404, 'File tidak ditemukan.');
    }
}