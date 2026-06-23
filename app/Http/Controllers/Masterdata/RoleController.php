<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Roles::latest()->paginate(10);

        return view('master-data.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('master-data.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|max:50'
        ]);

        Roles::create([
            'role_name' => $request->role_name
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan');
    }

    public function show(Roles $role)
    {
        return view('master-data.roles.show', compact('role'));
    }

    public function edit(Roles $role)
    {
        return view('master-data.roles.edit', compact('role'));
    }

    public function update(Request $request, Roles $role)
    {
        $request->validate([
            'role_name' => 'required|max:50'
        ]);

        $role->update([
            'role_name' => $request->role_name
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui');
    }

    public function destroy(Roles $role)
    {
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus');
    }
}