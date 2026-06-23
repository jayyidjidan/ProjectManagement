@extends('layouts.app')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<style>
    /* STYLE UNTUK TOM SELECT */
    .ts-wrapper.single .ts-control,
    .ts-wrapper.multi .ts-control {
        border: 1px solid #E5E7EB !important;
        border-radius: 0.75rem !important;
        min-height: 48px !important;
        padding: 0.75rem !important;
        box-shadow: none !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #F97316 !important;
        box-shadow: 0 0 0 1px #F97316 !important;
    }
    .ts-control input {
        font-size: 14px !important;
    }
    .ts-dropdown {
        border-radius: 0.75rem !important;
        border: 1px solid #E5E7EB !important;
        overflow: hidden;
    }
    .ts-wrapper.multi .ts-control > div {
        background: #FFF7ED !important;
        color: #EA580C !important;
        border-radius: 9999px !important;
        padding: 4px 10px !important;
    }

    /* STYLE UNTUK QUILL JS & ATTACHMENT BOX */
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
        background: white;
    }
    .ql-editor {
        min-height: 150px;
        max-height: 250px;
        overflow-y: auto;
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
    .ql-editor::-webkit-scrollbar {
        width: 6px;
    }
    .ql-toolbar.ql-snow .ql-picker-label {
        display: flex !important;
        align-items: center !important;
    }
    .ql-toolbar.ql-snow button {
        padding: 3px 5px !important;
    }
    
    /* PREVIEW FORMAT DALAM EDITOR */
    .ql-editor a       { color: hsl(var(--primary, 221 83% 53%)); text-decoration: underline; }
    .ql-editor ul      { list-style: disc; padding-left: 1.5rem; margin: 0.25rem 0; }
    .ql-editor ol      { list-style: decimal; padding-left: 1.5rem; margin: 0.25rem 0; }
    .ql-editor strong  { font-weight: 600; }
    .ql-editor em      { font-style: italic; }
    .ql-editor s       { text-decoration: line-through; }
    .ql-editor p:not(:last-child) { margin-bottom: 0.25rem; }
</style>
@endpush

@section('content')

<div class="flex items-end justify-between mb-6">
    <div>
        <h1 class="text-4xl font-bold">
            Create Task
        </h1>
        <p class="text-gray-500 mt-2">
            Add a new task
        </p>
    </div>

    @php
        if (request('origin') === 'project') {
            $backUrl = route('projects.show', request('project_id'));
        } else {
            $backUrl = request('project_id') 
                ? route('tasks.index', ['project_id' => request('project_id')]) 
                : route('tasks.index');
        }
    @endphp

    <a href="{{ $backUrl }}">
        <x-button-secondary>
            Back
        </x-button-secondary>
    </a>
</div>

<x-card>
    <form
        id="create-task-form"
        action="{{ route('tasks.store') }}"
        method="POST">

        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- TASK NAME --}}
            <div class="md:col-span-2">
                <label class="block mb-2 font-medium">Task Name</label>
                <input
                    type="text"
                    name="nama_task"
                    value="{{ old('nama_task') }}"
                    class="w-full rounded-xl border border-border p-3"
                    required>
                @error('nama_task')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- PROJECT --}}
            <div>
                <label class="block mb-2 font-medium">Project</label>
                @if(request('project_id'))
                    @php
                        $currentProject = $projects->firstWhere('id_proyek', request('project_id'));
                    @endphp
                    <input type="hidden" name="id_proyek" value="{{ request('project_id') }}">
                    <div class="w-full rounded-xl border border-border p-3 bg-gray-100 text-gray-500 cursor-not-allowed">
                        {{ $currentProject ? $currentProject->nama_proyek : 'Unknown Project' }}
                    </div>
                @else
                    <select name="id_proyek" class="w-full rounded-xl border border-border p-3">
                        <option value="">Personal Task</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id_proyek }}" @selected(old('id_proyek') == $project->id_proyek)>
                                {{ $project->nama_proyek }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- DEADLINE --}}
            <div>
                <label class="block mb-2 font-medium">Deadline</label>
                <input
                    type="date"
                    name="deadline_task"
                    value="{{ old('deadline_task') }}"
                    class="w-full rounded-xl border border-border p-3">
                @error('deadline_task')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- PRIORITY --}}
            <div>
                <label class="block mb-2 font-medium">Priority</label>
                <select name="id_priority" class="w-full rounded-xl border border-border p-3">
                    <option value="">Select Priority</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority->id_priority }}" @selected(old('id_priority') == $priority->id_priority)>
                            {{ $priority->priority_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- STATUS --}}
            <div>
                <label class="block mb-2 font-medium">Status</label>
                <select name="id_status" class="w-full rounded-xl border border-border p-3">
                    <option value="">Select Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id_status }}" @selected(old('id_status') == $status->id_status)>
                            {{ $status->status_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ASSIGNEES (MODIFIED WITH DATA ATTRIBUTES) --}}
            <div class="md:col-span-2">
                <label class="block mb-2 font-medium">Assignees</label>
                <select id="assignees" name="assignees[]" multiple>
                    @foreach($members as $member)
                        <option value="{{ $member->id_member }}" 
                            data-position="{{ $member->jabatan?->position_name ?? 'No Position' }}"
                            data-skills="{{ $member->skills->pluck('skill_name')->implode(', ') }}"
                            @if(old('assignees') && in_array($member->id_member, old('assignees'))) selected @endif>
                            {{ $member->member_name }}
                        </option>
                    @endforeach
                </select>
                @error('assignees')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- NOTE (QUILL JS EDITOR) --}}
            <div class="md:col-span-2 mb-15">
                <label class="block mb-2 font-medium">Note</label>
                <input type="hidden" name="note" id="hidden-note" value="{{ old('note') }}">
                <div id="note-editor"></div>
                @error('note')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex justify-end mt-8">
            <x-button-primary type="submit">
                Create Task
            </x-button-primary>
        </div>

    </form>
</x-card>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // ==========================================
    // 1. INISIALISASI TOM SELECT (ASSIGNEES WITH CUSTOM RENDER)
    // ==========================================
    new TomSelect('#assignees', {
        plugins: ['remove_button'],
        create: false,
        hideSelected: true,
        placeholder: 'Select assignees',
        render: {
            // Mengatur tampilan item di dalam list dropdown saat dibuka
            option: function(data, escape) {
                // Pecah string skill menjadi array jika ada data skill
                const skillsArray = data.skills ? data.skills.split(', ') : [];
                let skillsBadges = '';
                
                if (skillsArray.length > 0 && data.skills !== "") {
                    skillsBadges = skillsArray.map(skill => 
                        `<span class="bg-gray-100 text-gray-600 text-[10px] px-1.5 py-0.5 rounded-full border border-gray-200">${escape(skill)}</span>`
                    ).join(' ');
                } else {
                    skillsBadges = `<span class="text-gray-400 text-xs italic">No skills listed</span>`;
                }

                return `
                    <div class="p-2.5 border-b border-gray-50 cursor-pointer">
                        <div class="font-semibold text-gray-800">${escape(data.text)}</div>
                        <div class="text-xs text-orange-600 font-medium mb-1.5">${escape(data.position)}</div>
                        <div class="flex flex-wrap gap-1">${skillsBadges}</div>
                    </div>
                `;
            },
            // Mengatur tampilan teks tag ketika item sudah sukses dipilih/klik
            item: function(data, escape) {
                return `
                    <div class="flex items-center gap-1">
                        <span class="font-medium">${escape(data.text)}</span>
                        <span class="text-[10px] opacity-75 italic">(${escape(data.position)})</span>
                    </div>
                `;
            }
        }
    });


    // ==========================================
    // 2. INISIALISASI QUILL JS (CATATAN / NOTE)
    // ==========================================
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

    let quill = new Quill('#note-editor', {
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
        placeholder: 'Tulis deskripsi / catatan task di sini...'
    });

    const hiddenNoteInput = document.getElementById('hidden-note');
    if (hiddenNoteInput.value) {
        quill.root.innerHTML = hiddenNoteInput.value;
    }

    // ==========================================
    // 3. COPY DATA QUILL KE INPUT SEBELUM SUBMIT
    // ==========================================
    document.getElementById('create-task-form').addEventListener('submit', function (e) {
        const html = quill.root.innerHTML;
        const isEmpty = quill.getText().trim() === '' && !html.includes('attachment-box');
        hiddenNoteInput.value = isEmpty ? '' : html;
    });

    // ==========================================
    // 4. MENCEGAT KLIK JIKA ADA FILE YANG DIPASTE
    // ==========================================
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.attachment-box');
        if (!target) return;

        e.preventDefault(); 
        const fileUrl = target.getAttribute('href');
        const fileName = target.getAttribute('download') || 'download-file';

        window.location.href = `/tasks/download-file?path=${encodeURIComponent(fileUrl)}&name=${encodeURIComponent(fileName)}`;
    });

});
</script>
@endpush