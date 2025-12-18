@extends('layouts.app')

@section('title', 'Edit Material - ASI System')
@section('page-title', 'Edit Material')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="fa fa-edit"></i>
                    Edit Material: {{ $material->nomor_kr }}
                </h5>
            </div>
            
            <div class="panel-body" style="padding: 30px;">
                <form method="POST" action="{{ route('material.update', $material->id) }}">
                    @csrf
                    @method('PUT')
                    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Error!</strong> Periksa input Anda:<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                    
                    <!-- Informasi Dasar -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-info-circle me-1"></i>
                                Informasi Dasar
                            </h6>
                        </div>
                        

                        
                        <div class="col-md-6 mb-3">
                            <label for="material_code" class="form-label fw-semibold">
                                Material Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('material_code') is-invalid @enderror" 
                                   id="material_code" 
                                   name="material_code" 
                                   value="{{ old('material_code', $material->material_code) }}"
                                   placeholder="Contoh: 000000001060068"
                                   required>
                            @error('material_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="material_description" class="form-label fw-semibold">
                                Deskripsi Material <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('material_description') is-invalid @enderror" 
                                      id="material_description" 
                                      name="material_description" 
                                      rows="2"
                                      placeholder="Contoh: ISOLATOR;PINPOST;PORC;24KV;;12.5kN"
                                      required>{{ old('material_description', $material->material_description) }}</textarea>
                            @error('material_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- <div class="col-md-6 mb-3">
                            <label for="pabrikan" class="form-label fw-semibold">
                                Pabrikan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('pabrikan') is-invalid @enderror" 
                                   id="pabrikan" 
                                   name="pabrikan" 
                                   value="{{ old('pabrikan', $material->pabrikan) }}"
                                   placeholder="Contoh: KENTJANA SAKTI INDONESIA"
                                   required>
                            @error('pabrikan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> -->
                        
                        <!-- <div class="col-md-6 mb-3">
                            <label for="normalisasi" class="form-label fw-semibold">
                                Normalisasi
                            </label>
                            <input type="text" 
                                   class="form-control @error('normalisasi') is-invalid @enderror" 
                                   id="normalisasi" 
                                   name="normalisasi" 
                                   value="{{ old('normalisasi', $material->normalisasi) }}"
                                   placeholder="Opsional">
                            @error('normalisasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> -->
                    
                    <!-- Informasi Company -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-building me-1"></i>
                                Informasi Company
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="company_code" class="form-label fw-semibold">
                                Company Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('company_code') is-invalid @enderror" 
                                   id="company_code" 
                                   name="company_code" 
                                   value="{{ old('company_code', $material->company_code) }}"
                                   required>
                            @error('company_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="company_code_description" class="form-label fw-semibold">
                                Company Description <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('company_code_description') is-invalid @enderror" 
                                   id="company_code_description" 
                                   name="company_code_description" 
                                   value="{{ old('company_code_description', $material->company_code_description) }}"
                                   required>
                            @error('company_code_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="plant" class="form-label fw-semibold">
                                Plant <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('plant') is-invalid @enderror" 
                                   id="plant" 
                                   name="plant" 
                                   value="{{ old('plant', $material->plant) }}"
                                   required>
                            @error('plant')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="plant_description" class="form-label fw-semibold">
                                Plant Description <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('plant_description') is-invalid @enderror" 
                                   id="plant_description" 
                                   name="plant_description" 
                                   value="{{ old('plant_description', $material->plant_description) }}"
                                   required>
                            @error('plant_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="storage_location" class="form-label fw-semibold">
                                Storage Location <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('storage_location') is-invalid @enderror" 
                                   id="storage_location" 
                                   name="storage_location" 
                                   value="{{ old('storage_location', $material->storage_location) }}"
                                   required>
                            @error('storage_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="storage_location_description" class="form-label fw-semibold">
                                Storage Description <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('storage_location_description') is-invalid @enderror" 
                                   id="storage_location_description" 
                                   name="storage_location_description" 
                                   value="{{ old('storage_location_description', $material->storage_location_description) }}"
                                   required>
                            @error('storage_location_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="rak" class="form-label fw-semibold">
                                Rak
                            </label>
                            <input type="text" 
                                   class="form-control @error('rak') is-invalid @enderror" 
                                   id="rak" 
                                   name="rak" 
                                   value="{{ old('rak', $material->rak) }}"
                                   placeholder="Contoh: A-01-02">
                            @error('rak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Informasi Material Type -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-tags me-1"></i>
                                Informasi Material Type
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="material_type" class="form-label fw-semibold">
                                Material Type <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('material_type') is-invalid @enderror" 
                                   id="material_type" 
                                   name="material_type" 
                                   value="{{ old('material_type', $material->material_type) }}"
                                   required>
                            @error('material_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="material_type_description" class="form-label fw-semibold">
                                Material Type Description <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('material_type_description') is-invalid @enderror" 
                                   id="material_type_description" 
                                   name="material_type_description" 
                                   value="{{ old('material_type_description', $material->material_type_description) }}"
                                   required>
                            @error('material_type_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="material_group" class="form-label fw-semibold">
                                Material Group <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('material_group') is-invalid @enderror" 
                                   id="material_group" 
                                   name="material_group" 
                                   value="{{ old('material_group', $material->material_group) }}"
                                   required>
                            @error('material_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="base_unit_of_measure" class="form-label fw-semibold">
                                Unit of Measure <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('base_unit_of_measure') is-invalid @enderror" 
                                    id="base_unit_of_measure" 
                                    name="base_unit_of_measure" 
                                    required>
                                <option value="">Pilih Unit</option>
                                <option value="BH" {{ old('base_unit_of_measure', $material->base_unit_of_measure) == 'BH' ? 'selected' : '' }}>BH</option>
                                <option value="SET" {{ old('base_unit_of_measure', $material->base_unit_of_measure) == 'SET' ? 'selected' : '' }}>SET</option>
                                <option value="M" {{ old('base_unit_of_measure', $material->base_unit_of_measure) == 'M' ? 'selected' : '' }}>M</option>
                                <option value="KG" {{ old('base_unit_of_measure', $material->base_unit_of_measure) == 'KG' ? 'selected' : '' }}>KG</option>
                                <option value="BTG" {{ old('base_unit_of_measure', $material->base_unit_of_measure) == 'BTG' ? 'selected' : '' }}>BTG</option>
                                <option value="U" {{ old('base_unit_of_measure', $material->base_unit_of_measure) == 'U' ? 'selected' : '' }}>L</option>
                            </select>
                            @error('base_unit_of_measure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Informasi Stock & Harga -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-cubes me-1"></i>
                                Informasi Stock & Harga
                            </h6>
                        </div>
                        
                        <!-- <div class="col-md-4 mb-3">
                            <label for="qty" class="form-label fw-semibold">
                                Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('qty') is-invalid @enderror" 
                                   id="qty" 
                                   name="qty" 
                                   value="{{ old('qty', $material->qty) }}"
                                   min="1"
                                   required>
                            @error('qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> -->
                        
                        <div class="col-md-4 mb-3">
    <label for="unrestricted_use_stock" class="form-label fw-semibold">
        Unrestricted Stock <span class="text-danger">*</span>
    </label>

    <!-- Input yang terlihat (readonly) -->
    <input type="number" 
           class="form-control"
           value="{{ $material->unrestricted_use_stock }}"
           readonly>

    <!-- Input hidden yang dikirim ke server -->
    <input type="hidden" 
           id="unrestricted_use_stock" 
           name="unrestricted_use_stock" 
           value="{{ $material->unrestricted_use_stock }}">

    @error('unrestricted_use_stock')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                        <!-- <div class="col-md-4 mb-3">
                            <label for="tanggal_terima" class="form-label fw-semibold">
                                Tanggal Terima <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('tanggal_terima') is-invalid @enderror" 
                                   id="tanggal_terima" 
                                   name="tanggal_terima" 
                                   value="{{ old('tanggal_terima', $material->tanggal_terima ? $material->tanggal_terima->format('Y-m-d') : '') }}"
                                   required>
                            @error('tanggal_terima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> -->
                        
                        <div class="col-md-6 mb-3">
    <label for="harga_satuan" class="form-label fw-semibold">
        Harga Satuan (Rp) <span class="text-danger">*</span>
    </label>

    <!-- Input yang terlihat (readonly) -->
    <input type="number"
           class="form-control"
           value="{{ $material->harga_satuan }}"
           readonly>

    <!-- Hidden yang dikirim ke server -->
    <input type="hidden"
           id="harga_satuan"
           name="harga_satuan"
           value="{{ $material->harga_satuan }}">

    @error('harga_satuan')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                        
                        <div class="col-md-6 mb-3">
                            <label for="total_harga" class="form-label fw-semibold">
                                Total Harga (Rp) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('total_harga') is-invalid @enderror" 
                                   id="total_harga" 
                                   name="total_harga" 
                                   value="{{ old('total_harga', $material->total_harga) }}"
                                   min="0"
                                   readonly>
                            @error('total_harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Informasi Status & Keterangan -->
                    <!-- <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-info-circle me-1"></i>
                                Informasi Status & Keterangan
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="">Pilih Status</option>
                                <option value="BAIK" {{ old('status', $material->status) == 'BAIK' ? 'selected' : '' }}>Baik</option>
                                <option value="RUSAK" {{ old('status', $material->status) == 'RUSAK' ? 'selected' : '' }}>Rusak</option>
                                <option value="DALAM PERBAIKAN" {{ old('status', $material->status) == 'DALAM PERBAIKAN' ? 'selected' : '' }}>Dalam Perbaikan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="currency" class="form-label fw-semibold">
                                Currency <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('currency') is-invalid @enderror" 
                                    id="currency" 
                                    name="currency" 
                                    required>
                                <option value="">Pilih Currency</option>
                                <option value="IDR" {{ old('currency', $material->currency) == 'IDR' ? 'selected' : '' }}>IDR</option>
                                <option value="USD" {{ old('currency', $material->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency', $material->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="keterangan" class="form-label fw-semibold">
                                Keterangan
                            </label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3"
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $material->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                     -->
                    <!-- Hidden Fields -->
                    <!-- <input type="hidden" name="valuation_type" value="{{ $material->valuation_type }}">
                    <input type="hidden" name="quality_inspection_stock" value="{{ $material->quality_inspection_stock }}">
                    <input type="hidden" name="blocked_stock" value="{{ $material->blocked_stock }}">
                    <input type="hidden" name="in_transit_stock" value="{{ $material->in_transit_stock }}">
                    <input type="hidden" name="project_stock" value="{{ $material->project_stock }}">
                    <input type="hidden" name="wbs_element" value="{{ $material->wbs_element }}">
                    <input type="hidden" name="valuation_class" value="{{ $material->valuation_class }}">
                    <input type="hidden" name="valuation_description" value="{{ $material->valuation_description }}">
                     -->
                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('dashboard') }}" class="btn btn-default" style="z-index: 1000; position: relative;">
                                            <i class="fa fa-arrow-left"></i>
                                            Kembali
                                        </a>
                                    </div>
                                    
                                    <div>
                                        <button type="reset" class="btn btn-warning" style="margin-right: 10px; z-index: 1000; position: relative;">
                                            <i class="fa fa-undo"></i>
                                            Reset Form
                                        </button>
                                        
                                        <button type="submit" class="btn btn-primary" style="z-index: 1000; position: relative;">
                                            <i class="fa fa-save"></i>
                                            Update Material
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto calculate total harga
    function calculateTotal() {
        const qty = parseFloat($('#qty').val()) || 0;
        const hargaSatuan = parseFloat($('#harga_satuan').val()) || 0;
        const total = qty * hargaSatuan;
        $('#total_harga').val(total);
    }
    
    $('#qty, #harga_satuan').on('input', calculateTotal);
    
    // Auto sync qty with unrestricted_use_stock
    $('#qty').on('input', function() {
        $('#unrestricted_use_stock').val($(this).val());
        calculateTotal();
    });
    
    // Format currency input
    $('#harga_satuan, #total_harga').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(parseInt(value));
        }
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        $('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire('Error!', 'Mohon lengkapi semua field yang wajib diisi', 'error');
        }
    });
});
</script>
@endpush