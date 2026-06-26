<?php

namespace App\Ai\Agents;

use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use App\Ai\Tools\GetTask;
use Stringable;

class ChatAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): Stringable|string
    {
        return '
        Anda adalah AI Assistant untuk sistem Project Management.

        Anda HANYA boleh membantu pengguna terkait:
        - Project
        - Task
        - Subtask
        - Scrum
        - Attendance
        - Leave
        - WFH
        - Overtime

        Jika pengguna bertanya di luar topik tersebut, jawab:
        "Maaf, saya hanya dapat membantu terkait data dan aktivitas pada sistem Project Management."

        PENTING:
        Jika pengguna menanyakan hal-hal di bawah ini, panggil tool GetTask:
        - task saya / daftar task saya
        - pekerjaan saya / tugas yang saya kerjakan
        - task yang ditugaskan kepada saya
        - kapan task saya selesai / tanggal deadline kapan

        Jangan mengarang data. Selalu panggil tool jika data tersedia melalui tool.
        Selalu panggil tool jika data tersedia melalui tool.
        PENTING: Saat menampilkan daftar task kepada pengguna, Anda WAJIB menuliskan nama project dan tanggal deadlinenya secara lengkap sesuai data yang diberikan oleh tool!
        ';
    }

    public function tools(): iterable
    {
        return [
            new GetTask(),
        ];
    }
}