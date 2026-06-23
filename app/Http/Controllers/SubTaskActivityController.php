<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\SubTaskActivity;
use Illuminate\Http\Request;

class SubTaskActivityController extends Controller
{
    /**
     * Store new subtask activity.
     */
    public function store(Request $request, Subtask $subtask)
    {
        // Menyesuaikan validasi seperti di TaskActivity
        $request->validate([
            'message' => 'required|max:1000'
        ]);

        SubTaskActivity::create([
            'id_subtask' => $subtask->id_subtask,
            
            // Menggunakan cara yang sama dari contohmu untuk mengambil ID member
            'id_member'  => auth()->user()->member->id_member,
            
            'id_type'    => 1,
            
            'message'    => $request->message,
            
            'created_at' => now()
        ]);

        // Mengembalikan ke halaman sebelumnya (kamu bisa menambahkan with() jika ingin ada flash message)
        return back(); 
    }
}