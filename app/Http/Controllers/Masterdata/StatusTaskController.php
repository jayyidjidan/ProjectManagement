<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\StatusTasks;
use Illuminate\Http\Request;

class StatusTaskController extends Controller
{
    public function index()
    {
        $status_tasks = StatusTasks::latest()->paginate(10);

        return view('master-data.status-tasks.index', compact('status_tasks'));
    }

    public function create()
    {
        return view('master-data.status-tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_name' => 'required|max:100'
        ]);

        StatusTasks::create([
            'status_name' => $request->status_name
        ]);

        return redirect()
            ->route('status-tasks.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(StatusTasks $status_task)
    {
        return view('master-data.status-tasks.show', compact('status_task'));
    }

    public function edit(StatusTasks $status_task)
    {
        return view('master-data.status-tasks.edit', compact('status_task'));
    }

    public function update(Request $request, StatusTasks $status_task)
    {
        $request->validate([
            'status_name' => 'required|max:100'
        ]);

        $status_task->update([
            'status_name' => $request->status_name
        ]);
        
        return redirect()
            ->route('status-tasks.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(StatusTasks $status_task)
    {
        $status_task->delete();

        return redirect()
            ->route('status-tasks.index')
            ->with('success', 'Data berhasil dihapus');
    }
}