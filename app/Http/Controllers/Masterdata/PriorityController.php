<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function index()
    {
        $priorities = Priority::latest()->paginate(10);

        return view('master-data.priorities.index', compact('priorities'));
    }

    public function create()
    {
        return view('master-data.priorities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'priority_name' => 'required|max:100'
        ]);

        Priority::create([
            'priority_name' => $request->priority_name
        ]);

        return redirect()
            ->route('priorities.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(Priority $priority)
    {
        return view('master-data.priorities.show', compact('priority'));
    }

    public function edit(Priority $priority)
    {
        return view('master-data.priorities.edit', compact('priority'));
    }

    public function update(Request $request, Priority $priority)
    {
        $request->validate([
            'priority_name' => 'required|max:100'
        ]);

        $priority->update([
            'priority_name' => $request->priority_name
        ]);

        return redirect()
            ->route('priorities.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(Priority $priority)
    {
        $priority->delete();

        return redirect()
            ->route('priorities.index')
            ->with('success', 'Data berhasil dihapus');
    }
}