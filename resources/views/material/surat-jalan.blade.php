@extends('layouts.app')

@section('title', 'Surat Jalan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Daftar Surat Jalan</h3>
                    <div class="card-tools">
                        <a href="{{ route('surat-jalan.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Buat Surat Jalan
                        </a>
                        <!-- @if(auth()->user()->isAdmin())
                        <a href="{{ route('surat-jalan.approval') }}" class="btn btn-success">
                            <i class="fa fa-check"></i> Approval
                        </a>
                        @endif -->
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filterStatus">Filter Status</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">— Semua Status —</option>
                                <option value="BUTUH_PERSETUJUAN">Butuh Persetujuan</option>
                                <option value="APPROVED">Approved</option>
                                <option value="SELESAI">Selesai</option>
                            </select>
                        </div>
                    </div>  
                    <div class="table-responsive">
                        <table id="suratJalanTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Surat</th>
                                    <th>Tanggal</th>
                                    <th>Diberikan Kepada</th>
                                    <th>Berdasarkan</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Surat Jalan -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="detailModalLabel">Detail Surat Jalan</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" id="detailModalBody"></div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#suratJalanTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("surat-jalan.getData") }}',
            data: function (d) {
                d.status = $('#filterStatus').val(); // ⬅️ filter status masuk di sini
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nomor_surat', name: 'nomor_surat' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'kepada', name: 'kepada' },
            { data: 'berdasarkan', name: 'berdasarkan' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        responsive: true
    });

    // ⬇️ Tambahkan ini
    $('#filterStatus').on('change', function () {
        table.ajax.reload();
    });
});

// Popup detail surat jalan
function showDetailSuratJalan(id) {
    $.ajax({
        url: '/surat-jalan/' + id + '/modal-detail',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#detailModalBody').html(response.html);
                $('#detailModal').modal('show');
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Terjadi kesalahan saat memuat detail surat jalan.', 'error');
        }
    });
}


function deleteSuratJalan(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data surat jalan akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/surat-jalan/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#suratJalanTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        }
    });
}

function printSuratJalan(id) {
    window.open('{{ route("surat-jalan.export", ":id") }}'.replace(':id', id), '_blank');
}
</script>
@endpush
