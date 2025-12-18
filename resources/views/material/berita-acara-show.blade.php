@extends('layouts.app')

@section('title', 'Berita Acara')

@section('content')

<style>
    .ba-container {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        font-family: "Times New Roman", serif;
        line-height: 1.6;
    }
    .ba-title {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .ba-subtitle {
        text-align: center;
        font-size: 16px;
        margin-bottom: 25px;
    }
    .ba-section {
        margin-bottom: 25px;
        text-align: justify;
    }
    .ba-sign {
        margin-top: 40px;
    }
    .ba-sign td {
        vertical-align: top;
        padding: 10px;
        text-align: center;
    }
    .ba-name {
        margin-top: 60px;
        font-weight: bold;
        text-decoration: underline;
    }
</style>

@php
    // Generate format tanggal otomatis
    $tanggalObj = \Carbon\Carbon::parse($ba->tanggal);
    $tanggalAngka = $tanggalObj->format('d - m - Y');
    $tanggalSurat = $tanggalObj->translatedFormat('d F Y');
@endphp

<div class="ba-container">

    {{-- IDENTITAS PLN --}}
    <div style="text-align:center; margin-bottom:20px;">
        <div style="font-weight:bold;">PT PLN (Persero)</div>
        <div>UNIT INDUK DISTRIBUSI JAWA BARAT</div>
    </div>

    {{-- JUDUL --}}
    <div class="ba-title">
        BERITA ACARA REKONSILIASI MATERIAL GUDANG
    </div>
    <div class="ba-subtitle">
        PT PLN (Persero) UP3 CIMAHI
    </div>

    {{-- PARAGRAF 1 --}}
    <p class="ba-section">
        Pada hari ini <strong>{{ $ba->hari }}</strong> Tanggal 
        <strong>{{ $ba->tanggal_teks }}</strong> ({{ $tanggalAngka }}), 
        Kami yang bertanda tangan di bawah ini telah melakukan <em>Stock Count</em> di Gudang UP3 & ULP 
        dengan rincian sebagaimana terlampir dalam Blangko TUG 15 dan Daftar Pemeriksaan Fisik 
        serta Saldo <em>Serial Number</em> pada aplikasi MIMS (Material Identification Management System).
    </p>

    {{-- PARAGRAF 2 --}}
    <p class="ba-section">
        Dengan ini menyatakan bahwa data tersebut diambil dari saldo 
        <em>cut off</em> pada tanggal 1 (satu) bulan berjalan, telah diperiksa 
        dan diverifikasi dengan benar dan dapat dipertanggungjawabkan.
    </p>

    {{-- TANDA TANGAN --}}
    <table width="100%" class="ba-sign">
        <tr>
            <td>
                Mengetahui,<br>
                {{ $ba->jabatan_mengetahui }}
            </td>
            <td>
                Cimahi, {{ $tanggalSurat }}<br>
                Dibuat Oleh,<br>
                {{ $ba->jabatan_pembuat }}
            </td>
        </tr>

        {{-- Ruang tanda tangan --}}
        <tr>
            <td style="height:80px;"></td>
            <td></td>
        </tr>

        {{-- Nama pejabat --}}
        <tr>
            <td class="ba-name">({{ $ba->mengetahui }})</td>
            <td class="ba-name">({{ $ba->pembuat }})</td>
        </tr>
    </table>

</div>

@endsection
