<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Members;
use App\Models\Priority;
use App\Models\Subtask;
use App\Models\StatusTasks;
use Illuminate\Http\Request;
use App\Models\SubTaskActivity;
use Illuminate\Support\Facades\Storage;

class SubtaskController extends Controller
{
    /**
     * Display a listing of subtasks.
     */
    public function index()
    {
        $subtasks = Subtask::with([
            'task.project',
            'priority',
            'status',
            'assignees'
        ])
        ->latest()
        ->paginate(10);

        return view(
            'subtasks.index',
            compact('subtasks')
        );
    }

    /**
     * Show create form.
     */
    public function create(Request $request)
    {
        $task = Task::with('project')
            ->findOrFail(
                $request->task
            );

        $priorities = Priority::all();

        $statuses = StatusTasks::all();

        $members = Members::orderBy(
            'member_name'
        )->get();

        return view(
            'subtasks.create',
            compact(
                'task',
                'priorities',
                'statuses',
                'members'
            )
        );
    }

    /**
     * Store subtask.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_task' =>
                'required|exists:tasks,id_task',

            'subtask_name' =>
                'required|max:200',

            'subtask_deadline' =>
                'nullable|date',

            'id_priority' =>
                'nullable|exists:priorities,id_priority',

            'id_status' =>
                'nullable|exists:status_tasks,id_status',

            'note' =>
                'nullable',

            'assignees' =>
                'nullable|array',
        ]);

        $task = Task::findOrFail(
            $request->id_task
        );

        if (
            $request->filled(
                'subtask_deadline'
            ) &&
            $task->deadline_task &&
            $request->subtask_deadline >
            $task->deadline_task
        ) {
            return back()
                ->withErrors([
                    'subtask_deadline' =>
                        'Subtask deadline cannot exceed task deadline.'
                ])
                ->withInput();
        }

        $subtask = Subtask::create([
            'id_task' =>
                $request->id_task,

            'subtask_name' =>
                $request->subtask_name,

            'subtask_deadline' =>
                $request->subtask_deadline,

            'id_priority' =>
                $request->id_priority,

            'id_status' =>
                $request->id_status,

            'note' =>
                $request->note,
        ]);

        $subtask->assignees()->sync(
            $request->assignees ?? []
        );

        return redirect()
            ->route(
                'tasks.show',
                ['task' => $task->id_task]
            )
            ->with(
                'success',
                'Subtask berhasil ditambahkan'
            );
    }

    /**
     * Show subtask detail.
     */
    /**
     * Show subtask detail.
     */
    public function show(Subtask $subtask)
    {
        $subtask->load([
            'task.project',
            'priority',
            'status',
            'assignees',
            'activities.member', // Tambahkan baris ini
            'activities.type'    // Tambahkan baris ini agar tipe aktivitas juga termuat
        ]);

        return view(
            'subtasks.show',
            compact('subtask')
        );
    }

    /**
     * Show edit form.
     */
    public function edit(Subtask $subtask)
    {
        $subtask->load([
            'task.project',
            'assignees'
        ]);

        $priorities = Priority::all();

        $statuses = StatusTask::all();

        $members = Member::orderBy(
            'member_name'
        )->get();

        return view(
            'subtasks.edit',
            compact(
                'subtask',
                'priorities',
                'statuses',
                'members'
            )
        );
    }

    /**
     * Update subtask.
     */
    public function update(
        Request $request,
        Subtask $subtask
    )
    {
        $request->validate([
            'subtask_name' =>
                'required|max:200',

            'subtask_deadline' =>
                'nullable|date',

            'id_priority' =>
                'nullable|exists:priorities,id_priority',

            'id_status' =>
                'nullable|exists:status_tasks,id_status',

            'note' =>
                'nullable',

            'assignees' =>
                'nullable|array',
        ]);

        $task = $subtask->task;

        if (
            $request->filled(
                'subtask_deadline'
            ) &&
            $task->deadline_task &&
            $request->subtask_deadline >
            $task->deadline_task
        ) {
            return back()
                ->withErrors([
                    'subtask_deadline' =>
                        'Subtask deadline cannot exceed task deadline.'
                ])
                ->withInput();
        }

        $subtask->update([
            'subtask_name' =>
                $request->subtask_name,

            'subtask_deadline' =>
                $request->subtask_deadline,

            'id_priority' =>
                $request->id_priority,

            'id_status' =>
                $request->id_status,

            'note' =>
                $request->note,
        ]);

        $subtask->assignees()->sync(
            $request->assignees ?? []
        );

        return redirect()
            ->route(
                'tasks.show',
                ['task' => $subtask->id_task]
            )
            ->with(
                'success',
                'Subtask berhasil diperbarui'
            );
    }

    /**
     * Delete subtask.
     */
    public function destroy(
        Subtask $subtask
    )
    {
        $task = $subtask->task;

        $subtask->delete();

        return redirect()
            ->route(
                'tasks.show',
                $task
            )
            ->with(
                'success',
                'Subtask deleted successfully.'
            );
    }

    /**
     * Update field secara inline via AJAX.
     */
    public function inlineUpdate(Request $request, Subtask $subtask)
    {
        $request->validate([
            'field' => 'required',
            'value' => 'nullable'
        ]);

        // Sesuaikan dengan nama kolom di tabel subtasks
        $allowedFields = [
            'subtask_name',
            'subtask_deadline',
            'id_priority',
            'id_status',
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
        | VALIDASI DEADLINE (Cegah deadline subtask melebihi task utama)
        |--------------------------------------------------------------------------
        */
        if ($field === 'subtask_deadline' && $newValue) {
            $task = $subtask->task;
            if ($task->deadline_task && $newValue > $task->deadline_task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subtask deadline tidak boleh melebihi task deadline.'
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE ASSIGNEES (Relasi Many-to-Many)
        |--------------------------------------------------------------------------
        */
        if ($field === 'assignees') {
            $oldAssignees = $subtask->assignees
                ->pluck('member_name')
                ->implode(', ');

            $subtask->assignees()->sync($newValue ?? []);
            $subtask->refresh();

            $newAssignees = $subtask->assignees
                ->pluck('member_name')
                ->implode(', ');

            if ($oldAssignees != $newAssignees) {
                SubTaskActivity::create([
                    'id_subtask' => $subtask->id_subtask,
                    'id_member'  => $member->id_member,
                    'id_type'    => 5,
                    'message'    => 'Assignee changed',
                    'old_value'  => $oldAssignees,
                    'new_value'  => $newAssignees
                ]);
            }

            return response()->json(['success' => true]);
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE KOLOM BIASA
        |--------------------------------------------------------------------------
        */
        $oldValue = $subtask->{$field};

        $subtask->update([
            $field => $newValue
        ]);

        $subtask->refresh();

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

                SubTaskActivity::create([
                    'id_subtask' => $subtask->id_subtask,
                    'id_member'  => $member->id_member,
                    'id_type'    => 2,
                    'message'    => 'Status changed',
                    'old_value'  => $oldStatus?->status_name,
                    'new_value'  => $newStatus?->status_name
                ]);
            }

            // LOG PRIORITY
            if ($field == 'id_priority') {
                $oldPriority = Priority::find($oldValue);
                $newPriority = Priority::find($newValue);

                SubTaskActivity::create([
                    'id_subtask' => $subtask->id_subtask,
                    'id_member'  => $member->id_member,
                    'id_type'    => 3,
                    'message'    => 'Priority changed',
                    'old_value'  => $oldPriority?->priority_name,
                    'new_value'  => $newPriority?->priority_name
                ]);
            }

            // LOG DEADLINE
            if ($field == 'subtask_deadline') {
                SubTaskActivity::create([
                    'id_subtask' => $subtask->id_subtask,
                    'id_member'  => $member->id_member,
                    'id_type'    => 4,
                    'message'    => 'Deadline changed',
                    'old_value'  => $oldValue,
                    'new_value'  => $newValue
                ]);
            }

            // LOG NAMA SUBTASK
            if ($field == 'subtask_name') {
                SubTaskActivity::create([
                    'id_subtask' => $subtask->id_subtask,
                    'id_member'  => $member->id_member,
                    'id_type'    => 6,
                    'message'    => 'Subtask name changed',
                    'old_value'  => $oldValue,
                    'new_value'  => $newValue
                ]);
            }
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Upload lampiran file pada note subtask.
     */
    public function noteAttachment(Request $request, Subtask $subtask)
    {
        $request->validate([
            'file' => 'required|file|max:5120' // max 5MB
        ]);

        $file = $request->file('file');
        
        // Disimpan di folder khusus subtask agar lebih rapi
        $path = $file->store('subtask-attachments', 'public');

        return response()->json([
            'url'  => Storage::disk('public')->url($path),
            'name' => $file->getClientOriginalName() 
        ]);
    }

    /**
     * Download lampiran file subtask.
     */
    public function downloadFile(Request $request)
    {
        $path = $request->query('path');
        $name = $request->query('name', 'download-file');

        // Ambil path relatif file dari URL-nya
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