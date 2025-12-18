@extends('layouts.app')

@section('title', 'Pemeriksaan Fisik')

@section('content')

<div class="card p-4">

    <h3 class="mb-3">Pilih Bulan Pemeriksaan Fisik</h3>

    <form action="{{ route('material.pemeriksaanFisik') }}" method="GET" class="mb-4">
        <label class="form-label">Pilih Bulan</label>
        <input type="month" name="bulan" value="{{ $bulan }}" 
               class="form-control" style="max-width: 300px" required>

        <button class="btn btn-primary mt-3">Lanjut</button>
    </form>

    @if($materials)
    <hr>

    <h4 class="mt-4">
        Pemeriksaan Fisik Material  
        <span class="text-muted">({{ $bulan }})</span>
    </h4>

    <div class="table-responsive mt-3">
        <table class="table table-bordered table-sm" style="font-size: 12px;">
            <thead class="text-center" style="background: #f2f2f2; font-weight: bold;">
                <tr>
                    <th rowspan="2">NO</th>
                    <th rowspan="2">No Normalisasi / Part</th>
                    <th rowspan="2">Nama Barang / Sparepart</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Valuation Type</th>
                    <th colspan="3">SALDO / JUMLAH</th>
                    <th colspan="3">PERBEDAAN / SELISIH</th>
                    <th colspan="3">JUSTIFIKASI SELISIH</th>
                </tr>
                <tr>
                    <th>SAP</th>
                    <th>FISIK</th>
                    <th>SN MIMS</th>
                    <th>SAP - FISIK</th>
                    <th>SAP - SN MIMS</th>
                    <th>FISIK - SN MIMS</th>
                    <th>SAP - FISIK</th>
                    <th>SAP - SN MIMS</th>
                    <th>FISIK - SN MIMS</th>
                </tr>
            </thead>

            <tbody>
                @foreach($materials as $i => $m)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $m->material_code }}</td>
                    <td>{{ $m->material_description }}</td>
                    <td class="text-center">{{ $m->base_unit_of_measure }}</td>
                    <td class="text-center">{{ $m->valuation_type }}</td>

                    {{-- SALDO --}}
                    <td>
                        <input type="number" id="sap_{{ $i }}" 
                               class="form-control form-control-sm" 
                               oninput="hitung({{ $i }})">
                    </td>

                    <td class="text-center">
                        {{ $m->stock_realtime }}
                        <input type="hidden" id="fisik_val_{{ $i }}" value="{{ $m->stock_realtime }}">
                    </td>

                    <td>
                        <input type="number" id="sn_{{ $i }}" 
                               class="form-control form-control-sm" 
                               oninput="hitung({{ $i }})">
                    </td>

                    {{-- PERBEDAAN --}}
                    <td><input type="text" id="sf_{{ $i }}" class="form-control form-control-sm" readonly></td>
                    <td><input type="text" id="ss_{{ $i }}" class="form-control form-control-sm" readonly></td>
                    <td><input type="text" id="fs_{{ $i }}" class="form-control form-control-sm" readonly></td>

                    {{-- JUSTIFIKASI --}}
                    <td><input type="text" class="form-control form-control-sm"></td>
                    <td><input type="text" class="form-control form-control-sm"></td>
                    <td><input type="text" class="form-control form-control-sm"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

{{-- SCRIPT PENGHITUNG SELISIH --}}
<script>
function hitung(i) {
    const sap   = parseFloat(document.getElementById(`sap_${i}`).value)      || 0;
    const fisik = parseFloat(document.getElementById(`fisik_val_${i}`).value) || 0;
    const sn    = parseFloat(document.getElementById(`sn_${i}`).value)       || 0;

    document.getElementById(`sf_${i}`).value = sap - fisik;
    document.getElementById(`ss_${i}`).value = sap - sn;
    document.getElementById(`fs_${i}`).value = fisik - sn;
}
</script>

@endsection
