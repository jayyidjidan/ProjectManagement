@extends('layouts.app')

@section('content')

@if (session('success'))
    <div
        class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-end justify-between mb-6">

    <div class="space-y-2">

        <h1 class="text-4xl font-bold">
            Payments
        </h1>

        <p class="text-gray-500">
            Manage project payments and transactions
        </p>

    </div>

</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    <a
        href="{{ route('payments.index') }}"
        class="p-5 rounded-2xl border border-border bg-white">

        <p class="text-sm text-gray-500">
            Total Payments
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $total }}
        </h2>

    </a>

    <a
        href="{{ route('payments.index', ['status' => 'finished']) }}"
        class="p-5 rounded-2xl border border-border bg-white">

        <p class="text-sm text-green-600">
            Finished
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $finished }}
        </h2>

    </a>

    <a
        href="{{ route('payments.index', ['status' => 'remaining']) }}"
        class="p-5 rounded-2xl border border-border bg-white">

        <p class="text-sm text-orange-600">
            Remaining
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $remaining }}
        </h2>

    </a>

</div>

<x-card>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b border-border">

                    <th class="text-left p-4 w-20">
                        No
                    </th>

                    <th class="text-left p-4">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'id_pembayaran',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}">

                            Project

                        </a>

                    </th>

                    <th class="text-left p-4">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'total_pembayaran',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}">

                            Total Payment

                        </a>

                    </th>

                    <th class="text-left p-4">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'total_dibayarkan',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}">

                            Paid

                        </a>

                    </th>

                    <th class="text-left p-4">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'sisa_pembayaran',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}">

                            Remaining

                        </a>

                    </th>

                    <th class="text-left p-4">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'jumlah_pembayaran',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}">

                            Installments

                        </a>

                    </th>

                    <th class="text-left p-4">
                        Status
                    </th>

                    <th class="text-right p-4 w-64">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($payments as $payment)

                    <tr class="border-b border-border">

                        <td class="p-4">

                            {{ $payments->firstItem() + $loop->index }}

                        </td>

                        <td class="p-4 font-medium">

                            {{ $payment->project->nama_proyek ?? '-' }}

                        </td>

                        <td class="p-4">

                            Rp {{ number_format($payment->total_pembayaran, 0, ',', '.') }}

                        </td>

                        <td class="p-4 text-green-600">

                            Rp {{ number_format($payment->total_dibayarkan, 0, ',', '.') }}

                        </td>

                        <td class="p-4 text-red-600">

                            Rp {{ number_format($payment->sisa_pembayaran, 0, ',', '.') }}

                        </td>

                        <td class="p-4">

                            {{ $payment->jumlah_pembayaran }}

                        </td>

                        <td class="p-4">

                            @if($payment->sisa_pembayaran <= 0)

                                <span
                                    class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">

                                    Finished

                                </span>

                            @else

                                <span
                                    class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-sm">

                                    Remaining

                                </span>

                            @endif

                        </td>

                        <td class="p-4">

                            <div class="flex items-center justify-end gap-2">

                                <a
                                    href="{{ route('payments.show', $payment) }}"
                                    class="px-4 py-2 whitespace-nowrap rounded-xl border border-border hover:bg-gray-50">

                                    View

                                </a>

                                <a
                                    href="{{ route('transactions.create', ['payment' => $payment->id_pembayaran]) }}"
                                    class="px-4 py-2 whitespace-nowrap rounded-xl border border-primary text-primary hover:bg-orange-50">

                                    Add Transaction

                                </a>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-16 text-gray-500">

                            No Payments Available

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-card>

<div class="mt-6">

    {{ $payments->links() }}

</div>

@endsection