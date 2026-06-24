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

        // Mengambil parameter input filter dari request
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        // TANGKAP 3 PARAMETER FILTER BARU DI SINI
        $projectFilter  = $request->input('project'); 
        $statusFilter   = $request->input('status');
        $priorityFilter = $request->input('priority');
        
        $keyword = $request->input('q');

        // 2. MENCARI DATA PROJECT JIKA SEDANG DI-FILTER (Untuk Headline Dinamis)
        $currentProject = null;
        if ($projectFilter) {
            $currentProject = Proyeks::find($projectFilter); 
        }

        // 3. QUERY UTAMA TASKS DENGAN FILTER + SEARCH
        $tasks = Task::with(['project', 'priority', 'status', 'assignees'])
            ->when($projectFilter, function ($query) use ($projectFilter) {
                // Filter berdasarkan Project
                $query->where('id_proyek', $projectFilter);
            })
            ->when($statusFilter, function ($query) use ($statusFilter) {
                // Filter berdasarkan Status (berdasarkan nama status)
                $query->whereHas('status', function ($q) use ($statusFilter) {
                    $q->where('status_name', $statusFilter);
                });
            })
            ->when($priorityFilter, function ($query) use ($priorityFilter) {
                // Filter berdasarkan Priority
                $query->where('id_priority', $priorityFilter);
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where('nama_task', 'like', "%{$keyword}%");
            })
            ->orderBy($sort, $direction)
            ->get(); 

        // 4. KOLEKSI TASK UNTUK SUMMARY CARD & TABEL
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
            $project = Proyeks::find($request->id_proyek);

            if (
                $project &&
                $project->deadline &&
                $request->deadline_task &&
                $request->deadline_task > $project->deadline
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'deadline_task' => 'Task deadline tidak boleh melebihi deadline project'
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
            $task->assignees()->sync($request->assignees);
        }

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task berhasil dibuat');
    }

    public function show(Task $task)
    {
        $user = auth()->user();
        $member = $user->member; // Pindahkan ke atas agar bisa dipakai untuk tracking

        // Proteksi akses untuk Role 3 (Member biasa)
        if ($user->id_role == 3) {
            $task->load('assignees');
            $assigneeIds = $task->assignees->pluck('id_member')->toArray();
            $allowed = $member ? in_array($member->id_member, $assigneeIds) : false;

            abort_unless(
                $allowed,
                403, 
                'Akses Ditolak: Anda tidak di-assign pada task ini.'
            );
        }

        // --- FITUR BARU: Catat/Update waktu user membaca task ini ---
        if ($member) {
            \DB::table('task_reads')->updateOrInsert(
                [
                    'id_task' => $task->id_task, 
                    'id_member' => $member->id_member
                ],
                [
                    'last_read_at' => now(), 
                    'updated_at' => now()
                ]
            );
        }
        // ------------------------------------------------------------

        // Load semua relasi bawaan untuk halaman detail task
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

        $statuses = StatusTasks::where('status_name', '!=', 'Overdue')
            ->orderBy('status_name')
            ->get();

        return view('tasks.edit', compact(
            'task', 'projects', 'members', 'statuses', 'priorities'
        ));
    }

    public function update(Request $request, Task $task) {
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
            $project = Proyeks::find($request->id_proyek);

            if (
                $project &&
                $project->deadline &&
                $request->deadline_task &&
                $request->deadline_task > $project->deadline
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'deadline_task' => 'Task deadline tidak boleh melebihi deadline project'
                    ]);
            }
        }

        $member = auth()->user()->member;

        $oldStatus = optional($task->status)->status_name;
        $oldPriority = optional($task->priority)->priority_name;
        $oldDeadline = $task->deadline_task;
        $oldAssignees = $task->assignees->pluck('member_name')->implode(', ');

        if ($request->id_status == 8) {
            $unfinishedSubtasks = $task->subtasks()->where('id_status', '!=', 8)->count();

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

        $task->assignees()->sync($request->assignees ?? []);
        $task->refresh();

        $newStatus = optional($task->status)->status_name;
        $newPriority = optional($task->priority)->priority_name;
        $newDeadline = $task->deadline_task;
        $newAssignees = $task->assignees->pluck('member_name')->implode(', ');

        if ($oldStatus != $newStatus) {
            TaskActivity::create([
                'id_task' => $task->id_task,
                'id_member' => $member->id_member,
                'id_type' => 2,
                'message' => 'Status changed',
                'old_value' => $oldStatus,
                'new_value' => $newStatus
            ]);
        }

        if ($oldPriority != $newPriority) {
            TaskActivity::create([
                'id_task' => $task->id_task,
                'id_member' => $member->id_member,
                'id_type' => 3,
                'message' => 'Priority changed',
                'old_value' => $oldPriority,
                'new_value' => $newPriority
            ]);
        }

        if ($oldDeadline != $newDeadline) {
            TaskActivity::create([
                'id_task' => $task->id_task,
                'id_member' => $member->id_member,
                'id_type' => 4,
                'message' => 'Deadline changed',
                'old_value' => $oldDeadline,
                'new_value' => $newDeadline
            ]);
        }

        if ($oldAssignees != $newAssignees) {
            TaskActivity::create([
                'id_task' => $task->id_task,
                'id_member' => $member->id_member,
                'id_type' => 5,
                'message' => 'Assignee changed',
                'old_value' => $oldAssignees,
                'new_value' => $newAssignees
            ]);
        }

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task berhasil diperbarui');
    }

    public function destroy(Task $task)
    {
        $task->assignees()->detach();
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task berhasil dihapus');
    }

    public function inlineUpdate(Request $request, Task $task)
    {
        $request->validate([
            'field' => 'required',
            'value' => 'nullable'
        ]);

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

        if ($field == 'id_status' && $newValue == 8) {
            $unfinishedSubtasks = $task->subtasks()->where('id_status', '!=', 8)->count();

            if ($unfinishedSubtasks > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak bisa diubah ke Finished: masih ada {$unfinishedSubtasks} subtask yang belum selesai."
                ], 422);
            }
        }

        if ($field === 'assignees') {
            $oldAssignees = $task->assignees->pluck('member_name')->implode(', ');

            $task->assignees()->sync($newValue ?? []);
            $task->refresh();

            $newAssignees = $task->assignees->pluck('member_name')->implode(', ');

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

        $oldValue = $task->{$field};
        $task->update([$field => $newValue]);
        $task->refresh();

        if ($oldValue != $newValue) {
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

        return response()->json(['success' => true]);
    }

    public function myTask(Request $request)
    {
        // 1. LOGIKA AUTO-UPDATE OVERDUE
        Task::whereNotNull('deadline_task')
            ->whereDate('deadline_task', '<', Carbon::today())
            ->whereNotIn('id_status', [6, 8, 9])
            ->update(['id_status' => 9]);

        $member = auth()->user()->member;

        // TANGKAP INPUT SORT, FILTER, DAN SEARCH KEYWORD
        $sort           = $request->input('sort', 'created_at');
        $direction      = $request->input('direction', 'desc');
        $projectFilter  = $request->input('project'); 
        $statusFilter   = $request->input('status');
        $priorityFilter = $request->input('priority');
        $keyword        = $request->input('q');

        // MENCARI DATA PROJECT JIKA SEDANG DI-FILTER (Untuk Headline Dinamis Halaman My Task)
        $currentProject = null;
        if ($projectFilter) {
            $currentProject = Proyeks::find($projectFilter); 
        }

        // QUERY DENGAN SCOPE ASSIGNEE LOGIN + KLAUSA FILTER DINAMIS
        $query = Task::with(['project', 'priority', 'status', 'assignees'])
            ->whereHas('assignees', function ($q) use ($member) {
                $q->where('members.id_member', $member->id_member);
            })
            ->when($projectFilter, function ($query) use ($projectFilter) {
                $query->where('id_proyek', $projectFilter);
            })
            ->when($statusFilter, function ($query) use ($statusFilter) {
                $query->whereHas('status', function ($q) use ($statusFilter) {
                    $q->where('status_name', $statusFilter);
                });
            })
            ->when($priorityFilter, function ($query) use ($priorityFilter) {
                $query->where('id_priority', $priorityFilter);
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where('nama_task', 'like', "%{$keyword}%");
            });

        // Eksekusi Sorting dan Ambil Data
        $tasks = $query->orderBy($sort, $direction)->get();

        // Pecah Koleksi ke Dalam Status Kapsul / Tab Utama
        $planning = $tasks->filter(fn($task) => $task->status?->status_name === 'Planning');
        $ongoing  = $tasks->filter(fn($task) => $task->status?->status_name === 'On Going');
        $reviewed = $tasks->filter(fn($task) => $task->status?->status_name === 'Reviewed');
        $finished = $tasks->filter(fn($task) => $task->status?->status_name === 'Finished');
        $canceled = $tasks->filter(fn($task) => $task->status?->status_name === 'Canceled');
        $overdue  = $tasks->filter(fn($task) => $task->status?->status_name === 'Overdue');

        // Lookup Data untuk Dropdown Filter Komponen di View My Task
        $projects   = Proyeks::orderBy('nama_proyek')->get();
        $priorities = Priority::orderBy('priority_name')->get();
        $statuses   = StatusTasks::where('status_name', '!=', 'Overdue')->orderBy('status_name')->get();

        return view('tasks.my-index', compact(
            'planning', 'ongoing', 'reviewed', 'finished', 'canceled', 'overdue',
            'projects', 'priorities', 'statuses', 
            'sort', 'direction', 'currentProject'
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
            'url'  => Storage::disk('public')->url($path),
            'name' => $file->getClientOriginalName() 
        ]);
    }

    public function downloadFile(Request $request)
    {
        $path = $request->query('path');
        $name = $request->query('name', 'download-file');

        $relativePath = parse_url($path, PHP_URL_PATH);
        $relativePath = str_replace('/storage/', '', $relativePath);

        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->download($relativePath, $name);
        }

        abort(404, 'File tidak ditemukan.');
    }
}