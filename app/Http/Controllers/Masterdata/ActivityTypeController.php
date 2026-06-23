<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{
    public function index()
    {
        $activity_types = ActivityType::latest()->paginate(10);

        return view('master-data.activity-types.index', compact('activity_types'));
    }

    public function create()
    {
        return view('master-data.activity-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|max:100'
        ]);

        ActivityType::create([
            'type_name' => $request->type_name
        ]);

        return redirect()
            ->route('activity-types.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(ActivityType $activity_type)
    {
        return view('master-data.activity-types.show', compact('activity_type'));
    }

    public function edit(ActivityType $activity_type)
    {
        return view('master-data.activity-types.edit', compact('activity_type'));
    }

    public function update(Request $request, ActivityType $activity_type)
    {
        $request->validate([
            'type_name' => 'required|max:100'
        ]);

        $activity_type->update([
            'type_name' => $request->type_name
        ]);

        return redirect()
            ->route('activity-types.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(ActivityType $activity_type)
    {
        $activity_type->delete();

        return redirect()
            ->route('activity-types.index')
            ->with('success', 'Data berhasil dihapus');
    }
}