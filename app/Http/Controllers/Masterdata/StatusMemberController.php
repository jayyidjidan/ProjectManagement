<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\StatusMembers;
use Illuminate\Http\Request;

class StatusMemberController extends Controller
{
    public function index()
    {
        $status_members = StatusMembers::latest()->paginate(10);

        return view('master-data.status-members.index', compact('status_members'));
    }

    public function create()
    {
        return view('master-data.status-members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_name' => 'required|max:50'
        ]);

        StatusMembers::create([
            'status_name' => $request->status_name
        ]);

        return redirect()
            ->route('status-members.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(StatusMembers $status_member)
    {
        return view('master-data.status-members.show', compact('status_member'));
    }

    public function edit(StatusMembers $status_member)
    {
        return view('master-data.status-members.edit', compact('status_member'));
    }

    public function update(Request $request, StatusMembers $status_member)
    {
        $request->validate([
            'status_name' => 'required|max:50'
        ]);

        $status_member->update([
            'status_name' => $request->status_name
        ]);

        return redirect()
            ->route('status-members.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(StatusMembers $status_member)
    {
        $status_member->delete();

        return redirect()
            ->route('status-members.index')
            ->with('success', 'Data berhasil dihapus');
    }
}