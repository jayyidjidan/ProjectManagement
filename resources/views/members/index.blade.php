@extends('layouts.app')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="flex items-end justify-between mb-6">

    <div>
        <h1 class="text-4xl font-bold">
            Employees
        </h1>
        <p class="text-gray-500 mt-2">
            Manage all employees
        </p>
    </div>

    <div class="flex items-center gap-3">
        {{-- KONTANER FILTER DROPDOWN EMPLOYEES --}}
        <div class="relative" id="filter-container">
            <button type="button" onclick="toggleFilterDropdown()" class="px-5 py-2.5 rounded-2xl border border-border bg-white flex items-center gap-2 hover:bg-gray-50 transition text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filters
            </button>

            <div id="filter-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-border z-50 p-5 max-h-[85vh] overflow-y-auto">
                <form action="{{ route('members.index') }}" method="GET">
                    
                    {{-- Proteksi parameter pencarian global agar tidak hilang --}}
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif

                    {{-- 1. FILTER POSITION --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <select name="position" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Positions</option>
                            @foreach($positions as $p)
                                <option value="{{ $p->id_position }}" @selected(request('position') == $p->id_position)>
                                    {{ $p->position_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. FILTER ROLE --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Roles</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id_role }}" @selected(request('role') == $r->id_role)>
                                    {{ $r->role_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. FILTER MULTI-SKILLS (Scrollable Checkbox Block) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Skills (Choose multiple)</label>
                        <div class="max-h-36 overflow-y-auto border border-border rounded-xl p-3 bg-gray-50 space-y-2">
                            @foreach($skills as $s)
                                <label class="flex items-center gap-2.5 text-sm text-gray-700 cursor-pointer select-none hover:bg-gray-100 p-1 rounded-lg transition">
                                    <input type="checkbox" name="skills[]" value="{{ $s->id_skill }}" 
                                        @checked(in_array($s->id_skill, (array)request('skills')))
                                        class="rounded border-gray-300 text-black focus:ring-black">
                                    <span class="truncate">{{ $s->skill_name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                        <a href="{{ route('members.index') }}" class="text-sm text-gray-500 hover:text-gray-800 underline">
                            Clear Filter
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition">
                            Apply Filter
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <a href="{{ route('invitations.create') }}">
            <x-button-primary>
                Invite Employee
            </x-button-primary>
        </a>
    </div>

</div>

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="p-4 text-left">Employee</th>
                    <th class="p-4 text-left">Position</th>
                    <th class="p-4 text-left">Role</th>
                    <th class="p-4 text-left">Skills</th> {{-- KOLOM BARU --}}
                    <th class="p-4 text-left">Joined Date</th>
                    <th class="p-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr onclick="window.location='{{ route('members.show',$member) }}'"
                    class="cursor-pointer hover:bg-gray-50 border-b border-border">
                    
                    <td class="p-4">
                        <div>
                            <p class="font-medium">{{ $member->member_name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->user?->email }}</p>
                        </div>
                    </td>

                    <td class="p-4">
                        {{ $member->jabatan?->position_name ?? '-' }}
                    </td>

                    <td class="p-4">
                        {{ $member->user?->role?->role_name ?? '-' }}
                    </td>

                    {{-- ISI DATA KOLOM SKILLS BARU --}}
                    <td class="p-4" onclick="event.stopPropagation()">
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            @forelse($member->skills as $skill)
                                <span class="bg-gray-100 text-gray-600 text-[11px] font-medium px-2 py-0.5 rounded-full border border-gray-200">
                                    {{ $skill->skill_name }}
                                </span>
                            @empty
                                <span class="text-gray-400 text-xs">-</span>
                            @endforelse
                        </div>
                    </td>

                    <td class="p-4">
                        {{ $member->joined_date ? \Carbon\Carbon::parse($member->joined_date)->format('d M Y') : '-' }}
                    </td>

                    <td class="p-4 text-right" onclick="event.stopPropagation()">
                        <form action="{{ route('members.destroy',$member) }}" method="POST" onsubmit="return confirm('Delete employee?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-700">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-500">
                        No Employees Found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $members->links() }}
    </div>
</x-card>

{{-- SCRIPT TOGGLE DROPDOWN --}}
<script>
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filter-dropdown');
        dropdown.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const filterContainer = document.getElementById('filter-container');
        if (filterContainer && !filterContainer.contains(e.target)) {
            document.getElementById('filter-dropdown').classList.add('hidden');
        }
    });
</script>

@endsection