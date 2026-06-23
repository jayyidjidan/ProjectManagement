<!DOCTYPE html>
<html>
<head>
    <title>Laporan Project</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .badge { font-size: 10px; padding: 2px 4px; border-radius: 4px; background: #eee; display: inline-block; margin-bottom: 2px;}
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Data Project</h2>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="20%">Nama Project</th>
                <th width="15%">Klien</th>
                <th width="15%">Project Manager</th>
                <th width="15%">Kategori</th>
                <th width="10%">Tipe</th>
                <th width="10%">Status</th>
                <th width="12%">Deadline</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $index => $project)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $project->nama_proyek }}</td>
                <td>{{ $project->klien->nama_klien ?? '-' }}</td>
                <td>{{ $project->projectManager->member_name ?? '-' }}</td>
                <td>
                    @forelse($project->kategoris as $category)
                        <span class="badge">{{ $category->nama_kategori }}</span>
                    @empty
                        -
                    @endforelse
                </td>
                <td>{{ $project->tipe->nama_tipe ?? '-' }}</td>
                <td>{{ $project->status->nama_status ?? '-' }}</td>
                <td>
                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data project yang sesuai dengan filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>