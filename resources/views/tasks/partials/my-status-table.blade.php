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

                    {{-- KOLOM PRIORITY BISA DIKLIK (SORT) --}}
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

                    <th class="p-4 text-left">Status</th>

                    {{-- KOLOM DEADLINE BISA DIKLIK (SORT) --}}
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
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr
                    onclick="window.location='{{ route('tasks.show', $task) }}'"
                    class="cursor-pointer hover:bg-gray-50 transition border-b border-border">

                    <td class="p-4">{{ $task->nama_task }}</td>

                    <td class="p-4">{{ $task->project?->nama_proyek ?? 'Personal' }}</td>

                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full border text-xs">
                            {{ $task->priority?->priority_name }}
                        </span>
                    </td>

                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-xs">
                            {{ $task->status?->status_name }}
                        </span>
                    </td>

                    <td class="p-4">{{ $task->deadline_task }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">
                        No Tasks Available
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>