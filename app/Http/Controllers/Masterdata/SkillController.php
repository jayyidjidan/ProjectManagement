<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Skills;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skills::latest()->paginate(10);

        return view('master-data.skills.index', compact('skills'));
    }

    public function create()
    {
        return view('master-data.skills.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'skill_name' => 'required|max:100'
        ]);

        Skills::create([
            'skill_name' => $request->skill_name
        ]);

        return redirect()
            ->route('skills.index')
            ->with('success', 'Skill berhasil ditambahkan');
    }

    public function show(Skills $skill)
    {
        return view('master-data.skills.show', compact('skill'));
    }

    public function edit(Skills $skill)
    {
        return view('master-data.skills.edit', compact('skill'));
    }

    public function update(Request $request, Skills $skill)
    {
        $request->validate([
            'skill_name' => 'required|max:100'
        ]);

        $skill->update([
            'skill_name' => $request->skill_name
        ]);

        return redirect()
            ->route('skills.index')
            ->with('success', 'Skill berhasil diperbarui');
    }

    public function destroy(Skills $skill)
    {
        $skill->delete();

        return redirect()
            ->route('skills.index')
            ->with('success', 'Skill berhasil dihapus');
    }
}