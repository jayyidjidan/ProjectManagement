@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-4xl font-bold">Download Laporan Project</h1>
        <p class="text-gray-500 mt-2">Filter data project yang ingin Anda jadikan laporan PDF.</p>
    </div>

    <x-card>
        <form action="{{ route('reports.projects.download') }}" method="GET" target="_blank">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian Nama Project</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama project..." class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary outline-none bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary outline-none bg-gray-50">
                        <option value="">Semua Status</option>
                        @foreach($filterStatuses as $status)
                            <option value="{{ $status->id_status }}">{{ $status->nama_status }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select name="type" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary outline-none bg-gray-50">
                        <option value="">Semua Tipe</option>
                        @foreach($filterTypes as $type)
                            <option value="{{ $type->id_tipe }}">{{ $type->nama_tipe }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Manager</label>
                    <select name="manager" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary outline-none bg-gray-50">
                        <option value="">Semua PM</option>
                        @foreach($filterManagers as $manager)
                            <option value="{{ $manager->id_member }}">{{ $manager->member_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori (Bisa pilih lebih dari satu)</label>
                <div class="max-h-48 overflow-y-auto border border-border bg-gray-50 rounded-xl p-4 grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($filterCategories as $category)
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-100 p-2 rounded">
                            <input type="checkbox" name="categories[]" value="{{ $category->id_kategori }}" class="rounded text-primary border-gray-300">
                            {{ $category->nama_kategori }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-t border-border pt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</label>
                    <select name="sort" class="w-full rounded-xl border border-border p-2.5 text-sm bg-gray-50">
                        <option value="created_at">Tanggal Dibuat</option>
                        <option value="nama_proyek">Nama Project</option>
                        <option value="deadline">Deadline</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Arah Urutan</label>
                    <select name="direction" class="w-full rounded-xl border border-border p-2.5 text-sm bg-gray-50">
                        <option value="desc">Menurun (Terbaru/Z-A)</option>
                        <option value="asc">Menaik (Terlama/A-Z)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                    Download PDF
                </button>
            </div>

        </form>
    </x-card>
</div>
@endsection