@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    a.disabled {
        pointer-events: none;
        opacity: 0.5;
    }
</style>
@endpush

@section('content')
@php
    $materialId = $material->id ?? null;
@endphp
<div class="container-fluid px-lg-5 px-3 py-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body py-4 px-4">

            {{-- 🔝 HEADER & EXPORT --}}
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ $materialId 
                            ? route('material.history.export', ['id' => $materialId]) 
                            : route('material.history.export') }}"
                   class="btn btn-success shadow-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3">
                    <i class="fa fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>

                <a href="#"
                   id="exportPdfBtn"
                   class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-3 {{ $materialId ? '' : 'disabled' }}"
                   {{ $materialId ? 'href=' . route('material.history.export-pdf', ['id' => $materialId]) : 'onclick=return false;' }}>
                    <i class="fa fa-file-pdf"></i>
                    <span>Export PDF</span>
                </a>
            </div>

            {{-- 🔍 AUTOCOMPLETE MATERIAL --}}
            <form id="filterForm" class="mb-4">
                <div class="row g-3 align-items-end justify-content-between">
                    <div class="col-lg-8 col-md-7">
                        <label for="searchMaterial" class="form-label text-muted small mb-1">Cari Material</label>
                        <input type="text" id="searchMaterial"
                               class="form-control shadow-sm rounded-3 form-control-sm"
                               placeholder="Ketik nama material...">
                    </div>

                    <input type="hidden" id="materialId">

                    <div class="col-lg-3 col-md-5 text-md-end">
                        <label for="tipe" class="form-label text-muted small mb-1 d-block">Jenis Transaksi</label>
                        <select name="tipe" id="tipe" class="form-select shadow-sm rounded-3 form-select-sm">
                            <option value="">Semua Tipe</option>
                            <option value="MASUK">MASUK</option>
                            <option value="KELUAR">KELUAR</option>
                        </select>
                    </div>
                </div>
            </form>

            {{-- 📋 TABLE --}}
            <div class="table-responsive">
                <table class="table align-middle table-bordered shadow-sm mb-0 rounded-3 overflow-hidden">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>No Slip</th>
                            <th>Material</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Sisa Persediaan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>

                    <tbody id="dataTableBody">
                        @forelse ($histories as $h)
                            <tr class="text-center">
                                <td>{{ $h->tanggal ? \Carbon\Carbon::parse($h->tanggal)->format('Y-m-d') : '-' }}</td>

                                <td>
                                    <span class="badge 
                                        @if($h->tipe === 'MASUK') bg-success 
                                        @elseif($h->tipe === 'KELUAR') bg-danger 
                                        @else bg-secondary @endif px-3 py-2">
                                        {{ strtoupper($h->tipe) }}
                                    </span>
                                </td>

                                <td>{{ $h->no_slip ?? '-' }}</td>
                                <td class="text-start">{{ optional($h->material)->material_description ?? '-' }}</td>
                                <td class="text-success fw-semibold">{{ number_format($h->masuk ?? 0) }}</td>
                                <td class="text-danger fw-semibold">{{ number_format($h->keluar ?? 0) }}</td>
                                <td class="fw-semibold text-primary">{{ number_format($h->sisa_persediaan ?? 0) }}</td>
                                <td class="text-start text-muted">{{ $h->catatan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    ❌ Tidak ada data histori
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(function() {
    // 🔍 AUTOCOMPLETE MATERIAL
    $("#searchMaterial").autocomplete({
        minLength: 1,
        source: function(request, response) {
            $.get("{{ route('material.autocomplete') }}", { q: request.term }, function(data) {
                response(data);
            });
        },
        select: function(event, ui) {
            $("#materialId").val(ui.item.id);
            $("#searchMaterial").val(ui.item.value);

            // Enable Export PDF
            const exportPdfBtn = document.getElementById('exportPdfBtn');
            exportPdfBtn.classList.remove('disabled');
            exportPdfBtn.setAttribute('href', `/materials/${ui.item.id}/history/export-pdf`);
            exportPdfBtn.onclick = null;

            setTimeout(() => filterTable(), 10);
            return false;
        }
    });

    const tipeSelect = document.getElementById('tipe');

    function filterTable() {
        const keyword = $("#searchMaterial").val().toLowerCase();
        const tipe = tipeSelect.value.toLowerCase();

        $("#dataTableBody tr").each(function() {
            let material = $(this).find("td:nth-child(4)").text().toLowerCase();
            let tipeText = $(this).find("td:nth-child(2)").text().toLowerCase();

            let matchMaterial = !keyword || material.includes(keyword);
            let matchTipe = !tipe || tipeText.includes(tipe);

            $(this).toggle(matchMaterial && matchTipe);
        });
    }

    tipeSelect.addEventListener('change', filterTable);
    $("#searchMaterial").on('keyup', filterTable);
});
</script>
@endpush

@endsection
