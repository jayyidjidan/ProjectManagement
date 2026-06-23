<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskActivity;

use Illuminate\Http\Request;

class TaskActivityController extends Controller
{
    public function store(
        Request $request,
        Task $task
    )
    {
        $request->validate([

            'message' =>
                'required|max:1000'

        ]);

        TaskActivity::create([

            'id_task' =>
                $task->id_task,

            'id_member' =>
                auth()->user()
                    ->member
                    ->id_member,

            'id_type' =>
                1,

            'message' =>
                $request->message,

            'created_at' =>
                now()

        ]);

        return back();
    }
}