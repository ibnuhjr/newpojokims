<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara PDF</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 28px 60px;
            line-height: 1.45; 
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .title { font-size: 16pt; font-weight: bold; margin-top: 25px; }
        .subtitle { font-size: 12pt; margin-top: 1px; }

        .paragraph {
            text-align: justify;
            margin-top: 20px;
        }

        .ttd-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }

        .ttd-table td {
            width: 50%;
            text-align: center;
            padding-bottom: 70px; /* ruang tanda tangan */
            vertical-align: top;
        }

        .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: -40px; /* naikkan nama */
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="bold">PT PLN (Persero)</div>
<div>UNIT INDUK DISTRIBUSI JAWA BARAT</div>

{{-- TITLE --}}
<div class="center">
    <div class="title">BERITA ACARA REKONSILIASI MATERIAL GUDANG</div>
    <div class="subtitle">PT PLN (Persero) UP3 CIMAHI</div>
</div>

{{-- DATE --}}
@php
$tgl = \Carbon\Carbon::parse($ba->tanggal);
$tanggalAngka = $tgl->format('d - m - Y');
$tanggalSurat = $tgl->translatedFormat('d F Y');
@endphp

{{-- PARAGRAPH 1 --}}
<p class="paragraph">
    Pada hari ini <strong>{{ $ba->hari }}</strong> Tanggal
    <strong>{{ $ba->tanggal_teks }}</strong> ({{ $tanggalAngka }}),
    Kami yang bertanda tangan di bawah ini telah melakukan <i>Stock Count</i>
    di Gudang UP3 & ULP dengan rincian sebagaimana terlampir dalam Blangko TUG 15
    dan Daftar Pemeriksaan Fisik serta Saldo <i>Serial Number</i> pada aplikasi MIMS
    (<i>Material Identification Management System</i>).
</p>

{{-- PARAGRAPH 2 --}}
<p class="paragraph">
    Dengan ini menyatakan bahwa, data tersebut diambil dari saldo <i>cut off</i>
    pada tanggal 1 (satu) bulan berjalan, telah diperiksa dan diverifikasi
    dengan benar dan dapat dipertanggungjawabkan.
</p>

{{-- DATE RIGHT --}}
<p style="text-align: right; margin-top: 35px;">
    Cimahi, {{ $tanggalSurat }}
</p>

{{-- SIGNATURE TABLE --}}
<table class="ttd-table">
    <tr>
        <td>
            Mengetahui,<br>
            {{ $ba->jabatan_mengetahui }}
        </td>
        <td>
            Dibuat Oleh,<br>
            {{ $ba->jabatan_pembuat }}
        </td>
    </tr>

    <tr>
        <td class="name">({{ $ba->mengetahui }})</td>
        <td class="name">({{ $ba->pembuat }})</td>
    </tr>
</table>

</body>
</html>
