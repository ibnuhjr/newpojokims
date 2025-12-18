@extends('layouts.app')

@section('title', 'Edit Material Masuk')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Material Masuk</h3>
                    <a href="{{ route('material-masuk.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('material-masuk.update', $materialMasuk->id) }}" method="POST" id="materialMasukForm">
                        @csrf
                        @method('PUT')

                        {{-- ======== Bagian Identitas Utama ======== --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nomor_kr">Nomor KR</label>
                                <input type="text" class="form-control" id="nomor_kr" name="nomor_kr"
                                    value="{{ old('nomor_kr', $materialMasuk->nomor_kr) }}" placeholder="Masukkan Nomor KR">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pabrikan">Pabrikan</label>
                                <input type="text" class="form-control" id="pabrikan" name="pabrikan"
                                    value="{{ old('pabrikan', $materialMasuk->pabrikan) }}" placeholder="Masukkan Pabrikan">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_masuk">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" 
                                    value="{{ old('tanggal_masuk', \Carbon\Carbon::parse($materialMasuk->tanggal_masuk)->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tanggal_keluar">Tanggal Keluar</label>
                                <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar"
                                    value="{{ old('tanggal_keluar', $materialMasuk->tanggal_keluar ? \Carbon\Carbon::parse($materialMasuk->tanggal_keluar)->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="jenis">Jenis</label>
                                <select name="jenis" id="jenis" class="form-control">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="B1" {{ old('jenis', $materialMasuk->jenis) == 'B1' ? 'selected' : '' }}>B1</option>
                                    <option value="B2" {{ old('jenis', $materialMasuk->jenis) == 'B2' ? 'selected' : '' }}>B2</option>
                                    <option value="A0" {{ old('jenis', $materialMasuk->jenis) == 'A0' ? 'selected' : '' }}>A0</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nomor_po">Nomor PO</label>
                                <input type="text" class="form-control" id="nomor_po" name="nomor_po"
                                    value="{{ old('nomor_po', $materialMasuk->nomor_po) }}" placeholder="Masukkan Nomor PO">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nomor_doc">Nomor DOC</label>
                                <input type="text" class="form-control" id="nomor_doc" name="nomor_doc"
                                    value="{{ old('nomor_doc', $materialMasuk->nomor_doc) }}" placeholder="Masukkan Nomor DOC">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tugas_4">Tug 4</label>
                                <input type="text" class="form-control" id="tugas_4" name="tugas_4"
                                    value="{{ old('tugas_4', $materialMasuk->tugas_4) }}" placeholder="Masukkan Tug 4">
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                    placeholder="Masukkan keterangan (opsional)">{{ old('keterangan', $materialMasuk->keterangan) }}</textarea>
                        </div>

                        {{-- ======== Detail Material ======== --}}
                        <hr>
                        <h5>Detail Material</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="materialTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Material</th>
                                        <th>Normalisasi</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="materialTableBody">
                                    @foreach($materialMasuk->details as $index => $detail)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>

                                        {{-- Input Material dengan autocomplete --}}
                                        <td>
                                            <input type="hidden" name="materials[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                                            <input type="hidden" name="materials[{{ $index }}][material_id]" value="{{ $detail->material_id }}">
                                            <input type="text" 
                                                   name="materials[{{ $index }}][material_name]" 
                                                   value="{{ $detail->material->material_description ?? '' }}" 
                                                   class="form-control form-control-sm material-input" 
                                                   placeholder="Ketik nama material..." 
                                                   autocomplete="off">
                                        </td>

                                        {{-- Normalisasi otomatis isi material_code, tapi bisa diketik manual --}}
                                        <td>
                                            <input type="text" name="materials[{{ $index }}][normalisasi]" 
                                                value="{{ $detail->normalisasi ?? $detail->material->material_code ?? '' }}" 
                                                class="form-control form-control-sm">
                                        </td>

                                        {{-- Qty --}}
                                        <td>
                                            <input type="number" name="materials[{{ $index }}][quantity]" 
                                                value="{{ $detail->quantity }}" min="1" 
                                                class="form-control form-control-sm" required>
                                        </td>

                                        {{-- Satuan --}}
                                        <td>
                                            <input type="text" name="materials[{{ $index }}][satuan]" 
                                                value="{{ $detail->satuan }}" 
                                                class="form-control form-control-sm" required>
                                        </td>

                                        {{-- Tombol hapus baris --}}
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeRow(this)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ======== Tombol Aksi ======== --}}
                        <div class="form-group mt-4">
                        {{-- Tombol Update Data biasa --}}
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Data
                        </button>

                        {{-- Tombol Update & Selesai SAP --}}
                        <button type="submit" 
                                formaction="{{ route('material-masuk.updateDanSelesaiSAP', $materialMasuk->id) }}" 
                                class="btn btn-success">
                            <i class="fa fa-check"></i> Update & Selesai SAP
                        </button>

                        {{-- Tombol Batal --}}
                        <a href="{{ route('material-masuk.index') }}" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- jQuery UI Autocomplete --}}
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Autocomplete untuk kolom Material
    $(document).on('focus', '.material-input', function() {
        const input = $(this);
        const row = input.closest('tr');

        input.autocomplete({
            appendTo: 'body', // supaya dropdown nggak ketutup tabel
            minLength: 2,
            delay: 200,
            source: function(request, response) {
                $.ajax({
                    url: '/material/autocomplete',
                    dataType: 'json',
                    data: { term: request.term },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.material_code + ' - ' + item.material_description,
                                value: item.material_description,
                                id: item.id,
                                kode: item.material_code,
                                satuan: item.satuan
                            };
                        }));
                    },
                    error: function(xhr) {
                        console.error('Autocomplete error:', xhr.responseText);
                    }
                });
            },
            select: function(event, ui) {
                row.find('input[name*="[material_id]"]').val(ui.item.id);
                row.find('input[name*="[normalisasi]"]').val(ui.item.kode);
                row.find('input[name*="[satuan]"]').val(ui.item.satuan);
            }
        });
    });

    // Kalau user ngetik manual, kosongkan material_id
    $(document).on('input', '.material-input', function() {
        const row = $(this).closest('tr');
        row.find('input[name*="[material_id]"]').val('');
    });
});
</script>

@endpush
@endsection
