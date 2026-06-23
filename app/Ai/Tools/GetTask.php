<?php

namespace App\Ai\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetTask implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Mengambil daftar task milik user yang sedang login';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $user = auth()->user();

        if (!$user || !$user->member) {
            return 'Member tidak ditemukan';
        }

        $tasks = Task::query()
            ->whereHas(
                'assignees',
                fn ($q) =>
                    $q->where(
                        'members.id_member',
                        $user->member->id_member
                    )
            )
            ->get();

        if ($tasks->isEmpty()) {
            return 'Tidak ada task yang ditugaskan';
        }

        return $tasks
            ->map(function ($task) {
                return "- {$task->nama_task}";
            })
            ->implode("\n");
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
