@extends('layouts.app')

@section('title', 'Berita Acara')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Berita Acara</h2>

    <a href="{{ route('berita-acara.create') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> Buat Berita Acara
    </a>
</div>

<div class="card">
    <div class="card-body">

        <table class="table table-bordered table-striped" id="dataTable">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th>Mengetahui</th>
                    <th>Pembuat</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($beritaAcaras as $ba)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ba->hari }}</td>
                    <td>{{ \Carbon\Carbon::parse($ba->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $ba->mengetahui }}</td>
                    <td>{{ $ba->pembuat }}</td>

                    <td class="text-center">

    {{-- Lihat Surat --}}
    <a href="{{ route('berita-acara.show', $ba->id) }}" 
       class="btn btn-info btn-sm" title="Lihat Surat">
        <i class="fa fa-eye"></i>
    </a>

    {{-- Edit --}}
    <a href="{{ route('berita-acara.edit', $ba->id) }}" 
       class="btn btn-warning btn-sm" title="Edit">
        <i class="fa fa-pencil"></i>
    </a>

    {{-- Delete --}}
    <form action="{{ route('berita-acara.destroy', $ba->id) }}"
          method="POST"
          style="display:inline-block;">
          @csrf
          @method('DELETE')

        <button class="btn btn-danger btn-sm"
            onclick="return confirm('Hapus Berita Acara ini?')"
            title="Hapus">
            <i class="fa fa-trash"></i>
        </button>
    </form>

    {{-- PDF (paling ujung - biru) --}}
    <a href="{{ route('berita-acara.pdf', $ba->id) }}" 
       class="btn btn-primary btn-sm"
       title="Print PDF"
       target="_blank">
        <i class="fa fa-file-pdf-o"></i>
    </a>

</td>

                </tr>

                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Belum ada Berita Acara
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>

    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#dataTable').DataTable();
    });
</script>
@endpush
