@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <h3 class="fw-semibold mb-3 mb-md-0">⏱ Masa {{ ucfirst($jenis) }}</h3>

                <!-- 🔍 Filter -->
                <div class="d-flex align-items-center gap-2">
                    <label for="filterJenis" class="fw-semibold text-secondary">Filter Jenis Surat Jalan:</label>
                    <select id="filterJenis" class="form-select form-select-sm rounded-3" style="min-width: 220px;">
                        <option value="">Semua Jenis</option>
                        <!-- <option value="Normal">Normal</option> -->
                        <option value="Garansi">Garansi</option>
                        <option value="Peminjaman">Peminjaman</option>
                        <option value="Perbaikan">Perbaikan</option>
                        <!-- <option value="Manual">Manual</option> -->
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <!-- <table id="masaTable" class="table table-hover align-middle text-center"> -->
                    <table id="masaTable" class="table table-bordered table-striped" style="width:100%">
                    <thead class="table-light align-middle">
                        <tr>
                            <th>No</th>
                            <th>Nomor Surat</th>
                            <th>Jenis</th>
                            <th>Tanggal Keluar</th>
                            <th>Diberikan Kepada</th>
                            <th>Material</th>
                            <th>Keluar</th>
                            <th>Kembali</th>
                            <th>Sisa</th>
                            <th>Durasi</th>
                            <th>Progres</th>
                            <!-- <th>Tanggal Kembali</th> -->
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suratJalans as $i => $surat)
                            @foreach ($surat->details as $detail)
                                @php
                                    $keluar = \Carbon\Carbon::parse($surat->tanggal);
                                    $kembali = $detail->tanggal_kembali ? \Carbon\Carbon::parse($detail->tanggal_kembali) : now();
                                    $hari = $keluar->diffInDays($kembali);
                                    $masa = "{$hari} hari";
                                    $jumlahKeluar = $detail->quantity;
                                    $jumlahKembali = $detail->jumlah_kembali ?? 0;
                                    $sisa = $jumlahKeluar - $jumlahKembali;
                                    $persen = round(($jumlahKembali / max($jumlahKeluar, 1)) * 100);
                                    $namaMaterial = $detail->material->material_description ?? $detail->nama_barang_manual;
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-start">{{ $surat->nomor_surat }}</td>
                                    <td>{{ ucfirst($surat->jenis_surat_jalan) }}</td>
                                    <td>{{ $surat->tanggal->format('d/m/Y') }}</td>
                                    <td>{{ $surat->kepada }}</td>
                                    <td class="text-start">{{ $namaMaterial }}</td>
                                    <td class="fw-semibold text-primary">{{ $jumlahKeluar }}</td>
                                    <td class="fw-semibold text-success">{{ $jumlahKembali }}</td>
                                    <td class="fw-semibold text-danger">{{ $sisa }}</td>
                                    <td>{{ $masa }}</td>
                                    <td>
                                        <div class="progress mb-1" style="height: 8px; border-radius: 10px;">
                                            <div class="progress-bar bg-{{ $persen == 100 ? 'success' : ($persen >= 50 ? 'warning' : 'secondary') }}" 
                                                 style="width: {{ $persen }}%; border-radius: 10px;">
                                            </div>
                                        </div>
                                        <small class="text-muted fw-semibold">{{ $persen }}%</small>
                                    </td>
                                    <!-- <td>{{ $detail->tanggal_kembali ? \Carbon\Carbon::parse($detail->tanggal_kembali)->format('d/m/Y') : '-' }}</td> -->
                                    <td>
    <div class="d-flex justify-content-center gap-1 flex-wrap">
        <!-- Tombol Detail -->
        <button class="btn btn-info"
        title="Lihat Detail"
        onclick="viewHistory({{ $detail->id }})">
    <i class="fa fa-eye"></i>
</button>


        <!-- Tombol Pengembalian -->
        @if ($persen < 100)
            <a href="{{ route('surat-jalan.kembalikan.form', ['suratId' => $surat->id, 'detailId' => $detail->id]) }}"
               class="btn btn-primary"
               title="Pengembalian">
                <i class="fa fa-undo me-1"></i> Pengembalian
            </a>
        @else
            <!-- Tombol Hapus -->
            <form id="delete-detail-{{ $detail->id }}" 
                action="{{ route('surat.masa.hapus-detail', ['surat' => $surat->id, 'detail' => $detail->id]) }}" 
                method="POST" 
                style="display: none;">
                @csrf
                @method('DELETE')
            </form>

            <button class="btn btn-danger" 
                    title="Hapus Detail"
                    onclick="confirmDeleteDetail({{ $detail->id }})">
                <i class="fa fa-trash"></i>
            </button>

        @endif
    </div>
</td>


                                        <!-- Modal Pengembalian -->
                                        <div class="modal fade" id="modalKembalikan{{ $detail->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 shadow-sm border-0">
                                                    <div class="modal-header bg-primary text-white rounded-top-4">
                                                        <h5 class="modal-title"><i class="fa fa-undo me-2"></i>Form Pengembalian Barang</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <form action="{{ route('surat-jalan.kembalikan', ['suratId' => $surat->id, 'detailId' => $detail->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Jumlah yang dikembalikan</label>
                                                                <input type="number" name="jumlah_kembali" class="form-control rounded-3" min="1" max="{{ $sisa }}" value="{{ $sisa }}" required>
                                                            </div>
                                                            <!-- <div class="mb-3">
                                                                <label class="form-label fw-semibold">Tanggal Kembali</label>
                                                                <input type="date" name="tanggal_kembali" class="form-control rounded-3" value="{{ now()->toDateString() }}" required>
                                                            </div> -->
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary rounded-pill">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    body { background-color: #f8fafc; }
    .card { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    table th, table td { vertical-align: middle; padding: 0.75rem; }
    .table-responsive { border-radius: 0.5rem; overflow-x: auto; }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
    }
    #masaTable th, #masaTable td {
    padding: 10px !important;
}

#masaTable thead th {
    background: #f8f9fa !important;
    font-weight: 600;
}

    .btn { padding: 6px 12px; font-size: 14px; transition: all 0.2s; }
    .btn:hover { transform: translateY(-1px); }
    .modal-content { border-radius: 1rem; }
    .modal-header { border-bottom: none; }
    .modal-footer { border-top: none; }
    .progress { background-color: #e9ecef; }
    .form-select { border-radius: 0.375rem; }
    .form-control { border-radius: 0.375rem; }
    @media (max-width: 768px) {
        .d-flex.flex-wrap { flex-direction: column; align-items: stretch; }
        .table-responsive { font-size: 0.875rem; }
        th, td { padding: 0.5rem; }
    }
</style>
@endpush

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        const table = $('#masaTable').DataTable({
            language: {
                search: "🔍 Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: { previous: "Sebelumnya", next: "Berikutnya" },
                zeroRecords: "Tidak ada data ditemukan"
            },
            pageLength: 10,
            ordering: true,
            responsive: true
        });

        $('#filterJenis').on('change', function() {
            const value = $(this).val();
            table.column(2).search(value ? '^' + value + '$' : '', true, false).draw();
        });
    });

    function viewData(data) {
        Swal.fire({
            title: `<strong>📦 Detail Surat Jalan</strong>`,
            html: `
                <div class="text-start">
                    <table class="table table-sm table-bordered">
                        <tr><th>Nomor Surat</th><td>${data.nomor}</td></tr>
                        <tr><th>Jenis</th><td>${data.jenis}</td></tr>
                        <tr><th>Tanggal Keluar</th><td>${data.tanggal}</td></tr>
                        <tr><th>Diberikan Kepada</th><td>${data.kepada}</td></tr>
                        <tr><th>Material</th><td>${data.material}</td></tr>
                        <tr><th>Jumlah Keluar</th><td>${data.keluar}</td></tr>
                        <tr><th>Jumlah Kembali</th><td>${data.kembali}</td></tr>
                        <tr><th>Sisa</th><td>${data.sisa}</td></tr>
                        <tr><th>Tanggal Kembali</th><td>${data.tanggal_kembali ?? '-'}</td></tr>
                        <tr><th>Durasi</th><td>${data.masa}</td></tr>
                        <tr><th>Progres</th><td>${data.progres}%</td></tr>
                    </table>
                </div>
            `,
            confirmButtonText: 'Tutup',
            width: 'auto',
            customClass: {
                popup: 'rounded-4'
            }
        });
    }

    function viewHistory(detailId) {
    Swal.fire({
        title: '⏳ Memuat data...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`/surat-jalan/${detailId}/history`)
        .then(res => res.json())
        .then(res => {
            if (!res.success) throw new Error('Gagal ambil data');

            const d = res.detail;
            const historyRows = res.history.length
                ? res.history.map((h, i) => `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${h.nomor_surat_masuk}</td>
                        <td>${h.tanggal_masuk}</td>
                        <td>${h.jumlah_kembali}</td>
                        <td>${h.keterangan}</td>
                    </tr>
                `).join('')
                : `<tr><td colspan="5" class="text-center text-muted">Belum ada pengembalian</td></tr>`;

            Swal.fire({
                title: `<strong>📦 Detail Pengembalian</strong>`,
                width: '800px',
                html: `
                    <div class="text-start mb-3">
                        <table class="table table-sm table-bordered">
                            <tr><th>Nomor Surat</th><td>${d.nomor_surat}</td></tr>
                            <tr><th>Tanggal Keluar</th><td>${d.tanggal_keluar}</td></tr>
                            <tr><th>Material</th><td>${d.material}</td></tr>
                            <tr><th>Keluar</th><td>${d.keluar}</td></tr>
                            <tr><th>Kembali</th><td>${d.kembali}</td></tr>
                            <tr><th>Sisa</th><td>${d.sisa}</td></tr>
                        </table>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-2">Riwayat Pengembalian</h6>
                        <table class="table table-sm table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>No Surat Masuk</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Jumlah Kembali</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${historyRows}
                            </tbody>
                        </table>
                    </div>
                `,
                confirmButtonText: 'Tutup'
            });
        })
        .catch(err => {
            Swal.fire('Gagal', 'Tidak dapat memuat data.', 'error');
            console.error(err);
        });
}


    function confirmDeleteDetail(id) {
    Swal.fire({
        title: 'Hapus Data?',
        text: 'Apakah kamu yakin ingin menghapus detail ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-detail-' + id).submit();
        }
    });
}

</script>
@endpush
@endsection