<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kartu Gantung Barang</title>

<style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: #f8b4c9; /* Pink lembut seragam */
    }

    .page {
        width: 210mm;
        height: 297mm;
        background: #f8b4c9; /* Konsisten */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .card {
        width: 185mm;
        height: 270mm;
        background: #ffb3c9; /* Warna pink seragam */
        box-sizing: border-box;
        padding: 10mm;
        position: relative;
        overflow: hidden;
        border-radius: 4mm;
    }

    /* HEADER KIRI */
    .header-left {
        font-weight: bold;
        font-size: 13px;
        line-height: 17px;
        position: absolute;
        top: 10mm;
        left: 12mm;
    }

    /* HEADER KANAN */
    .header-right {
        position: absolute;
        top: 10mm;
        right: 12mm;
        text-align: right;
        font-size: 13px;
        font-weight: bold;
    }

    .lokasi-box {
        margin-top: 3px;
        width: 22mm;
        height: 10mm;
        border: 1px solid #000;
    }

    /* NORMALISASI — diperbaiki */
    .normalisasi-box {
        position: absolute;
        top: 16mm;
        left: 50%;
        transform: translateX(-50%) rotate(-4deg); /* lebih rapi */
        width: 45mm;
        height: 18mm;
        border: 2px solid #000;
        background: rgba(255,255,255,0.55);
        text-align: center;
        padding-top: 2mm;
        font-size: 12px;
        font-weight: bold;
    }

    .title {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        text-decoration: underline;
        margin-top: 40mm;
    }

    .info-row {
        margin-top: 6mm;
        font-size: 14px;
        font-weight: bold;
        line-height: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    table th, table td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
    }

    table th {
        background: rgba(255,255,255,0.3);
        font-weight: bold;
    }

    .table-wrapper {
        margin-top: 5mm;
        height: 125mm;
        overflow: hidden;
    }

    .footer {
        position: absolute;
        bottom: 8mm;
        right: 12mm;
        font-size: 13px;
        font-weight: bold;
    }
</style>
</head>
<body>

<div class="page">

<div class="card">

    <!-- HEADER KIRI -->
    <div class="header-left">
        PT PLN (PERSERO)<br>
        UID JAWA BARAT<br>
        UP3 CIMAHI
    </div>

    <!-- HEADER KANAN -->
    <div class="header-right">
        TUG.2
        <div class="lokasi-box"></div>
    </div>

    <!-- NORMALISASI -->
    <div class="normalisasi-box">
        {{ $material->material_code ?? '-' }}<br>
        <span style="font-size:11px;">Nomor Normalisasi</span>
    </div>

    <!-- JUDUL -->
    <div class="title">KARTU GANTUNG BARANG</div>

    <!-- INFO MATERIAL -->
    <div class="info-row">
        Nama Barang: {{ $material->material_description ?? '-' }}<br>
        Satuan: {{ $material->base_unit_of_measure ?? '-' }}
    </div>

    <!-- TABEL -->
    <div class="table-wrapper">
        <table>
            <tr>
                <th>Tgl</th>
                <th>No. Slip</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Rak</th>
                <th>Peti</th>
                <th>Jumlah</th>
                <th>Catatan</th>
            </tr>

            @forelse($histories as $h)
            <tr>
                <td>{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $h->no_slip ?? '-' }}</td>
                <td>{{ $h->masuk ?? '' }}</td>
                <td>{{ $h->keluar ?? '' }}</td>
                <td>{{ $h->rak ?? '' }}</td>
                <td>{{ $h->peti ?? '' }}</td>
                <td>{{ $h->sisa_persediaan ?? 0 }}</td>
                <td style="text-align:left;">{{ $h->catatan ?? '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;">Tidak ada histori material.</td>
            </tr>
            @endforelse
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">A5 &nbsp;&nbsp; TUG.2</div>

</div>

</div>

</body>
</html>
