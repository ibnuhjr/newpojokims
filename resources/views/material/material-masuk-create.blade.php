@extends('layouts.app')

@section('title', 'Tambah Material Masuk')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Tambah Material Masuk</h3>
                    <a href="{{ route('material-masuk.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('material-masuk.store') }}" method="POST" id="materialMasukForm">
                        @csrf

                        {{-- --- Bagian Identitas Utama --- --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nomor_kr">Nomor KR</label>
                                <input type="text" class="form-control" id="nomor_kr" name="nomor_kr" placeholder="Masukkan Nomor KR" value="{{ old('nomor_kr') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pabrikan">Pabrikan</label>
                                <input type="text" class="form-control" id="pabrikan" name="pabrikan" placeholder="Masukkan Pabrikan" value="{{ old('pabrikan') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_masuk">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tanggal_keluar">Tanggal Keluar</label>
                                <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar" value="{{ old('tanggal_keluar') }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="jenis">Jenis</label>
                                <select name="jenis" id="jenis" class="form-control">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="B1" {{ old('jenis')=='B1' ? 'selected' : '' }}>B1</option>
                                    <option value="B2" {{ old('jenis')=='B2' ? 'selected' : '' }}>B2</option>
                                    <option value="A0" {{ old('jenis')=='A0' ? 'selected' : '' }}>A0</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nomor_po">Nomor PO</label>
                                <input type="text" class="form-control" id="nomor_po" name="nomor_po" placeholder="Masukkan Nomor PO" value="{{ old('nomor_po') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nomor_doc">Nomor DOC</label>
                                <input type="text" class="form-control" id="nomor_doc" name="nomor_doc" placeholder="Masukkan Nomor DOC" value="{{ old('nomor_doc') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tugas_4">Tug 4</label>
                                <input type="text" class="form-control" id="tugas_4" name="tugas_4" placeholder="Masukkan Tug 4" value="{{ old('tugas_4') }}">
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan (opsional)">{{ old('keterangan') }}</textarea>
                        </div>

                        {{-- --- Detail Material Table (JS Autocomplete tetap) --- --}}
                        <hr>
                        <h5>Detail Material</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="materialTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Material</th>
                                        <th>Normalisasi</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="materialTableBody">
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>
                                            <div class="autocomplete-container">
                                                <input type="text" class="form-control form-control-sm material-search" 
                                                    name="materials[0][material_description]" 
                                                    placeholder="Ketik untuk mencari material..." autocomplete="off" required>
                                                <input type="hidden" name="materials[0][material_id]" class="material-id">
<input type="hidden" name="materials[0][material_name]" class="material-name">

                                                <div class="autocomplete-results"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="autocomplete-container">
                                                <input type="text" class="form-control form-control-sm normalisasi-search" 
                                                    name="materials[0][normalisasi]" placeholder="Normalisasi">
                                                <div class="autocomplete-results"></div>
                                            </div>
                                        </td>
                                        <td><input type="number" class="form-control form-control-sm" name="materials[0][quantity]" placeholder="Qty" min="1" required></td>
                                        <td><input type="text" class="form-control form-control-sm" name="materials[0][satuan]" placeholder="Satuan" required></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeRow(this)" disabled>
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-success btn-sm mb-3" onclick="addRow()">
                            <i class="fa fa-plus"></i> Tambah Material
                        </button>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Material Masuk</button>
                            <a href="{{ route('material-masuk.index') }}" class="btn btn-secondary"><i class="fa fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.autocomplete-container {
    position: relative;
}

.autocomplete-results {
    position: absolute;
    width: max-content;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 400px;
    overflow-y: auto;
    z-index: 9999 !important;
    display: none;
}

.autocomplete-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.autocomplete-item:hover {
    background-color: #f8f9fa;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.table-responsive {
    overflow: visible !important;
}

.table-responsive table {
    overflow: visible !important;
}
</style>
@endpush

@push('scripts')
<script>
let rowIndex = 1;

function addRow() {
    const tbody = document.getElementById('materialTableBody');
    const newRow = document.createElement('tr');
    
    newRow.innerHTML = `
        <td class="text-center">${rowIndex + 1}</td>
        <td>
            <div class="autocomplete-container">
                <input type="text" class="form-control form-control-sm material-search" 
                       name="materials[${rowIndex}][material_description]" 
                       placeholder="Ketik untuk mencari material..." 
                       autocomplete="off" required>
                <input type="hidden" name="materials[${rowIndex}][material_id]" class="material-id">
<input type="hidden" name="materials[${rowIndex}][material_name]" class="material-name">

                <div class="autocomplete-results"></div>
            </div>
        </td>
        <td>
            <div class="autocomplete-container">
                <input type="text" class="form-control form-control-sm normalisasi-search" 
                       name="materials[${rowIndex}][normalisasi]" placeholder="Normalisasi">
                <div class="autocomplete-results"></div>
            </div>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" 
                   name="materials[${rowIndex}][quantity]" placeholder="Qty" min="1" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" 
                   name="materials[${rowIndex}][satuan]" placeholder="Satuan" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row" 
                    onclick="removeRow(this)">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    rowIndex++;
    
    // Update row numbers and enable/disable remove buttons
    updateRowNumbers();
    initializeAutocomplete(newRow.querySelector('.material-search'));
    initializeNormalisasiAutocomplete(newRow.querySelector('.normalisasi-search'));
}

function removeRow(button) {
    const row = button.closest('tr');
    row.remove();
    updateRowNumbers();
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('#materialTableBody tr');
    rows.forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;
        
        // Update name attributes
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            }
        });
        
        // Enable/disable remove button
        const removeBtn = row.querySelector('.remove-row');
        removeBtn.disabled = rows.length === 1;
    });
}

function initializeAutocomplete(input) {
    let timeout;
    
    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value;
        const resultsDiv = this.parentElement.querySelector('.autocomplete-results');
        const hiddenInput = this.parentElement.querySelector('.material-id');
        
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            hiddenInput.value = '';
            return;
        }
        
        timeout = setTimeout(() => {
            fetch(`{{ route('material-masuk.autocomplete.material') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(material => {
                            const item = document.createElement('div');
                            item.className = 'autocomplete-item';
                            item.innerHTML = `
                                <strong>${material.text}</strong><br>
                                <small class="text-muted">
                                    Normalisasi: ${material.normalisasi || 'N/A'} | 
                                    Satuan: ${material.satuan || ''}
                                </small>
                            `;
                            
                            item.addEventListener('click', () => {
                                input.value = material.text;
                                hiddenInput.value = material.id;
                                const nameInput = input.closest('tr').querySelector('.material-name');
if (nameInput) nameInput.value = material.text;

                                
                                // Auto-fill normalisasi
                                const normalisasiInput = input.closest('tr').querySelector('input[name*="[normalisasi]"]');
                                if (normalisasiInput && material.normalisasi) {
                                    normalisasiInput.value = material.normalisasi;
                                }
                                
                                // Auto-fill satuan if available
                                const satuanInput = input.closest('tr').querySelector('input[name*="[satuan]"]');
                                if (satuanInput && material.satuan) {
                                    satuanInput.value = material.satuan;
                                }
                                
                                resultsDiv.style.display = 'none';
                            });
                            
                            resultsDiv.appendChild(item);
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.innerHTML = '<div class="autocomplete-item">Tidak ada material ditemukan</div>';
                        resultsDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsDiv.style.display = 'none';
                });
        }, 300);
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!input.parentElement.contains(e.target)) {
            input.parentElement.querySelector('.autocomplete-results').style.display = 'none';
        }
    });
}

function initializeNormalisasiAutocomplete(input) {
    let timeout;
    
    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value;
        const resultsDiv = this.parentElement.querySelector('.autocomplete-results');
        
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }
        
        timeout = setTimeout(() => {
            fetch(`{{ route('material-masuk.autocomplete.normalisasi') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(material => {
                            const item = document.createElement('div');
                            item.className = 'autocomplete-item';
                            item.innerHTML = `
                                <strong>${material.text}</strong><br>
                                <small class="text-muted">
                                    Material: ${material.material_description || 'N/A'} | 
                                    Satuan: ${material.satuan || ''}
                                </small>
                            `;
                            
                            item.addEventListener('click', () => {
                                input.value = material.text;
                                
                                // Auto-fill material description
                                const materialInput = input.closest('tr').querySelector('input[name*="[material_description]"]');
                                const hiddenInput = input.closest('tr').querySelector('.material-id');
                                if (materialInput && material.material_description) {
                                    materialInput.value = material.material_description;
                                    hiddenInput.value = material.id;
                                }
                                
                                // Auto-fill satuan if available
                                const satuanInput = input.closest('tr').querySelector('input[name*="[satuan]"]');
                                if (satuanInput && material.satuan) {
                                    satuanInput.value = material.satuan;
                                }
                                
                                resultsDiv.style.display = 'none';
                            });
                            
                            resultsDiv.appendChild(item);
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.innerHTML = '<div class="autocomplete-item">Tidak ada normalisasi ditemukan</div>';
                        resultsDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsDiv.style.display = 'none';
                });
        }, 300);
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!input.parentElement.contains(e.target)) {
            input.parentElement.querySelector('.autocomplete-results').style.display = 'none';
        }
    });
}

// Initialize autocomplete for existing inputs
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.material-search').forEach(input => {
        initializeAutocomplete(input);
    });
    
    document.querySelectorAll('.normalisasi-search').forEach(input => {
        initializeNormalisasiAutocomplete(input);
    });
    
    updateRowNumbers();
});

// Form validation
document.getElementById('materialMasukForm').addEventListener('submit', function(e) {
    const materialInputs = document.querySelectorAll('.material-id');
    let hasValidMaterial = false;
    
    materialInputs.forEach(input => {
        if (input.value) {
            hasValidMaterial = true;
        }
    });
    
    if (!hasValidMaterial) {
        e.preventDefault();
        alert('Minimal harus ada satu material yang dipilih!');
        return false;
    }
});
</script>
@endpush