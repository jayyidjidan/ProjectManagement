@extends('layouts.app')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<style>
    /* TAMBAHKAN DI BAWAH CSS QUILL KAMU SEBELUMNYA */
.attachment-box {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    margin: 8px 0;
    border: 1px solid var(--border, #e5e7eb);
    border-radius: 10px;
    background-color: #f9fafb;
    text-decoration: none !important;
    color: #1f2937 !important;
    font-family: inherit;
    font-size: 13px;
    font-weight: 500;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    cursor: pointer;
    user-select: none;
}
.attachment-box svg {
    width: 18px;
    height: 18px;
    color: #6b7280;
    flex-shrink: 0;
}
.attachment-box span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 200px;
}

    .ql-toolbar.ql-snow {
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 0.75rem 0.75rem 0 0;
        background: white;
    }
    .ql-container.ql-snow {
        border: 1px solid var(--border, #e5e7eb);
        border-top: none;
        border-radius: 0 0 0.75rem 0.75rem;
        font-family: inherit;
        font-size: 0.875rem;
    }
    .ql-editor {
        min-height: 140px;
        padding: 0.75rem 1rem;
    }
    .ql-editor.ql-blank::before {
        color: #9ca3af;
        font-style: normal;
    }
    
    /* FIX BENTROK QUILL & TAILWIND */
    .ql-toolbar.ql-snow button svg,
    .ql-toolbar.ql-snow .ql-picker-label svg {
        width: 18px !important;
        height: 18px !important;
        display: inline-block !important;
        vertical-align: middle !important;
    }
    .ql-toolbar.ql-snow .ql-picker-label {
        display: flex !important;
        align-items: center !important;
    }
    .ql-toolbar.ql-snow button {
        padding: 3px 5px !important;
    }
    
    .note-content a       { color: hsl(var(--primary, 221 83% 53%)); text-decoration: underline; }
    .note-content ul      { list-style: disc; padding-left: 1.5rem; margin: 0.25rem 0; }
    .note-content ol      { list-style: decimal; padding-left: 1.5rem; margin: 0.25rem 0; }
    .note-content strong  { font-weight: 600; }
    .note-content em      { font-style: italic; }
    .note-content s       { text-decoration: line-through; }
    .note-content p:not(:last-child) { margin-bottom: 0.25rem; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">
    {{ session('success') }}
</div>
@endif

{{-- HEADER --}}
<div class="flex items-end justify-between mb-6">
    <div class="flex items-start gap-3">
    @php
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        
        // 1. Tentukan fallback paling dasar
        $fallbackUrl = auth()->user()->id_role == 3 ? route('my-task.index') : route('tasks.index');
        
        // 2. FALLBACK CERDAS (Hanya untuk non-member / Admin / PM)
        if (auth()->user()->id_role != 3 && $task->id_proyek) {
            $fallbackUrl = route('projects.show', $task->id_proyek);
        }

        // 3. LOGIKA UTAMA + PENGUNCIAN UNTUK MEMBER
        if (auth()->user()->id_role == 3) {
            // Jika user adalah Member, PAKSA selalu ke halaman My Task
            $backUrl = route('my-task.index');
        } elseif ($previousUrl === $currentUrl || str_contains(strtolower($previousUrl), 'subtask')) {
            // Jika Admin/PM melakukan refresh atau dari halaman subtask, gunakan fallback
            $backUrl = $fallbackUrl;
        } else {
            // Selain itu, gunakan URL sebelumnya
            $backUrl = $previousUrl;
        }
    @endphp

    <a href="{{ $backUrl }}">
        <x-button-secondary>Back</x-button-secondary>
    </a>
    <div>
        <h1 class="text-4xl font-bold">{{ $task->nama_task }}</h1>
        <p class="text-gray-500">Task Detail Information</p>
    </div>
</div>
    @if(auth()->user()->id_role != 3)
        <a href="{{ route('tasks.edit', $task) }}">Edit</a>
    @endif
</div>

{{-- INFO CARDS --}}
<div class="flex gap-6">

    <x-card class="flex-1">
        <h2 class="text-xl font-semibold mb-4">Task Information</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-500">Task Name</p>
                <p>{{ $task->nama_task }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p>{{ $task->status->status_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Priority</p>
                <p>{{ $task->priority->priority_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Deadline</p>
                <p>{{ $task->deadline_task ? \Carbon\Carbon::parse($task->deadline_task)->format('d M Y') : '-' }}</p>
            </div>
        </div>
    </x-card>

    <x-card class="flex-1">
        <h2 class="text-xl font-semibold mb-4">Project Information</h2>
        @if($task->project)
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Project Name</p>
                    <p>
                        <a href="{{ route('projects.show', $task->project) }}" class="text-primary hover:underline">
                            {{ $task->project->nama_proyek }}
                        </a>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Project Deadline</p>
                    <p>{{ $task->project->deadline ? \Carbon\Carbon::parse($task->project->deadline)->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Project Status</p>
                    <p>{{ $task->project->status->nama_status ?? '-' }}</p>
                </div>
            </div>
        @else
            <div class="text-gray-500">Personal Task</div>
        @endif
    </x-card>

    <x-card class="flex-1">
        <h2 class="text-xl font-semibold mb-4">Assignees</h2>
        <div class="flex flex-wrap gap-2">
            @forelse($task->assignees as $member)
                <span class="px-3 py-2 rounded-full border border-border">{{ $member->member_name }}</span>
            @empty
                <span class="text-gray-400">No Assignees</span>
            @endforelse
        </div>
    </x-card>

</div>

{{-- NOTES (WYSIWYG INLINE) --}}
<x-card class="mt-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Notes</h2>

        @if(auth()->user()->id_role != 3)
        <div class="flex gap-2">
            <div id="edit-note-btn" onclick="startEditNote()" class="cursor-pointer">
                <x-button-secondary>Edit Note</x-button-secondary>
            </div>
            <div id="save-note-btn" class="hidden cursor-pointer" onclick="saveNote()">
                <x-button-primary>Save</x-button-primary>
            </div>
            <div id="cancel-note-btn" class="hidden cursor-pointer" onclick="cancelEditNote()">
                <x-button-secondary>Cancel</x-button-secondary>
            </div>
        </div>
        @endif
    </div>

    {{-- Display mode --}}
    <div id="note-display">
        @if($task->note)
            <div class="note-content text-sm leading-relaxed">{!! $task->note !!}</div>
        @else
            <p class="text-gray-500 text-sm" id="note-placeholder">No Notes Available</p>
        @endif
    </div>

    {{-- Editor mode (hidden) --}}
    <div id="note-editor-container" class="hidden">
        <div id="note-editor"></div>

        <div class="mt-3 flex items-center gap-3">
            <label for="note-file-input"
                class="cursor-pointer inline-flex items-center gap-1.5 text-sm text-gray-500 border border-border rounded-xl px-3 py-1.5 hover:bg-gray-50 transition-colors select-none">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                Attach File
            </label>
            <input type="file" id="note-file-input" class="hidden">
            <span id="upload-status" class="text-sm text-gray-400"></span>
        </div>
    </div>
</x-card>

{{-- SUBTASKS --}}
<x-card class="mt-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold">
            Subtasks
            <span class="text-gray-500">({{ $task->subtasks->count() }})</span>
        </h2>
        @if(auth()->user()->id_role != 3)
        <a href="{{ route('subtasks.create', ['task' => $task->id_task]) }}">
            <x-button-primary>Add Subtask</x-button-primary>
        </a>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="p-4 text-left">Subtask</th>
                    <th class="p-4 text-left">Assignees</th>
                    <th class="p-4 text-left">Priority</th>
                    <th class="p-4 text-left">Status</th>
                    <th class="p-4 text-left">Deadline</th>
                </tr>
            </thead>
            <tbody>
                @forelse($task->subtasks as $subtask)
                    <tr onclick="window.location='{{ route('subtasks.show', $subtask) }}'"
                        class="cursor-pointer hover:bg-gray-50 transition border-b border-border">
                        <td class="p-4 font-medium">{{ $subtask->subtask_name }}</td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-2">
                                @forelse($subtask->assignees as $member)
                                    <span class="px-3 py-1 rounded-full border border-border text-xs">{{ $member->member_name }}</span>
                                @empty
                                    <span class="text-gray-400">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="p-4">{{ $subtask->priority->priority_name ?? '-' }}</td>
                        <td class="p-4">{{ $subtask->status->status_name ?? '-' }}</td>
                        <td class="p-4">
                            {{ $subtask->subtask_deadline ? \Carbon\Carbon::parse($subtask->subtask_deadline)->format('d M Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-500">No Subtasks Available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- ACTIVITY & COMMENTS (CHAT STYLE) --}}
<x-card class="mt-6">
    <h2 class="text-xl font-semibold mb-4">Activity & Comments</h2>

    {{-- Feed --}}
    <div id="activity-feed" class="space-y-2 max-h-96 overflow-y-auto pb-2">
        @forelse($task->activities->sortBy('created_at') as $activity)

            @if($activity->id_type == 1)
                {{-- Chat bubble --}}
                @php $isMe = auth()->user()->member?->id_member == $activity->id_member; @endphp

                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[70%]">
                        @if(!$isMe)
                            <p class="text-xs text-gray-500 mb-1 ml-3">
                                {{ $activity->member->member_name }}
                            </p>
                        @endif

                        <div class="px-4 py-2.5 rounded-2xl text-sm
                            {{ $isMe
                                ? 'bg-primary text-white rounded-br-none'
                                : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                            {{ $activity->message }}
                        </div>

                        <p class="text-xs text-gray-400 mt-1 {{ $isMe ? 'text-right mr-2' : 'ml-3' }}">
                            {{ \Carbon\Carbon::parse($activity->created_at)->format('d M, H:i') }}
                        </p>
                    </div>
                </div>

            @else
                {{-- Activity log: centered plain text --}}
                <div class="flex justify-center py-0.5">
                    <p class="text-xs text-gray-400 text-center leading-relaxed">
                        <span class="font-medium text-gray-500">{{ $activity->member->member_name }}</span>
                        {{ strtolower($activity->message) }}:
                        <span class="line-through">{{ $activity->old_value ?? '-' }}</span>
                        →
                        <span class="font-medium text-gray-600">{{ $activity->new_value ?? '-' }}</span>
                        &bull;
                        {{ \Carbon\Carbon::parse($activity->created_at)->format('d M, H:i') }}
                    </p>
                </div>
            @endif

        @empty
            <div class="text-center text-gray-400 py-8 text-sm">No activity yet</div>
        @endforelse
    </div>

    {{-- Comment input bar --}}
    <div class="border-t border-border mt-4 pt-4">
        <form action="{{ route('tasks.activities.store', $task) }}" method="POST"
            class="flex items-center gap-3">
            @csrf
            <input
                type="text"
                name="message"
                placeholder="Write a comment..."
                required
                autocomplete="off"
                class="flex-1 rounded-xl border border-border px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition">
            <x-button-primary type="submit">Send</x-button-primary>
        </form>
    </div>
</x-card>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
    const taskId    = {{ $task->id_task }};
    let currentNote = @json($task->note ?? '');
    let quill       = null;

    // 1. DAFTARKAN CUSTOM MODULE (BLOT) AGAR QUILL MENGENALI KOTAK FILE
    const Embed = Quill.import('blots/embed');

    class AttachmentBlot extends Embed {
        static create(value) {
            let node = super.create();
            node.setAttribute('href', value.url);
            node.setAttribute('download', value.name); 
            node.setAttribute('target', '_blank');
            node.setAttribute('contenteditable', 'false');
            node.className = 'attachment-box'; 

            node.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>${value.name}</span>
            `;
            return node;
        }

        static value(node) {
            return {
                url: node.getAttribute('href'),
                name: node.getAttribute('download')
            };
        }
    }
    AttachmentBlot.blotName = 'attachmentCard';
    AttachmentBlot.tagName = 'a';
    Quill.register(AttachmentBlot); 

    // 2. INISIALISASI QUILL
    function initQuill() {
        if (quill) return;
        quill = new Quill('#note-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            },
            placeholder: 'Tulis catatan di sini...'
        });
    }

    function startEditNote() {
        initQuill();
        document.getElementById('note-display').classList.add('hidden');
        document.getElementById('note-editor-container').classList.remove('hidden');
        document.getElementById('edit-note-btn').classList.add('hidden');
        document.getElementById('save-note-btn').classList.remove('hidden');
        document.getElementById('cancel-note-btn').classList.remove('hidden');

        quill.root.innerHTML = currentNote || '';
        setTimeout(() => quill.focus(), 50);
    }

    function cancelEditNote() {
        document.getElementById('note-display').classList.remove('hidden');
        document.getElementById('note-editor-container').classList.add('hidden');
        document.getElementById('edit-note-btn').classList.remove('hidden');
        document.getElementById('save-note-btn').classList.add('hidden');
        document.getElementById('cancel-note-btn').classList.add('hidden');
    }

    async function saveNote() {
        const html  = quill.root.innerHTML;
        const value = quill.getText().trim() === '' && !html.includes('attachment-box') ? null : html;

        try {
            const res = await fetch(`/tasks/${taskId}/inline-update`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ field: 'note', value })
            });

            const data = await res.json();

            if (data.success) {
                currentNote = value;
                const display = document.getElementById('note-display');
                display.innerHTML = value
                    ? `<div class="note-content text-sm leading-relaxed">${value}</div>`
                    : `<p class="text-gray-500 text-sm" id="note-placeholder">No Notes Available</p>`;
                cancelEditNote();
            } else {
                alert(data.message ?? 'Gagal menyimpan catatan.');
            }
        } catch {
            alert('Terjadi kesalahan saat menyimpan.');
        }
    }

    // 3. FUNGSI UPLOAD & INSERT CARD
    document.getElementById('note-file-input').addEventListener('change', async function () {
        const file = this.files[0];
        if (!file) return;

        const status = document.getElementById('upload-status');
        status.textContent = 'Uploading...';

        const form = new FormData();
        form.append('file', file);
        form.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const res  = await fetch(`/tasks/${taskId}/note-attachment`, { method: 'POST', body: form });
            const data = await res.json();

            if (data.url) {
                const range = quill.getSelection(true);
                const idx   = range ? range.index : quill.getLength();
                
                quill.insertText(idx, '\n'); 
                quill.insertEmbed(idx + 1, 'attachmentCard', { url: data.url, name: file.name });
                quill.insertText(idx + 2, '\n'); 
                quill.setSelection(idx + 3);     
                
                status.textContent = '✓ Attached';
            } else {
                status.textContent = '✗ Upload failed';
            }
        } catch {
            status.textContent = '✗ Upload failed';
        }

        setTimeout(() => { status.textContent = ''; }, 3000);
        this.value = '';
    });

    // 4. FIX UTAMA: MENCEGAT KLIK DAN DIALIKHAN KE ROUTE LARAVEL FORCED DOWNLOAD
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.attachment-box');
        if (!target) return;

        // Stop browser agar tidak membuka link gambar secara default
        e.preventDefault(); 

        const fileUrl = target.getAttribute('href');
        const fileName = target.getAttribute('download') || 'download-file';

        // Alihkan ke route khusus Laravel dengan query string
        window.location.href = `/tasks/download-file?path=${encodeURIComponent(fileUrl)}&name=${encodeURIComponent(fileName)}`;
    });

    const feed = document.getElementById('activity-feed');
    if (feed) feed.scrollTop = feed.scrollHeight;
</script>
@endpush