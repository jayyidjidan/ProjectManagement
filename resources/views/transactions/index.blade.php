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
            Transactions
        </h1>
        <p class="text-gray-500">
            Manage all transactions in your company
        </p>
    </div>

    {{-- KONTANER FILTER DROPDOWN BARU --}}
    <div class="flex items-center gap-3">
        <div class="relative" id="filter-container">
            <button type="button" onclick="toggleFilterDropdown()" class="px-5 py-2.5 rounded-2xl border border-border bg-white flex items-center gap-2 hover:bg-gray-50 transition text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filters
            </button>

            <div id="filter-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-border z-50 p-5">
                <form action="{{ route('transactions.index') }}" method="GET">
                    
                    {{-- Menyimpan parameter sort dan search agar tidak hilang --}}
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif

                    {{-- 1. FILTER PROJECT --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select name="project" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Projects</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id_proyek }}" @selected(request('project') == $p->id_proyek)>
                                    {{ $p->nama_proyek }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. FILTER TRANSACTION TYPE --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                        <select name="id_jenis" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Types</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id_jenis }}" @selected(request('id_jenis') == $type->id_jenis)>
                                    {{ $type->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. FILTER PAYMENT METHOD --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Methods</option>
                            <option value="Transfer" @selected(request('payment_method') == 'Transfer')>Transfer</option>
                            <option value="Cash" @selected(request('payment_method') == 'Cash')>Cash</option>
                            <option value="QRIS" @selected(request('payment_method') == 'QRIS')>QRIS</option>
                        </select>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:text-gray-800 underline">
                            Clear Filter
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition">
                            Apply Filter
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<x-card>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b border-border">

                    <th class="p-4 text-left w-20">
                        No
                    </th>

                    <th class="p-4 text-left">
                        <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'tanggal_transaksi',
                                'direction' => request('direction') == 'asc' ? 'desc' : 'asc'
                            ]) }}">
                            Date
                        </a>
                    </th>

                    <th class="p-4 text-left">
                        Project
                    </th>

                    <th class="p-4 text-left">
                        Transaction Type
                    </th>

                    <th class="p-4 text-left">
                        <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'jumlah_transaksi',
                                'direction' => request('direction') == 'asc' ? 'desc' : 'asc'
                            ]) }}">
                            Amount
                        </a>
                    </th>

                    <th class="p-4 text-left">
                        Payment Method
                    </th>

                    <th class="p-4 text-left">
                        Note
                    </th>

                    <th class="p-4 text-left">
                        Proof
                    </th>

                    <th class="p-4 text-right w-48">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($transactions as $transaction)

                    <tr class="border-b border-border">

                        <td class="p-4">
                            {{ $transactions->firstItem() + $loop->index }}
                        </td>

                        <td class="p-4">
                            {{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d M Y') }}
                        </td>

                        <td class="p-4">
                            {{ $transaction->pembayaran->project->nama_proyek ?? '-' }}
                        </td>

                        <td class="p-4">
                            {{ $transaction->jenis->nama_jenis ?? '-' }}
                        </td>

                        <td class="p-4 font-medium text-green-600">
                            Rp {{ number_format($transaction->jumlah_transaksi, 0, ',', '.') }}
                        </td>

                        <td class="p-4">
                            {{ $transaction->metode_pembayaran ?? '-' }}
                        </td>

                        <td class="p-4">
                            {{ $transaction->note ?? '-' }}
                        </td>

                        <td class="p-4">
                            @if($transaction->bukti_transaksi)
                                <a href="{{ asset('storage/' . $transaction->bukti_transaksi) }}" target="_blank" class="text-primary">
                                    View Proof
                                </a>
                            @else
                                -
                            @endif
                        </td>

                        <td class="p-4">

                            <div class="flex justify-end gap-2">

                                <a href="{{ route('transactions.edit', $transaction) }}" class="px-4 py-2 rounded-xl border border-border hover:bg-gray-50">
                                    Edit
                                </a>

                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Delete this transaction?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9" class="text-center py-16 text-gray-500">
                            No Transactions Available
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-card>

<div class="mt-6">
    {{ $transactions->links() }}
</div>

{{-- SCRIPT JAVASCRIPT UNTUK TOGGLE DROPDOWN --}}
<script>
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filter-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Auto-close ketika klik di luar area dropdown filter
    window.addEventListener('click', function(e) {
        const filterContainer = document.getElementById('filter-container');
        if (filterContainer && !filterContainer.contains(e.target)) {
            document.getElementById('filter-dropdown').classList.add('hidden');
        }
    });
</script>

@endsection