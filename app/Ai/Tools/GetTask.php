<?php

namespace App\Ai\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetTask implements Tool
{
    public function description(): Stringable|string
    {
        return 'Mengambil daftar semua task beserta deadline yang ditugaskan kepada user yang sedang login. ' .
               'Gunakan tool ini setiap kali user bertanya tentang task saya, daftar tugas, pekerjaan saya, deadline, atau tugas yang diberikan.';
    }

    public function handle(Request $request): Stringable|string
    {
        $user = auth()->user();

        if (!$user || !$user->member) {
            return 'Member tidak ditemukan. Pastikan Anda sudah login.';
        }

        $tasks = Task::query()
            ->whereHas('assignees', fn($q) => 
                $q->where('members.id_member', $user->member->id_member)
            )
            ->with('project') // optional, untuk info project
            ->get();

        if ($tasks->isEmpty()) {
            return 'Anda saat ini tidak memiliki task yang ditugaskan.';
        }

        return $tasks->map(function ($task) {
            $deadline = $task->deadline_task
                ? \Carbon\Carbon::parse($task->deadline_task)->format('d M Y') 
                : 'Tidak ada deadline';

            $project = $task->project?->nama_project ?? 'Tidak ada project';

            return "- {$task->nama_task} (Project: {$project}, Deadline: {$deadline})";
        })->implode("\n");
    }

    /**
     * Empty schema = no parameters needed
     */
    public function schema(JsonSchema $schema): array
    {
        return [];   // ← This is the key fix
    }
}