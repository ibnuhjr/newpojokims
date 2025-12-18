@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h4 class="fw-semibold mb-3">
                <i class="fa fa-undo me-2"></i>Form Pengembalian Barang
            </h4>

            <!-- Informasi Surat Jalan Keluar -->
            <div class="mb-4">
                <table class="table table-bordered table-sm">
                    <tr><th>Nomor Surat Jalan Keluar</th><td>{{ $surat->nomor_surat }}</td></tr>
                    <tr><th>Jenis Surat Jalan</th><td>{{ ucfirst($surat->jenis_surat_jalan) }}</td></tr>
                    <tr><th>Tanggal Keluar</th><td>{{ $surat->tanggal->format('d/m/Y') }}</td></tr>
                    <tr><th>Diberikan Kepada</th><td>{{ $surat->kepada }}</td></tr>
                </table>
            </div>

            <!-- Form Pengembalian -->
            <form action="{{ route('surat-jalan.kembalikan', ['suratId' => $surat->id, 'detailId' => $detail->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Input Nomor dan Tanggal Surat Jalan Masuk -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nomor Surat Jalan Masuk</label>
                        <input type="text" name="nomor_surat_masuk" class="form-control rounded-3"
                               placeholder="Contoh: SJ-MSK/001/2025" required>
                        <small class="text-muted">Masukkan nomor dokumen surat jalan pengembalian barang.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control rounded-3"
                               value="{{ now()->toDateString() }}" required>
                    </div>
                </div>

                <h5 class="fw-semibold mb-3">Detail Material</h5>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:30%">Material</th>
                                <th style="width:10%">Keluar</th>
                                <th style="width:10%">Masuk</th>
                                <th style="width:10%">Sisa</th>
                                <th style="width:10%">Satuan</th>
                                <th style="width:25%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($surat->details as $i => $item)
                                @php
                                    $keluar = $item->quantity;
                                    $masuk = $item->jumlah_kembali ?? 0;
                                    $sisa = $keluar - $masuk;
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                               value="{{ $item->material->material_description ?? $item->nama_barang_manual }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center fw-semibold"
                                               value="{{ $keluar }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center text-success fw-semibold"
                                               value="{{ $masuk }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center text-danger fw-semibold"
                                               value="{{ $sisa }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-center"
                                               value="{{ $item->satuan ?? 'Unit' }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="keterangan" class="form-control form-control-sm" placeholder="Keterangan">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Riwayat Pengembalian (readonly display) -->
                @if($detail->histories && $detail->histories->count() > 0)
                    <div class="mb-4">
                        <h5 class="fw-semibold"><i class="fa fa-history me-2"></i>Riwayat Pengembalian Sebelumnya</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Surat Jalan Masuk</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Jumlah Kembali</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detail->histories as $i => $h)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $h->nomor_surat_masuk }}</td>
                                            <td>{{ \Carbon\Carbon::parse($h->tanggal_masuk)->format('d/m/Y') }}</td>
                                            <td class="fw-semibold text-success">{{ $h->jumlah_kembali }}</td>
                                            <td>{{ $h->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="mt-4 mb-3">
                    <label class="form-label fw-semibold">Jumlah yang Dikembalikan Sekarang</label>
                    <input type="number" name="jumlah_kembali" class="form-control"
                           min="1" max="{{ $detail->quantity - ($detail->jumlah_kembali ?? 0) }}"
                           value="{{ $detail->quantity - ($detail->jumlah_kembali ?? 0) }}" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary rounded-pill">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan</button>
                </div>
            </form>
            @if ($detail->pengembalianHistories->count() > 0)
<div class="mt-5">
    <h5 class="fw-semibold mb-3">
        <i class="fa fa-history me-2"></i>Riwayat Pengembalian Barang
    </h5>

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nomor Surat Masuk</th>
                    <th>Tanggal Masuk</th>
                    <th>Jumlah Kembali</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail->pengembalianHistories as $i => $history)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $history->nomor_surat_masuk }}</td>
                    <td>{{ \Carbon\Carbon::parse($history->tanggal_masuk)->format('d/m/Y') }}</td>
                    <td class="fw-semibold text-success">{{ $history->jumlah_kembali }}</td>
                    <td>{{ $history->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    table th, table td {
        vertical-align: middle;
        text-align: center;
    }
    .form-control-sm {
        font-size: 0.875rem;
        padding: 0.35rem 0.5rem;
    }
    .form-control[readonly] {
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }
    .text-success { color: #198754 !important; }
    .text-danger { color: #dc3545 !important; }
</style>
@endpush
