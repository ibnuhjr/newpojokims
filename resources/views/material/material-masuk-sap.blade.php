@extends('layouts.app')

@section('title', 'Material Masuk')
@section('page-title', 'Material Masuk')

@push('styles')
<style>
/* Styling clean dan responsif */
.table-responsive { background-color: #fff; padding: 15px; margin:10px 0; }
.table thead { background-color: #343a40; color:white; }
.table-striped tbody tr:nth-child(even) { background-color:#f8f9fa; }
.table-hover tbody tr:hover { background-color:#e9ecef !important; }
.badge { font-size:0.85rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa fa-box me-2"></i>Material Masuk</h5>
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="materialTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="belum-sap-tab" data-toggle="tab" href="#belum-sap" role="tab">
                                Belum Selesai SAP <span class="badge bg-warning ms-2" id="pending-count">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="selesai-sap-tab" data-toggle="tab" href="#selesai-sap" role="tab">
                                Sudah Selesai SAP <span class="badge bg-success ms-2" id="approved-count">0</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-3">
                        <!-- Belum Selesai SAP -->
                        <div class="tab-pane fade show active" id="belum-sap">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="pendingTable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Material</th>
                                            <th>Qty</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Status</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Sudah Selesai SAP -->
                        <div class="tab-pane fade" id="selesai-sap">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="approvedTable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Material</th>
                                            <th>Qty</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Status</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Tanggal Selesai SAP</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let pendingTable, approvedTable;

$(document).ready(function() {

    // Inisialisasi tabel Belum Selesai SAP
    pendingTable = $('#pendingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("material-masuk.data") }}',
            data: { status: 'BELUM_SAP' }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'nama_material', name: 'nama_material' },
            { data: 'qty', name: 'qty' },
            { data: 'tanggal_masuk', name: 'tanggal_masuk' },
            { data: 'status', name: 'status' },
            { data: 'created_by', name: 'created_by' },
            { data: 'action', name: 'action', orderable:false, searchable:false }
        ],
        responsive:true,
        drawCallback: function(settings){
            $('#pending-count').text(settings.json ? settings.json.recordsTotal : 0);
        }
    });

    // Inisialisasi tabel Sudah Selesai SAP
    approvedTable = $('#approvedTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("material-masuk.data") }}',
            data: { status: 'SELESAI_SAP' }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'nama_material', name: 'nama_material' },
            { data: 'qty', name: 'qty' },
            { data: 'tanggal_masuk', name: 'tanggal_masuk' },
            { data: 'status', name: 'status' },
            { data: 'created_by', name: 'created_by' },
            { data: 'tanggal_selesai_sap', name: 'tanggal_selesai_sap' }
        ],
        responsive:true,
        drawCallback: function(settings){
            $('#approved-count').text(settings.json ? settings.json.recordsTotal : 0);
        }
    });

});

// Fungsi tombol Selesai SAP
function selesaiSAP(id){
    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah material ini sudah selesai SAP?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, selesai',
        cancelButtonText: 'Batal'
    }).then((result)=>{
        if(result.isConfirmed){
            $.ajax({
                url: '{{ url("material-masuk/selesai-sap") }}/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(){
                    Swal.fire('Berhasil!', 'Material telah selesai SAP.', 'success');
                    pendingTable.ajax.reload();
                    approvedTable.ajax.reload();
                },
                error: function(){
                    Swal.fire('Error!', 'Gagal update status.', 'error');
                }
            });
        }
    });
}
</script>
@endpush
