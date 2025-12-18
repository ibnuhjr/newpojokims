@extends('layouts.app')

@section('title', 'Approval Surat Jalan')

@section('page-title', 'Approval Surat Jalan')

@push('styles')
<style>
    /* Clean styling without debug colors */
    * {
        box-sizing: border-box !important;
    }
    
    html, body {
        background-color: #f0f0f0 !important;
        overflow-x: auto !important;
        overflow-y: auto !important;
    }
    
    /* Clean DataTables styling */
     .dataTables_wrapper {
         display: block !important;
         visibility: visible !important;
         opacity: 1 !important;
         position: relative !important;
         z-index: 999 !important;
         width: 100% !important;
         overflow: visible !important;
         min-height: 400px !important;
     }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: block !important;
        visibility: visible !important;
    }
    
    /* Clean table styling */
    table.dataTable,
    #pendingTable,
    #approvedTable {
        display: table !important;
        visibility: visible !important;
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background-color: #ffffff !important;
        position: relative !important;
        z-index: 888 !important;
        min-height: 200px !important;
    }
    
    table.dataTable thead,
    #pendingTable thead,
    #approvedTable thead {
        display: table-header-group !important;
        visibility: visible !important;
        background-color: #000000 !important;
        position: relative !important;
        z-index: 887 !important;
    }
    
    table.dataTable tbody,
    #pendingTable tbody,
    #approvedTable tbody {
        display: table-header-group !important;
        visibility: visible !important;
        background-color: #ffffff !important;
        position: relative !important;
        z-index: 887 !important;
    }
    
    table.dataTable tbody,
    #pendingTable tbody,
    #approvedTable tbody {
        display: table-row-group !important;
    }
    
    table.dataTable tr,
    #pendingTable tr,
    #approvedTable tr {
        display: table-row !important;
        visibility: visible !important;
        background-color: #ffffff !important;
        position: relative !important;
        z-index: 886 !important;
    }
    
    table.dataTable th,
    #pendingTable th,
    #approvedTable th {
         display: table-cell !important;
         visibility: visible !important;
         background-color: #000000 !important;
         color: white !important;
         border: 1px solid #333333 !important;
         padding: 8px !important;
         position: relative !important;
         z-index: 885 !important;
     }
     
     table.dataTable td,
     #pendingTable td,
     #approvedTable td {
         display: table-cell !important;
         visibility: visible !important;
         background-color: #ffffff !important;
         border: 1px solid #dee2e6 !important;
         padding: 8px !important;
         position: relative !important;
         z-index: 885 !important;
     }
     
     /* Clean container styling */
     .tab-content,
     #approvalTabContent {
         background-color: #f8f9fa !important;
         padding: 20px !important;
         margin-top: 10px !important;
         position: relative !important;
         z-index: 777 !important;
         display: block !important;
         visibility: visible !important;
         opacity: 1 !important;
         width: 100% !important;
         min-height: 500px !important;
         overflow: visible !important;
     }
     
     .table-responsive {
         background-color: #ffffff !important;
         padding: 15px !important;
         margin: 10px 0 !important;
         position: relative !important;
         z-index: 666 !important;
         display: block !important;
         visibility: visible !important;
         opacity: 1 !important;
         width: 100% !important;
         min-height: 300px !important;
         overflow: visible !important;
     }
     
     .tab-pane,
     #butuh-persetujuan,
     #approved {
         display: block !important;
         visibility: visible !important;
         opacity: 1 !important;
         background-color: #ffffff !important;
         padding: 10px !important;
         position: relative !important;
         z-index: 555 !important;
         width: 100% !important;
         min-height: 400px !important;
     }
     
     /* Remove any potential overlay elements */
     .card-body::before,
     .card-body::after,
     .tab-pane::before,
     .tab-pane::after {
         display: none !important;
     }
     
     /* Ensure no white overlay */
     .card-body {
         position: relative !important;
         z-index: 1 !important;
     }
     
     .tab-pane {
         position: relative !important;
         z-index: 5 !important;
         background: transparent !important;
     }
     
     /* DataTables processing indicator */
     .dataTables_processing {
         background-color: rgba(255, 255, 255, 0.9) !important;
         border: 2px solid #007bff !important;
         color: #007bff !important;
         font-weight: bold !important;
         padding: 20px !important;
         z-index: 1000 !important;
         position: absolute !important;
     }
     
     /* Fix potential Bootstrap conflicts */
     .nav-tabs .nav-link.active {
         z-index: 2 !important;
     }
     
     /* Ensure table elements are visible */
     table.dataTable,
     table.dataTable * {
         position: relative !important;
         z-index: auto !important;
     }
     
     /* Remove any modal backdrop interference */
     .modal-backdrop {
         z-index: -1 !important;
     }
     
     /* Fix any potential overlay from other elements */
     .container-fluid,
     .row,
     .col-12,
     .card {
         position: relative !important;
         z-index: auto !important;
         background: transparent !important;
     }
    
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        color: #495057 !important;
        font-weight: 500;
    }
    
    .dataTables_length select,
    .dataTables_filter input {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
        background-color: #fff;
    }
    
    table.dataTable tbody tr {
        background-color: #ffffff;
        border-bottom: 1px solid #dee2e6;
    }
    
    table.dataTable tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    table.dataTable tbody tr:hover {
        background-color: #e9ecef !important;
    }
    
    table.dataTable tbody td {
        border: 1px solid #dee2e6;
        padding: 12px 8px;
        color: #495057;
        font-weight: 500;
    }
    
    .dataTables_paginate .paginate_button {
        border: 1px solid #dee2e6 !important;
        background: #ffffff !important;
        color: #495057 !important;
        margin: 0 2px;
        border-radius: 0.25rem;
    }
    
    .dataTables_paginate .paginate_button:hover {
        background: #e9ecef !important;
        border-color: #adb5bd !important;
    }
    
    .dataTables_paginate .paginate_button.current {
        background: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-check-circle me-2"></i>
                        Approval Surat Jalan
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="approvalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="butuh-persetujuan-tab" data-toggle="tab" href="#butuh-persetujuan" role="tab">
                                <i class="fa fa-clock me-1"></i>
                                Butuh Persetujuan
                                <span class="badge bg-warning ms-2" id="pending-count">0</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab">
                                <i class="fa fa-check me-1"></i>
                                Approved
                                <span class="badge bg-success ms-2" id="approved-count">0</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab panes -->
                    <div class="tab-content" id="approvalTabContent">
                        <!-- Tab Butuh Persetujuan -->
                        <div class="tab-pane fade show active" id="butuh-persetujuan" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped table-hover" id="pendingTable" style="width: 100%;">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="background-color: #343a40; color: white; border: 1px solid #dee2e6;">No</th>
                                            <th style="background-color: #343a40; color: white; border: 1px solid #dee2e6;">Nomor Surat</th>
                                            <th style="background-color: #343a40; color: white; border: 1px solid #dee2e6;">Tanggal</th>
                                            <th style="background-color: #343a40; color: white; border: 1px solid #dee2e6;">Kepada</th>
                                            <th style="background-color: #343a40; color: white; border: 1px solid #dee2e6;">Status</th>
                                            <th style="background-color: #343a40; color: white; border: 1px solid #dee2e6;">Dibuat Oleh</th>
                                            <th style="background-color: #343a40; color: white; border: 1px solid #dee2e6;">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Tab Approved -->
                        <div class="tab-pane fade" id="approved" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped table-hover" id="approvedTable" style="width: 100%;">
                                    <thead class="table-success">
                                        <tr>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">No</th>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">Nomor Surat</th>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">Tanggal</th>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">Kepada</th>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">Status</th>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">Dibuat Oleh</th>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">Disetujui Oleh</th>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">Tanggal Approval</th>
                                            <th style="background-color: #198754; color: white; border: 1px solid #dee2e6;">Aksi</th>
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

<!-- Modal Detail Surat Jalan -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fa fa-eye me-2"></i>
                    Detail Surat Jalan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Detail content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approval -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fa fa-check me-2"></i>
                    Konfirmasi Approval Surat Jalan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="approvalDetailContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat detail surat jalan...</p>
                    </div>
                </div>
                
                <hr>
                
                <p><strong>Apakah Anda yakin ingin menyetujui surat jalan ini?</strong></p>
                <div class="mb-3">
                    <label for="approval_notes" class="form-label">Catatan Approval (Opsional)</label>
                    <textarea class="form-control" id="approval_notes" rows="3" placeholder="Masukkan catatan approval..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmApproval">
                    <i class="fa fa-check me-1"></i>
                    Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let pendingTable, approvedTable;
let currentApprovalId = null;

$(document).ready(function() {
    console.log('Initializing approval page...');
    
    // Clean initialization - remove overlays and ensure visibility
    $('.overlay, .loading-overlay').remove();
    
    // Ensure containers are visible
    $('.tab-content, .table-responsive, .dataTables_wrapper').css({
        'display': 'block',
        'visibility': 'visible',
        'opacity': '1'
    });
    
    // Remove any potential overlay elements that might hide content
    $('body').find('*').each(function() {
        const $el = $(this);
        if ($el.css('position') === 'fixed' && $el.css('background-color') === 'rgb(255, 255, 255)') {
            if (!$el.hasClass('modal') && !$el.hasClass('dropdown-menu') && !$el.closest('.modal').length) {
                $el.hide();
            }
        }
    });
    
    // Initialize DataTables with delay
    setTimeout(function() {
        initializeTables();
    }, 500);
    
    // Tab change event
    $('#approvalTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        console.log('Tab changed to:', $(e.target).attr('href'));
        const target = $(e.target).attr('href');
        
        // Force show active tab content
        $(target).show();
        
        setTimeout(function() {
            if (target === '#approved' && approvedTable) {
                approvedTable.ajax.reload();
                approvedTable.columns.adjust().responsive.recalc();
            } else if (target === '#butuh-persetujuan' && pendingTable) {
                pendingTable.ajax.reload();
                pendingTable.columns.adjust().responsive.recalc();
            }
        }, 100);
    });
    
    // Adjust table columns after initialization
    setTimeout(function() {
        if (pendingTable) {
            pendingTable.columns.adjust().responsive.recalc();
        }
        if (approvedTable) {
            approvedTable.columns.adjust().responsive.recalc();
        }
    }, 1000);
});

function initializeTables() {
    console.log('Initializing DataTables...');
    
    // Check if elements exist
    if ($('#pendingTable').length === 0) {
        console.error('pendingTable element not found!');
        return;
    }
    
    if ($('#approvedTable').length === 0) {
        console.error('approvedTable element not found!');
        return;
    }
    
    console.log('Both table elements found, proceeding with initialization...');
    
    // Pending Table
    try {
        pendingTable = $('#pendingTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("surat-jalan.approval-data") }}',
                data: { status: 'BUTUH_PERSETUJUAN' },
                error: function(xhr, error, thrown) {
                    console.error('AJAX Error for pending table:', error, thrown);
                    console.error('Response:', xhr.responseText);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nomor_surat', name: 'nomor_surat' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'kepada', name: 'kepada' },
                { data: 'status', name: 'status' },
                { data: 'created_by', name: 'created_by' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            responsive: true,
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            drawCallback: function(settings) {
                console.log('Pending table drawn with', settings.json ? settings.json.recordsTotal : 0, 'records');
                updateBadgeCount('pending-count', settings.json ? settings.json.recordsTotal : 0);
            },
            initComplete: function() {
                console.log('Pending table initialization complete');
            }
        });
        console.log('Pending table initialized successfully');
    } catch (error) {
        console.error('Error initializing pending table:', error);
    }
    
    // Approved Table
    try {
        approvedTable = $('#approvedTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("surat-jalan.approval-data") }}',
                data: { status: 'APPROVED' },
                error: function(xhr, error, thrown) {
                    console.error('AJAX Error for approved table:', error, thrown);
                    console.error('Response:', xhr.responseText);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nomor_surat', name: 'nomor_surat' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'kepada', name: 'kepada' },
                { data: 'status', name: 'status' },
                { data: 'created_by', name: 'created_by' },
                { data: 'approved_by', name: 'approved_by' },
                { data: 'approved_at', name: 'approved_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            responsive: true,
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            drawCallback: function(settings) {
                console.log('Approved table drawn with', settings.json ? settings.json.recordsTotal : 0, 'records');
                updateBadgeCount('approved-count', settings.json ? settings.json.recordsTotal : 0);
            },
            initComplete: function() {
                console.log('Approved table initialization complete');
            }
        });
        console.log('Approved table initialized successfully');
    } catch (error) {
        console.error('Error initializing approved table:', error);
    }
}

function updateBadgeCount(badgeId, count) {
    document.getElementById(badgeId).textContent = count;
}

// View detail function
function viewDetail(id) {
    $.ajax({
        url: '{{ route("surat-jalan.show", ":id") }}'.replace(':id', id),
        type: 'GET',
        success: function(response) {
            $('#detailContent').html(response);
            $('#detailModal').modal('show');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat detail surat jalan.'
            });
        }
    });
}

// Approve function
function approveSuratJalan(id) {
    console.log("🔍 Memuat detail untuk approval ID:", id);
    currentApprovalId = id;

    $.ajax({
        url: `{{ route('surat-jalan.modal-detail', ':id') }}`.replace(':id', id),
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#approvalDetailContent').html(response.html);
                $('#approvalModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message || 'Tidak bisa memuat detail surat jalan.'
                });
            }
        },
        error: function(xhr) {
            console.error('XHR Error:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat memuat detail surat jalan.'
            });
        }
    });
}

// Confirm approval
$('#confirmApproval').click(function() {
    if (currentApprovalId) {
        const notes = $('#approval_notes').val();
        
        $.ajax({
            url: '{{ route("surat-jalan.approve", ":id") }}'.replace(':id', currentApprovalId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                notes: notes
            },
            success: function(response) {
                $('#approvalModal').modal('hide');
                $('#approval_notes').val('');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Surat jalan berhasil disetujui.'
                }).then(() => {
                    pendingTable.ajax.reload();
                    approvedTable.ajax.reload();
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menyetujui surat jalan.'
                });
            }
        });
    }
});

// Print function
function printSuratJalan(id) {
    window.open('{{ route("surat-jalan.export", ":id") }}'.replace(':id', id), '_blank');
}

// Delete function
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
                url: '{{ route("surat-jalan.destroy", ":id") }}'.replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Surat jalan berhasil dihapus.'
                    }).then(() => {
                        pendingTable.ajax.reload();
                        approvedTable.ajax.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal menghapus surat jalan.'
                    });
                }
            });
        }
    });
}
$(document).on('click', '.btn-approve', function() {
    const id = $(this).data('id');
    approveSuratJalan(id);
});

</script>
@endpush