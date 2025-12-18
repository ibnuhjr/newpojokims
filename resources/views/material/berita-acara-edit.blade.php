@extends('layouts.app')

@section('title', 'Edit Berita Acara')

@section('content')

<div class="card">
    <div class="card-header bg-warning text-white">
        <strong>Edit Berita Acara</strong>
    </div>

    <div class="card-body">

        <form action="{{ route('berita-acara.update', $ba->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Hari</label>
                    <input type="text" name="hari" class="form-control" value="{{ $ba->hari }}">
                </div>

                <div class="col-md-8">
                    <label>Tanggal (teks)</label>
                    <input type="text" name="tanggal_teks" class="form-control"
                        value="{{ $ba->tanggal_teks }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $ba->tanggal }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nama Mengetahui</label>
                    <input type="text" name="mengetahui" class="form-control" value="{{ $ba->mengetahui }}">
                </div>

                <div class="col-md-6">
                    <label>Jabatan Mengetahui</label>
                    <input type="text" name="jabatan_mengetahui" class="form-control"
                        value="{{ $ba->jabatan_mengetahui }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nama Pembuat</label>
                    <input type="text" name="pembuat" class="form-control" value="{{ $ba->pembuat }}">
                </div>

                <div class="col-md-6">
                    <label>Jabatan Pembuat</label>
                    <input type="text" name="jabatan_pembuat" class="form-control"
                        value="{{ $ba->jabatan_pembuat }}">
                </div>
            </div>

            <button class="btn btn-warning float-end">Update</button>

        </form>

    </div>
</div>

@endsection
