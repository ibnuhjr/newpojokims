@extends('layouts.app')

@section('title', 'Edit Surat Jalan')

@section('page-title', 'Edit Surat Jalan')

@section('content')
<!-- <pre>{{ json_encode($suratJalan->details, JSON_PRETTY_PRINT) }}</pre> -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-edit me-2"></i>
                        Edit Surat Jalan: {{ $suratJalan->nomor_surat }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('surat-jalan.update', $suratJalan->id) }}" method="POST" id="suratJalanForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informasi Surat Jalan -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info border-bottom pb-2">
                                    <i class="fa fa-file-alt me-1"></i>
                                    Informasi Surat Jalan
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nomor_surat" class="form-label fw-semibold">
                                    Nomor Surat Jalan <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nomor_surat" 
                                       name="nomor_surat" 
                                       value="{{ $suratJalan->nomor_surat }}"
                                       readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="jenis_surat_jalan" class="form-label fw-semibold">
                                    Jenis Surat Jalan <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" 
                                        id="jenis_surat_jalan" 
                                        name="jenis_surat_jalan" 
                                        required>
                                    <option value="">Pilih Jenis Surat Jalan</option>
                                    <option value="Normal" {{ $suratJalan->jenis_surat_jalan == 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Garansi" {{ $suratJalan->jenis_surat_jalan == 'Garansi' ? 'selected' : '' }}>Garansi</option>
                                    <option value="Peminjaman" {{ $suratJalan->jenis_surat_jalan == 'Peminjaman' ? 'selected' : '' }}>Peminjaman</option>
                                    <option value="Perbaikan" {{ $suratJalan->jenis_surat_jalan == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                     <option value="Manual" {{ $suratJalan->jenis_surat_jalan == 'Manual' ? 'selected' : '' }}>Manual</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label fw-semibold">
                                    Tanggal Surat Jalan <span class="text-danger">*</span>
                                </label>
                                
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                           value="{{ old('tanggal', \Carbon\Carbon::parse($suratJalan->tanggal)->format('Y-m-d')) }}">
                                <!-- <input type="date" 
                                       class="form-control" 
                                       id="tanggal" 
                                       name="tanggal" 
                                       value="{{ $suratJalan->tanggal }}"
                                       required> -->
                            </div>
                        </div>
                        
                        <!-- Informasi Penerima -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info border-bottom pb-2">
                                    <i class="fa fa-user-check me-1"></i>
                                    Informasi Penerima
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="kepada" class="form-label fw-semibold">
                                    Nama Penerima <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="kepada" 
                                       name="kepada" 
                                       value="{{ $suratJalan->kepada }}"
                                       placeholder="Nama perusahaan/instansi penerima"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="berdasarkan" class="form-label fw-semibold">
                                    Berdasarkan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="berdasarkan" 
                                          name="berdasarkan" 
                                          rows="2"
                                          required>{{ $suratJalan->berdasarkan }}</textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="security" class="form-label fw-semibold">
                                    Security
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="security" 
                                       name="security" 
                                       value="{{ $suratJalan->security }}"
                                       placeholder="Nama security">
                            </div>
                        </div>
                        
                        <!-- Daftar Material -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info border-bottom pb-2">
                                    <i class="fa fa-boxes me-1"></i>
                                    Daftar Material
                                </h6>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="materialTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="35%">Material</th>
                                                <th width="10%">Stock</th>
                                                <th width="15%">Qty</th>
                                                <th width="15%">Satuan</th>
                                                <th width="20%">Keterangan</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($suratJalan->details as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>

                                            {{-- Kolom Material --}}
                                            <td>
                                                @if($suratJalan->status === 'APPROVED')
                                                    <input type="text" class="form-control form-control-sm" 
                                                        value="{{ $detail->material->material_code ?? '' }} - {{ $detail->material->material_description ?? '' }}" 
                                                        readonly>
                                                    <input type="hidden" name="materials[{{ $index }}][material_id]" value="{{ $detail->material_id }}">
                                                    <input type="hidden" name="materials[{{ $index }}][material_search]" 
                                                        value="{{ $detail->material->material_code ?? '' }} - {{ $detail->material->material_description ?? '' }}">
                                                @else
                                                    <input type="text" 
                                                        class="form-control form-control-sm material-autocomplete" 
                                                        name="materials[{{ $index }}][material_search]" 
                                                        value="{{ $detail->material->material_code ?? '' }} - {{ $detail->material->material_description ?? '' }}"
                                                        placeholder="Ketik kode atau nama material..."
                                                        autocomplete="off"
                                                        required>
                                                    <input type="hidden" 
                                                        name="materials[{{ $index }}][material_id]" 
                                                        value="{{ $detail->material_id }}"
                                                        class="material-id-input">
                                                    <div class="autocomplete-results" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; max-height: 400px; overflow-y: auto; width: 200%;"></div>
                                                @endif
                                            </td>

                                            {{-- Kolom Stock --}}
                                            <td>
                                                <input type="number" 
                                                    class="form-control form-control-sm" 
                                                    name="materials[{{ $index }}][stock]" 
                                                    value="{{ $detail->material->unrestricted_use_stock ?? 0 }}"
                                                    readonly>
                                            </td>

                                            {{-- Kolom Qty --}}
                                            <td>
                                                <input type="number" 
                                                    class="form-control form-control-sm" 
                                                    name="materials[{{ $index }}][quantity]" 
                                                    value="{{ $detail->quantity }}"
                                                    min="1"
                                                    @if($suratJalan->status === 'APPROVED') readonly @endif
                                                    required>
                                            </td>

                                            {{-- Kolom Satuan --}}
                                            <td>
                                                <input type="text" 
                                                    class="form-control form-control-sm" 
                                                    name="materials[{{ $index }}][satuan]" 
                                                    value="{{ $detail->satuan }}"
                                                    readonly>
                                            </td>

                                            {{-- Kolom Keterangan --}}
                                            <td>
                                                <input type="text" 
                                                    class="form-control form-control-sm" 
                                                    name="materials[{{ $index }}][keterangan]" 
                                                    value="{{ $detail->keterangan }}"
                                                    placeholder="Keterangan"
                                                    @if($suratJalan->status === 'APPROVED') readonly @endif>
                                            </td>

                                            {{-- Kolom Aksi --}}
                                            <td>
                                                @if($suratJalan->status !== 'APPROVED')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-2" style="display: flex !important; justify-content: flex-end !important; width: 100%;">
                                    <button type="button" class="btn btn-success btn-sm" onclick="addRow()">
                                        <i class="fa fa-plus me-1"></i>
                                        Tambah Material
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Keterangan -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info border-bottom pb-2">
                                    <i class="fa fa-sticky-note me-1"></i>
                                    Keterangan
                                </h6>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="keterangan" class="form-label fw-semibold">
                                    Keterangan Tambahan
                                </label>
                                <textarea class="form-control" 
                                          id="keterangan" 
                                          name="keterangan" 
                                          rows="3"
                                          placeholder="Keterangan tambahan untuk surat jalan (opsional)">{{ $suratJalan->keterangan }}</textarea>
                            </div>
                        </div>
                        
                        <!-- Informasi Kendaraan -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info border-bottom pb-2">
                                    <i class="fa fa-truck me-1"></i>
                                    Informasi Kendaraan
                                </h6>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="kendaraan" class="form-label fw-semibold">
                                    Kendaraan
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="kendaraan" 
                                       name="kendaraan" 
                                       value="{{ $suratJalan->kendaraan }}"
                                       placeholder="Jenis/Merk kendaraan">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="no_polisi" class="form-label fw-semibold">
                                    No. Polisi
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="no_polisi" 
                                       name="no_polisi" 
                                       value="{{ $suratJalan->no_polisi }}"
                                       placeholder="Nomor polisi kendaraan">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="pengemudi" class="form-label fw-semibold">
                                    Pengemudi
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="pengemudi" 
                                       name="pengemudi" 
                                       value="{{ $suratJalan->pengemudi }}"
                                       placeholder="Nama pengemudi">
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="row mt-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>

        <div class="d-flex gap-2">
            <button type="reset" class="btn btn-warning">
                <i class="fa fa-undo me-1"></i> Reset Form
            </button>

            @if($suratJalan->status === 'BUTUH_PERSETUJUAN')
<button type="button" class="btn btn-primary" onclick="approveSuratJalan({{ $suratJalan->id }})">
    <i class="fa fa-check me-1"></i> Approve Surat Jalan
</button>
@endif


{{-- 🔰 Tombol Update sekarang tampil lebih dulu --}}
<button type="submit" name="action" value="update" class="btn btn-success">
    <i class="fa fa-save me-1"></i> Update Surat Jalan
</button>

{{-- ✅ Tombol Tandai Selesai setelah Update --}}
@if($suratJalan->status === 'APPROVED')
    <button type="submit" name="action" value="selesai" class="btn btn-primary">
        <i class="fa fa-check-double me-1"></i> Tandai Selesai
    </button>
@endif

        </div>
    </div>
</div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table-responsive {
    overflow-x: auto;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.form-control-sm {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

#materialTable tbody tr:hover {
    background-color: #f8f9fa;
}

.invalid-feedback {
    display: block;
}

/* Autocomplete Styles */
.autocomplete-container {
    position: relative;
}

.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    z-index: 9999;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: none;
}

/* Ensure parent td has relative positioning */
td {
    position: relative;
}

.autocomplete-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.autocomplete-item:hover {
    background-color: #f8f9fa;
}

.autocomplete-item:last-child {
    border-bottom: none;
}
</style>
@endpush

@push('scripts')
<script>
let rowCount = {{ count($suratJalan->details) }};

// Materials data for JavaScript
const materialsData = [
    @foreach($materials as $material)
    {
        id: {{ $material->id }},
        kode: '{{ addslashes($material->material_code ?? '') }}',
        nama: '{{ addslashes($material->material_description ?? '') }}',
        satuan: '{{ addslashes($material->base_unit_of_measure ?? '') }}',
        stock: {{ $material->unrestricted_use_stock ?? 0 }}
    },
    @endforeach
];

// Function to add new row
function addRow() {
    const tbody = document.querySelector('#materialTable tbody');
    const newRow = document.createElement('tr');
    
    newRow.innerHTML = `
        <td>${rowCount + 1}</td>
        <td style="position: relative;">
            <input type="text" class="form-control form-control-sm material-autocomplete" 
                   name="materials[${rowCount}][material_search]" 
                   placeholder="Ketik untuk mencari material..." 
                   autocomplete="off" required>
            <input type="hidden" name="materials[${rowCount}][material_id]" class="material-id">
            <div class="autocomplete-results" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; max-height: 400px; overflow-y: auto; width: 200%;"></div>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="materials[${rowCount}][stock]" readonly disabled>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="materials[${rowCount}][quantity]" min="1" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="materials[${rowCount}][satuan]" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="materials[${rowCount}][keterangan]" placeholder="Keterangan">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    rowCount++;
    
    // Add autocomplete functionality for new row
    initializeAutocomplete(newRow.querySelector('.material-autocomplete'));
    
    // Add quantity validation for new row
    const quantityInput = newRow.querySelector('input[name*="[quantity]"]');
    addQuantityValidation(quantityInput);
    
    // Update row numbers
    updateRowNumbers();
}

// Function to remove row
function removeRow(button) {
    const row = button.closest('tr');
    const tbody = row.parentNode;
    
    if (tbody.children.length > 1) {
        row.remove();
        updateRowNumbers();
    } else {
        alert('Minimal harus ada satu material!');
    }
}

// Initialize autocomplete functionality
// =========================
// 🔧 FIXED initializeAutocomplete()
// =========================
function initializeAutocomplete(input) {
    let timeout;
    const resultsDiv = input.parentElement.querySelector('.autocomplete-results'); // div hasil autocomplete
    const hiddenInput = input.closest('tr').querySelector('.material-id-input, .material-id');
    const satuanInput = input.closest('tr').querySelector('input[name*="[satuan]"]');
    const stockInput = input.closest('tr').querySelector('input[name*="[stock]"]');

    // 🧠 Reset ID jika user mengetik manual
    input.addEventListener('input', function () {
        clearTimeout(timeout);

        // Kosongkan ID supaya backend tahu belum ada pilihan valid
        if (hiddenInput) hiddenInput.value = '';
        if (satuanInput) satuanInput.value = '';
        if (stockInput) stockInput.value = '';

        const query = this.value.trim();

        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`/material/autocomplete?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    resultsDiv.style.display = data.length ? 'block' : 'none';

                    data.forEach(material => {
                        const item = document.createElement('div');
                        item.className = 'autocomplete-item';
                        item.innerHTML = `
                            <strong>[${material.material_code}]</strong> ${material.material_description}<br>
                            <small class="text-muted">Stock: ${material.unrestricted_use_stock || 0} ${material.base_unit_of_measure || ''}</small>
                        `;

                        // 🖱️ Saat user klik hasil autocomplete
                        item.addEventListener('click', function () {
                            input.value = `[${material.material_code}] - ${material.material_description}`;
                            hiddenInput.value = material.id;
                            satuanInput.value = material.base_unit_of_measure || '';
                            stockInput.value = material.unrestricted_use_stock || 0;

                            resultsDiv.style.display = 'none';
                            input.style.borderColor = ''; // reset warna
                            input.style.backgroundColor = '';
                        });

                        resultsDiv.appendChild(item);
                    });
                })
                .catch(error => {
                    console.error('Error fetching materials:', error);
                    resultsDiv.style.display = 'none';
                });
        }, 300);
    });

    // 🔻 Tutup hasil saat klik di luar
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

    // 🧩 Validasi visual — kalau user tidak pilih dari daftar
    input.addEventListener('blur', function () {
        if (hiddenInput && hiddenInput.value === '') {
            input.style.borderColor = '#dc3545';
            input.style.backgroundColor = '#f8d7da';
        } else {
            input.style.borderColor = '';
            input.style.backgroundColor = '';
        }
    });
}

// Function to validate stock quantity
function validateStockQuantity(quantityInput, showAlert = true) {
    const row = quantityInput.closest('tr');
    const stockInput = row.querySelector('input[name*="[stock]"]');
    const materialSearch = row.querySelector('.material-autocomplete');
    
    const quantity = parseInt(quantityInput.value) || 0;
    const stock = parseInt(stockInput.value) || 0;
    
    if (quantity > stock && materialSearch.value.trim() !== '') {
        if (showAlert) {
            alert(`Quantity (${quantity}) melebihi stock yang tersedia (${stock}). Silakan kurangi quantity.`);
        }
        return false;
    }
    return true;
}

// Function to add quantity validation to input
function addQuantityValidation(quantityInput) {
    let lastValidatedValue = '';
    
    quantityInput.addEventListener('blur', function() {
        // Only show alert if value has changed since last validation
        if (this.value !== lastValidatedValue) {
            if (!validateStockQuantity(this, true)) {
                lastValidatedValue = this.value;
                this.focus();
                this.select();
            } else {
                lastValidatedValue = this.value;
            }
        }
    });
    
    quantityInput.addEventListener('input', function() {
        const row = this.closest('tr');
        const stockInput = row.querySelector('input[name*="[stock]"]');
        const materialSearch = row.querySelector('.material-autocomplete');
        
        const quantity = parseInt(this.value) || 0;
        const stock = parseInt(stockInput.value) || 0;
        
        // Real-time visual feedback
        if (quantity > stock && materialSearch.value.trim() !== '') {
            this.style.borderColor = '#dc3545';
            this.style.backgroundColor = '#f8d7da';
        } else {
            this.style.borderColor = '';
            this.style.backgroundColor = '';
        }
    });
}
function approveSuratJalan(id) {

    fetch(`/surat-jalan/${id}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (response.ok) {
            // LANGSUNG REDIRECT TANPA ALERT
            window.location.href = "/surat-jalan"; 
        } else {
            console.error("Approve gagal");
        }
    })
    .catch(error => {
        console.error("Error jaringan:", error);
    });
}

// Initialize autocomplete for existing inputs
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    const materialInputs = document.querySelectorAll('.material-autocomplete');
    console.log('Found material inputs:', materialInputs.length);
    console.log('Materials data:', materialsData);
    
    materialInputs.forEach((input, index) => {
        console.log(`Initializing autocomplete for input ${index}:`, input);
        initializeAutocomplete(input);
        
    });
    
    // Add validation to existing quantity inputs
    const quantityInputs = document.querySelectorAll('input[name*="[quantity]"]');
    quantityInputs.forEach(input => {
        addQuantityValidation(input);
    });
    
    // Update row numbers for existing rows
    updateRowNumbers();
});

// Function to update row numbers
function updateRowNumbers() {
    const rows = document.querySelectorAll('#materialTable tbody tr');
    rows.forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;
        
        // Update input names to maintain proper indexing
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name && name.includes('materials[')) {
                const newName = name.replace(/materials\[\d+\]/, `materials[${index}]`);
                input.setAttribute('name', newName);
            }
        });
    });
}

// Function to check for duplicate materials
function checkDuplicateMaterials() {
    const materialIds = document.querySelectorAll('input[name*="[material_id]"]');
    const materialSearchInputs = document.querySelectorAll('.material-autocomplete');
    const duplicates = [];
    const seenMaterials = new Map();
    
    materialIds.forEach((input, index) => {
        const materialId = input.value;
        const materialSearch = materialSearchInputs[index] ? materialSearchInputs[index].value.trim() : '';
        
        if (materialId && materialSearch) {
            // Extract material code from the search input format: [CODE - DESCRIPTION]
            const codeMatch = materialSearch.match(/\[([^\]]+)\s*-/);
            const materialCode = codeMatch ? codeMatch[1].trim() : '';
            
            // Check for duplicate by material_id
            if (seenMaterials.has(materialId)) {
                const firstOccurrence = seenMaterials.get(materialId);
                duplicates.push({
                    materialId: materialId,
                    materialCode: materialCode,
                    materialName: materialSearch,
                    rows: [firstOccurrence.row, index + 1]
                });
            } else {
                seenMaterials.set(materialId, {
                    row: index + 1,
                    code: materialCode,
                    name: materialSearch
                });
            }
        }
    });
    
    return duplicates;
}

// Form validation
// === Toggle mode Manual ===
document.addEventListener('DOMContentLoaded', function() {
    const jenisSelect = document.getElementById('jenis_surat_jalan');
    const table = document.getElementById('materialTable');

    function toggleManualMode() {
        const isManual = jenisSelect.value === 'Manual';

        // Ganti label kolom
        table.querySelector('th:nth-child(2)').textContent = isManual ? 'Nama Barang (Manual)' : 'Material';
        table.querySelector('th:nth-child(3)').textContent = isManual ? '-' : 'Stock';

        // Loop tiap baris tabel
        table.querySelectorAll('tbody tr').forEach((row, index) => {
            const materialTd = row.querySelector('td:nth-child(2)');
            const stockInput = row.querySelector('input[name*="[stock]"]');
            const satuanInput = row.querySelector('input[name*="[satuan]"]');

            // Ambil nilai lama dari row data (kalau belum ada, ambil dari input)
            const existingMaterialInput = row.querySelector('input[name*="[material_search]"]');
            const existingIdInput = row.querySelector('input[name*="[material_id]"]');
            const existingValue = existingMaterialInput ? existingMaterialInput.value : '';
            const existingId = existingIdInput ? existingIdInput.value : '';

            // simpan di dataset agar toggle tidak hilang
            if (!row.dataset.materialText) row.dataset.materialText = existingValue;
            if (!row.dataset.materialId) row.dataset.materialId = existingId;

            if (isManual) {
                // ubah ke input manual
                materialTd.innerHTML = `
                    <input type="text" class="form-control form-control-sm"
                           name="materials[${index}][nama_barang]"
                           value="${row.dataset.namaBarangManual || ''}"
                           placeholder="Nama barang manual..." required>
                `;
                satuanInput.readOnly = false;
                satuanInput.placeholder = "Isi satuan (misal: pcs, kg)";
                stockInput.value = '';
                stockInput.disabled = true;
            } else {
                // kembali ke autocomplete normal
                const materialText = row.dataset.materialText || '';
                const materialId = row.dataset.materialId || '';

                // rebuild hanya kalau sebelumnya manual
                if (materialTd.querySelector('input[name*="[nama_barang]"]')) {
                    materialTd.innerHTML = `
                        <input type="text" class="form-control form-control-sm material-autocomplete"
                               name="materials[${index}][material_search]"
                               value="${materialText}"
                               placeholder="Ketik untuk mencari material..." autocomplete="off" required>
                        <input type="hidden" name="materials[${index}][material_id]" class="material-id" value="${materialId}">
                        <div class="autocomplete-results" style="display:none;position:absolute;z-index:1000;background:white;border:1px solid #ddd;max-height:400px;overflow-y:auto;width:200%;"></div>
                    `;
                    initializeAutocomplete(row.querySelector('.material-autocomplete'));
                }

                satuanInput.readOnly = true;
                satuanInput.placeholder = '';
                stockInput.disabled = false;
            }
        });
    }

    // ⚠️ Jangan panggil di awal — biar data dari DB tetap tampil
    // toggleManualMode();

    // Jalankan kalau dropdown jenis surat jalan berubah
    jenisSelect.addEventListener('change', toggleManualMode);
    toggleManualMode(); // <-- TAMBAHKAN INI
});



</script>
@endpush

@push('styles')
<style>
.table-responsive {
    overflow: visible !important;
}
.table-responsive table {
    overflow: visible !important;
}
.autocomplete-results {
    z-index: 9999 !important;
}
</style>
@endpush