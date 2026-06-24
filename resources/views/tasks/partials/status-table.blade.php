{{-- ERROR DARI CONTROLLER (FORM SUBMIT BIASA) --}}
@if($errors->any())
<div class="mb-6 p-4 rounded-2xl border border-red-200 bg-red-50 text-red-700">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ERROR DARI AJAX INLINE UPDATE (AWALNYA HIDDEN) --}}
<div id="ajax-error-alert" class="hidden mb-6 p-4 rounded-2xl border border-red-200 bg-red-50 text-red-700">
    <ul id="ajax-error-list" class="list-disc pl-5">
        </ul>
</div>

<x-card class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">
            {{ $title }}
            <span class="text-gray-500">
                ({{ $tasks->count() }})
            </span>
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
                <thead>
                    <tr class="border-b border-border">
                        <th class="p-4 text-left">Task Name</th>
                        <th class="p-4 text-left">Project</th>
                        <th class="p-4 text-left">Assignees</th>
                        <th class="p-4 text-left">Status</th>
                        
                        {{-- KOLOM PRIORITY BISA DIKLIK --}}
                        <th class="p-4 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id_priority', 'direction' => request('sort') === 'id_priority' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                            class="flex items-center hover:text-blue-600 transition-colors">
                                Priority
                                @if(request('sort') === 'id_priority')
                                    <span class="ml-1 text-xs">
                                        {!! request('direction') === 'asc' ? '▲' : '▼' !!}
                                    </span>
                                @else
                                    <span class="ml-1 text-xs text-gray-300">↕</span>
                                @endif
                            </a>
                        </th>

                        {{-- KOLOM DEADLINE BISA DIKLIK --}}
                        <th class="p-4 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'deadline_task', 'direction' => request('sort') === 'deadline_task' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                            class="flex items-center hover:text-blue-600 transition-colors">
                                Deadline
                                @if(request('sort') === 'deadline_task')
                                    <span class="ml-1 text-xs">
                                        {!! request('direction') === 'asc' ? '▲' : '▼' !!}
                                    </span>
                                @else
                                    <span class="ml-1 text-xs text-gray-300">↕</span>
                                @endif
                            </a>
                        </th>

                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
            <tbody>
                @forelse($tasks as $task)
                    {{-- 1. Gunakan class khusus 'task-row' dan 'data-href' alih-alih onclick --}}
                    <tr
                        data-href="{{ route('tasks.show', $task) }}"
                        class="task-row cursor-pointer hover:bg-gray-50 transition border-b border-border">

                        {{-- 2. Hapus semua onclick="event.stopPropagation()" dari tag <td> --}}
                        
                        {{-- TASK NAME --}}
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <!-- Input Inline Edit Bawaan Kamu -->
                                <input
                                    type="text"
                                    value="{{ $task->nama_task }}"
                                    data-id="{{ $task->id_task }}"
                                    data-field="nama_task"
                                    class="inline-edit w-full bg-transparent border-none focus:ring-0 focus:border-b">

                                <!-- Indikator Komen Belum Dibaca -->
                                @if($task->has_unread_comments)
                                    <div class="relative flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white shrink-0" title="Ada komentar baru">
                                        <!-- Efek berdenyut (ping) ala Messenger -->
                                        <span class="absolute inline-flex w-full h-full rounded-full bg-red-400 opacity-75 animate-ping"></span>
                                        
                                        <!-- Ikon Chat Kecil -->
                                        <svg class="w-3 h-3 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- PROJECT --}}
                        <td class="p-4">
                            <select
                                data-id="{{ $task->id_task }}"
                                data-field="id_proyek"
                                class="inline-edit bg-transparent border-none focus:ring-0">
                                <option value="">Personal</option>
                                @foreach($projects as $project)
                                    <option
                                        value="{{ $project->id_proyek }}"
                                        @selected($task->id_proyek == $project->id_proyek)>
                                        {{ $project->nama_proyek }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- ASSIGNEES --}}
                        <td class="p-4">
                            <select
                                multiple
                                data-id="{{ $task->id_task }}"
                                data-field="assignees"
                                class="assignee-edit tom-assignee">
                                @foreach($members as $member)
                                    <option
                                        value="{{ $member->id_member }}"
                                        @selected($task->assignees->contains('id_member', $member->id_member))>
                                        {{ $member->member_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- STATUS --}}
                        <td class="p-4">
                            <select
                                data-id="{{ $task->id_task }}"
                                data-field="id_status"
                                class="inline-edit bg-transparent border-none focus:ring-0">
                                @foreach($statuses as $status)
                                    <option
                                        value="{{ $status->id_status }}"
                                        @selected($task->id_status == $status->id_status)>
                                        {{ $status->status_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- PRIORITY --}}
                        <td class="p-4">
                            <select
                                data-id="{{ $task->id_task }}"
                                data-field="id_priority"
                                class="inline-edit bg-transparent border-none focus:ring-0">
                                @foreach($priorities as $priority)
                                    <option
                                        value="{{ $priority->id_priority }}"
                                        @selected($task->id_priority == $priority->id_priority)>
                                        {{ $priority->priority_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- DEADLINE --}}
                        <td class="p-4">
                            <input
                                type="date"
                                value="{{ $task->deadline_task }}"
                                data-id="{{ $task->id_task }}"
                                data-field="deadline_task"
                                class="inline-edit bg-transparent border-none focus:ring-0">
                        </td>

                        {{-- ACTION --}}
                        <td class="p-4 text-right">
                            <form
                                action="{{ route('tasks.destroy', $task) }}"
                                method="POST"
                                onsubmit="return confirm('Delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="text-red-600 hover:text-red-700">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="7"
                            class="text-center py-10 text-gray-500">
                            No Tasks Available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<script>
// ================================
// FUNGSI MENAMPILKAN ERROR KE HTML
// ================================
function showAjaxError(message) {
    const alertBox = document.getElementById('ajax-error-alert');
    const errorList = document.getElementById('ajax-error-list');
    
    // Isi list dengan pesan error
    errorList.innerHTML = `<li>${message}</li>`;
    
    // Tampilkan kotak error
    alertBox.classList.remove('hidden');
    
    // Scroll ke atas agar user bisa melihat error-nya
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Sembunyikan kembali kotak error setelah 6 detik
    setTimeout(() => {
        alertBox.classList.add('hidden');
    }, 6000);
}

// ================================
// ROW CLICK HANDLER
// ================================
document.querySelectorAll('.task-row').forEach(row => {
    row.addEventListener('click', function (e) {
        if (e.target.closest('input, select, button, form, .ts-wrapper')) {
            return;
        }
        window.location.href = this.dataset.href;
    });
});

// ================================
// STANDARD INLINE EDIT
// ================================
document.querySelectorAll('.inline-edit').forEach(element => {
    // Simpan nilai awal untuk berjaga-jaga jika update gagal
    let previousValue = element.value; 

    // Update nilai awal setiap kali elemen diklik sebelum diganti
    element.addEventListener('focus', function () {
        previousValue = this.value;
    });

    element.addEventListener('change', async function () {
        try {
            const response = await fetch(`/tasks/${this.dataset.id}/inline-update`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    field: this.dataset.field,
                    value: this.value
                })
            });

            const data = await response.json();

            // JIKA GAGAL: Tampilkan error HTML & kembalikan nilai dropdown
            if (response.status === 422 || response.status === 400) {
                showAjaxError(data.message);
                this.value = previousValue; // Revert ke nilai sebelumnya
                return;
            }

            // JIKA SUKSES: Update nilai awal dengan yang baru
            if (data.success) {
                previousValue = this.value; 
                document.getElementById('ajax-error-alert').classList.add('hidden'); // Sembunyikan error jika ada
            }
        } catch (error) {
            showAjaxError('Terjadi kesalahan pada server saat menyimpan data.');
            this.value = previousValue; // Revert ke nilai sebelumnya
        }
    });
});

// ================================
// ASSIGNEE INLINE EDIT (TOM SELECT)
// ================================
document.querySelectorAll('.assignee-edit').forEach(element => {
    // Simpan nilai awal (karena bisa lebih dari satu / array)
    let previousValues = Array.from(element.selectedOptions).map(opt => opt.value);

    element.addEventListener('change', async function () {
        const values = Array.from(this.selectedOptions).map(option => option.value);

        try {
            const response = await fetch(`/tasks/${this.dataset.id}/inline-update`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    field: 'assignees',
                    value: values
                })
            });

            const data = await response.json();

            // JIKA GAGAL
            if (response.status === 422 || response.status === 400) {
                showAjaxError(data.message);
                
                // Kembalikan TomSelect ke nilai awal secara "diam-diam" (tanpa trigger change lagi)
                const tom = this.tomselect;
                tom.setValue(previousValues, true); 
                return;
            }

            // JIKA SUKSES
            if (data.success) {
                previousValues = values; // Update riwayat nilai
                document.getElementById('ajax-error-alert').classList.add('hidden');
            }
        } catch (error) {
            showAjaxError('Terjadi kesalahan pada server saat memperbarui assignee.');
            this.tomselect.setValue(previousValues, true); // Revert
        }
    });
});

// ================================
// TOM SELECT INIT
// ================================
document.querySelectorAll('.tom-assignee').forEach(select => {
    new TomSelect(select, {
        plugins: ['remove_button'],
        create: false,
        persist: false,
        maxItems: null
    });
});
</script>
