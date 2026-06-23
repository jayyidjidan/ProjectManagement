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
        Invitations
    </h1>

    <p class="text-gray-500 mt-2">
        Manage employee invitations
    </p>

</div>

<a href="{{ route('invitations.create') }}">

    <x-button-primary>
        Send Invitation
    </x-button-primary>

</a>

</div>

<x-card>

<div class="overflow-x-auto">

    <table class="w-full">

        <thead>

            <tr class="border-b border-border">

                <th class="p-4 text-left">
                    Email
                </th>

                <th class="p-4 text-left">
                    Role
                </th>

                <th class="p-4 text-left">
                    Status
                </th>

                <th class="p-4 text-left">
                    Expired At
                </th>

                <th class="p-4 text-right">
                    Action
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($invitations as $invitation)

            <tr class="border-b border-border">

                <td class="p-4">
                    {{ $invitation->email }}
                </td>

                <td class="p-4">
                    {{ $invitation->role->role_name ?? '-' }}
                </td>

                <td class="p-4">

                    @if($invitation->is_used)

                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs">
                            Accepted
                        </span>

                    @elseif($invitation->expired_at < now())

                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs">
                            Expired
                        </span>

                    @else

                        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs">
                            Pending
                        </span>

                    @endif

                </td>

                <td class="p-4">
                    {{ $invitation->expired_at?->format('d M Y') }}
                </td>
                
                <td class="p-4 text-right">

                    <form
                        action="{{ route('invitations.destroy',$invitation) }}"
                        method="POST">

                        @csrf
                        @method('DELETE')

                        <button
                            onclick="return confirm('Delete invitation?')"
                            class="text-red-600">

                            Delete

                        </button>

                    </form>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="6" class="text-center py-10 text-gray-500">
                    No Invitations
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-6">

    {{ $invitations->links() }}

</div>

</x-card>

@endsection
