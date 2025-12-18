@extends('layouts.app')

@section('title', 'Daftar SAP')
@section('page-title', 'Daftar SAP')

@push('styles')
<style>
    * { box-sizing: border-box !important; }
    html, body { background-color: #f0f0f0 !important; overflow-x: auto !important; overflow-y: auto !important; }
    .table-responsive { background-color: #fff !important; padding: 15px !important; margin: 10px 0 !important; overflow: auto !important; }
    table { width: 100% !important; border-collapse: separate !important; }
    thead { background-color: #000 !important; color: #fff !important; }
    th, td { border: 1px solid #dee2e6 !important; padding: 8px !important; }
    tbody tr:nth-child(even) { background-color: #f8f9fa; }
    tbody tr:hover { background-color: #e9ecef !important; }
    .badge { font-weight: 500; }
</style>
@endpush

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">📋 Daftar Status SAP Material Masuk</h5>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="#">Butuh Persetujuan 
                    <span class="badge bg-warning text-dark">{{ $materialBelumSelesai->count() ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Approved 
                    <span class="badge bg-success">{{ $materialSelesai->count() ?? 0 }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        {{-- 🔍 PENCARIAN --}}
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan Nomor KR, Pabrikan, PO, DOC, atau Jenis...">
        </div>

        {{-- ✅ BELUM SELESAI SAP --}}
        <h6 class="fw-bold">Belum Selesai SAP</h6>
        @if($materialBelumSelesai->isEmpty())
            <p class="text-muted fst-italic">Belum ada data material.</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="tableBelumSAP">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor KR</th>
                        <th>Pabrikan</th>
                        <th>Nomor PO</th>
                        <th>Nomor DOC</th>
                        <th>Jenis</th>
                        <th>Tug 4</th>
                        <!-- <th>Tanggal Masuk</th>
                        <th>Tanggal Keluar</th> -->
                        <th>Material</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materialBelumSelesai as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nomor_kr ?? '-' }}</td>
                            <td>{{ $item->pabrikan ?? '-' }}</td>
                            <td>{{ $item->nomor_po ?? '-' }}</td>
                            <td>{{ $item->nomor_doc ?? '-' }}</td>
                            <td>{{ $item->jenis ?? '-' }}</td>
                            <td>{{ $item->tugas_4 ?? '-' }}</td>
                            <!-- <td>{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->tanggal_keluar ? \Carbon\Carbon::parse($item->tanggal_keluar)->format('d/m/Y') : '-' }}</td> -->
                            <td>
                                @forelse ($item->details as $detail)
                                    {{ $detail->material->material_description ?? '-' }} ({{ $detail->quantity }} {{ $detail->satuan }})<br>
                                @empty
                                    <em>-</em>
                                @endforelse
                            </td>
                            <td><span class="badge bg-warning text-dark">Belum Selesai SAP</span></td>
                            <td>
                                <button type="button" 
                                    class="btn btn-success btn-sm btnSelesaiSAP"
                                    data-id="{{ $item->id }}"
                                    data-nomor_kr="{{ $item->nomor_kr }}"
                                    data-pabrikan="{{ $item->pabrikan }}"
                                    data-nomor_po="{{ $item->nomor_po }}"
                                    data-nomor_doc="{{ $item->nomor_doc }}"
                                    data-jenis="{{ $item->jenis }}"
                                    data-tugas_4="{{ $item->tugas_4 }}"
                                    data-tanggal_masuk="{{ $item->tanggal_masuk }}"
                                    data-tanggal_keluar="{{ $item->tanggal_keluar }}"
                                    data-status="Belum Selesai SAP"
                                    data-details='@json($item->details->map(fn($d) => [
                                        "description" => $d->material->material_description,
                                        "qty" => $d->quantity,
                                        "satuan" => $d->satuan
                                    ]))'>
                                    <i class="fa fa-check"></i> Selesaikan
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- ✅ SUDAH SELESAI SAP --}}
        <h6 class="mt-4 fw-bold">Selesai SAP</h6>
        @if($materialSelesai->isEmpty())
            <p class="text-muted fst-italic">Belum ada data material yang selesai SAP.</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nomor KR</th>
                        <th>Pabrikan</th>
                        <th>Nomor PO</th>
                        <th>Nomor DOC</th>
                        <th>Jenis</th>
                        <th>Tug 4</th>
                        <!-- <th>Tanggal Masuk</th>
                        <th>Tanggal Keluar</th> -->
                        <th>Material</th>
                        <th>Status SAP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materialSelesai as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nomor_kr ?? '-' }}</td>
                            <td>{{ $item->pabrikan ?? '-' }}</td>
                            <td>{{ $item->nomor_po ?? '-' }}</td>
                            <td>{{ $item->nomor_doc ?? '-' }}</td>
                            <td>{{ $item->jenis ?? '-' }}</td>
                            <td>{{ $item->tugas_4 ?? '-' }}</td>
                            <!-- <td>{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->tanggal_keluar ? \Carbon\Carbon::parse($item->tanggal_keluar)->format('d/m/Y') : '-' }}</td> -->
                            <td>
                                @forelse ($item->details as $detail)
                                    {{ $detail->material->material_description ?? '-' }} ({{ $detail->quantity }} {{ $detail->satuan }})<br>
                                @empty
                                    <em>-</em>
                                @endforelse
                            </td>
                            <td><span class="badge bg-success">Selesai SAP</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- 🔹 MODAL KONFIRMASI --}}
<div class="modal fade" id="modalSelesaiSAP" tabindex="-1" aria-labelledby="modalSelesaiSAPLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">✔ Konfirmasi Selesai SAP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr><th>Nomor KR</th><td id="sapNomorKR">-</td></tr>
                    <tr><th>Pabrikan</th><td id="sapPabrikan">-</td></tr>
                    <tr><th>Nomor PO</th><td id="sapNomorPO">-</td></tr>
                    <tr><th>Nomor DOC</th><td id="sapNomorDOC">-</td></tr>
                    <tr><th>Jenis</th><td id="sapJenis">-</td></tr>
                    <tr><th>Tug 4</th><td id="sapTug4">-</td></tr>
                    <tr><th>Tanggal Masuk</th><td id="sapTanggalMasuk">-</td></tr>
                    <tr><th>Tanggal Keluar</th><td id="sapTanggalKeluar">-</td></tr>
                    <tr><th>Status</th><td id="sapStatus">-</td></tr>
                </table>
                <hr>
                <h6 class="fw-bold">📦 Detail Material</h6>
                <table class="table table-sm table-striped" id="sapDetailTable">
                    <thead>
                        <tr><th>No</th><th>Deskripsi Material</th><th>Qty</th><th>Satuan</th></tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnBatalModal" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnKonfirmasiSAP" class="btn btn-success">Tandai Selesai SAP</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    let selectedId = null;

    // 🔍 Filter Pencarian
    $('#searchInput').on('keyup', function () {
        const value = $(this).val().toLowerCase();
        $("#tableBelumSAP tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // 🟢 Klik tombol Selesai SAP
    $('.btnSelesaiSAP').on('click', function () {
        selectedId = $(this).data('id');
        $('#sapNomorKR').text($(this).data('nomor_kr'));
        $('#sapPabrikan').text($(this).data('pabrikan'));
        $('#sapNomorPO').text($(this).data('nomor_po'));
        $('#sapNomorDOC').text($(this).data('nomor_doc'));
        $('#sapJenis').text($(this).data('jenis'));
        $('#sapTug4').text($(this).data('tugas_4'));
        $('#sapTanggalMasuk').text($(this).data('tanggal_masuk'));
        $('#sapTanggalKeluar').text($(this).data('tanggal_keluar'));
        $('#sapStatus').text($(this).data('status'));

        const details = $(this).data('details');
        const tbody = $('#sapDetailTable tbody').empty();

        if (details && details.length > 0) {
            details.forEach((d, i) => {
                tbody.append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td>${d.description ?? '-'}</td>
                        <td>${d.qty ?? '-'}</td>
                        <td>${d.satuan ?? '-'}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="4" class="text-center text-muted">Tidak ada detail material</td></tr>');
        }

        $('#modalSelesaiSAP').modal('show');
    });

    // 🔴 Tombol Batal → Tutup modal manual jika Bootstrap tidak aktif
    $('#btnBatalModal').on('click', function () {
        $('#modalSelesaiSAP').modal('hide');
    });

    // 🟢 Tombol Konfirmasi
    $('#btnKonfirmasiSAP').on('click', function () {
        if (!selectedId) return;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan ditandai sebagai 'Selesai SAP'.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesai',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("material-masuk.selesai-sap", ":id") }}'.replace(':id', selectedId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT'
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Status berhasil diperbarui.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#modalSelesaiSAP').modal('hide');
                        setTimeout(() => location.reload(), 1600);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui status.',
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
