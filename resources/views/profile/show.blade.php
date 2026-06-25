@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold">
                My Profile
            </h1>
            <p class="text-gray-500 mt-2">
                Your profile and performance overview
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('profile.edit') }}">
                <x-button-primary>
                    Edit Profile
                </x-button-primary>
            </a>

            <a href="{{ route('dashboard') }}">
                <x-button-secondary>
                    Back to Dashboard
                </x-button-secondary>
            </a>
        </div>
    </div>

    {{-- PROFILE CARD --}}
    <x-card>
        <div class="flex flex-col md:flex-row gap-6 items-center">
            <div>
                @if($member->profile_photo)
                    <img
                        src="{{ asset('storage/' . $member->profile_photo) }}"
                        class="w-32 h-32 rounded-full object-cover border border-border">
                @else
                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode($member->member_name) }}&size=200"
                        class="w-32 h-32 rounded-full border border-border">
                @endif
            </div>

            <div class="flex-1">
                <h2 class="text-3xl font-bold">
                    {{ $member->member_name }}
                </h2>
                <p class="text-gray-500 mt-1">
                    {{ $member->jabatan?->position_name ?? '-' }}
                </p>
                <p class="text-gray-500">
                    {{ $member->user?->role?->role_name ?? '-' }}
                </p>
                <p class="text-sm text-gray-400 mt-3">
                    {{ $member->user?->email }}
                </p>
                <p class="text-sm text-gray-400">
                    Joined :
                    {{ $member->joined_date
                        ? \Carbon\Carbon::parse($member->joined_date)->format('d M Y')
                        : '-' }}
                </p>
            </div>
        </div>
    </x-card>
    
    {{-- SKILLS --}}
    <x-card>
        <h2 class="text-xl font-semibold mb-4">
            Skills
        </h2>
        <div class="flex flex-wrap gap-2">
            @forelse($member->skills as $skill)
                <span class="px-3 py-1 rounded-full border border-border text-sm">
                    {{ $skill->skill_name }}
                </span>
            @empty
                <span class="text-gray-500">
                    No Skills
                </span>
            @endforelse
        </div>
    </x-card>

    {{-- MONTHLY REPORT (DISAMAKAN DENGAN MEMBER) --}}
    <x-card>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">
                Monthly Report
            </h2>
            <span class="text-gray-500 text-sm">
                {{ now()->format('F Y') }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div>
                <p class="text-gray-500 text-sm">
                    Attendance
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    {{ $monthlyAttendance }} Days
                </h3>
            </div>

            <div>
                <p class="text-gray-500 text-sm">
                    Points
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    {{ $member->point }}
                </h3>
            </div>

            <div>
                <p class="text-gray-500 text-sm">
                    WFH
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    {{ $member->total_WFH }}
                </h3>
            </div>

            <div>
                <p class="text-gray-500 text-sm">
                    Leave
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    {{ $member->total_cuti }}
                </h3>
            </div>

            {{-- DURASI OVERTIME --}}
            <div>
                <p class="text-gray-500 text-sm">
                    Overtime Duration
                </p>
                <h3 class="text-2xl font-bold mt-1 text-orange-600">
                    {{ $monthlyOvertime ?? 0 }} Hours
                </h3>
            </div>
        </div>
    </x-card>

    {{-- TASKS SUMMARY --}}
    <x-card>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">
                Assigned Tasks Summary
            </h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="p-4 border border-border rounded-xl bg-gray-50">
                <p class="text-gray-500 text-sm font-medium">Total Tasks</p>
                <h2 class="text-3xl font-bold mt-2 text-gray-800">
                    {{ $tasks->count() }}
                </h2>
            </div>

            <div class="p-4 border border-border rounded-xl bg-white">
                <p class="text-gray-500 text-sm font-medium">Planning</p>
                <h2 class="text-3xl font-bold text-yellow-600 mt-2">
                    {{ $planning->count() }}
                </h2>
            </div>

            <div class="p-4 border border-border rounded-xl bg-white">
                <p class="text-gray-500 text-sm font-medium">On Going</p>
                <h2 class="text-3xl font-bold text-blue-600 mt-2">
                    {{ $ongoing->count() }}
                </h2>
            </div>

            <div class="p-4 border border-border rounded-xl bg-white">
                <p class="text-gray-500 text-sm font-medium">Reviewed</p>
                <h2 class="text-3xl font-bold text-purple-600 mt-2">
                    {{ $reviewed->count() }}
                </h2>
            </div>

            <div class="p-4 border border-border rounded-xl bg-white">
                <p class="text-gray-500 text-sm font-medium">Finished</p>
                <h2 class="text-3xl font-bold text-green-600 mt-2">
                    {{ $finished->count() }}
                </h2>
            </div>

            <div class="p-4 border border-border rounded-xl bg-white">
                <p class="text-gray-500 text-sm font-medium">Overdue</p>
                <h2 class="text-3xl font-bold text-red-600 mt-2">
                    {{ $overdue->count() }}
                </h2>
            </div>
        </div>
    </x-card>

    {{-- RECENT SCRUM --}}
    <x-card>
        <h2 class="text-xl font-semibold mb-4">
            Recent Scrum Activities
        </h2>

        <div class="space-y-4">
            @forelse($member->scrumUpdates->sortByDesc('created_at')->take(10) as $update)
                <div class="border border-border rounded-xl p-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">
                            {{ $update->created_at->format('d M Y H:i') }}
                        </span>
                    </div>

                    @if($update->task1)
                        <p>
                            <span class="font-medium">
                                {{ $update->task1->nama_task }}
                            </span>
                            - {{ $update->target_1 }}
                        </p>
                    @endif

                    @if($update->task2)
                        <p class="mt-1">
                            <span class="font-medium">
                                {{ $update->task2->nama_task }}
                            </span>
                            - {{ $update->target_2 }}
                        </p>
                    @endif
                </div>
            @empty
                <p class="text-gray-500">
                    No Scrum Activities
                </p>
            @endforelse
        </div>
    </x-card>

</div>

@endsection