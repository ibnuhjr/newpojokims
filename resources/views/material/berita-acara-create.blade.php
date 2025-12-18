@extends('layouts.app')

@section('title', 'Buat Berita Acara')

@section('content')

<h2 class="fw-bold mb-3">Buat Berita Acara</h2>

<a href="{{ route('berita-acara.index') }}" class="text-primary">
    <i class="fa fa-arrow-left"></i> Kembali
</a>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0 fw-semibold">Form Berita Acara</h5>
    </div>

    <div class="card-body p-4">

        <form action="{{ route('berita-acara.store') }}" method="POST">
            @csrf

            {{-- ================== BARIS 1: Hari & Tanggal Teks ================== --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Hari</label>
                    <input type="text" name="hari" class="form-control" placeholder="Selasa" required>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Tanggal (Teks)</label>
                    <input type="text" name="tanggal_teks" class="form-control"
                        placeholder="Empat Bulan November Tahun Dua Ribu Dua Puluh Lima" required>
                </div>
            </div>

            {{-- ================== BARIS 2: Tanggal Angka / Tanggal Surat ================== --}}
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                    <small class="text-muted">Akan dipakai untuk (04 - 11 - 2025) & Cimahi, 04 November 2025</small>
                </div>
            </div>

            {{-- ================== BARIS 3: MENGETAHUI ================== --}}
            <h5 class="fw-semibold mt-4 mb-2">Pejabat Mengetahui</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Mengetahui</label>
                    <input type="text" name="mengetahui" class="form-control" placeholder="ARYTA WULANDARI" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jabatan Mengetahui</label>
                    <input type="text" name="jabatan_mengetahui" class="form-control"
                        placeholder="Manager UP3 Cimahi" required>
                </div>
            </div>

            {{-- ================== BARIS 4: PEMBUAT ================== --}}
            <h5 class="fw-semibold mt-4 mb-2">Pejabat Pembuat</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Pembuat</label>
                    <input type="text" name="pembuat" class="form-control" placeholder="DENI PURNAMA" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jabatan Pembuat</label>
                    <input type="text" name="jabatan_pembuat" class="form-control"
                        placeholder="Asman Konstruksi UP3 Cimahi" required>
                </div>
            </div>

            {{-- ================== BUTTON ================== --}}
            <div class="text-end">
                <button class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="fa fa-save"></i> Simpan Berita Acara
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
