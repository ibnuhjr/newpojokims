@extends('layouts.app')

@section('title', 'Tambah Material - ASI System')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-plus-circle"></i> Tambah Material Baru
                </h3>
            </div>

            <div class="panel-body" style="padding-left:40px; padding-right:40px;">
                <form method="POST" action="{{ route('material.store') }}">
                    @csrf

                    <!-- INFORMASI DASAR -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-info-circle"></i> Informasi Dasar
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Material Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="material_code"
                                       value="{{ old('material_code') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deskripsi Material <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="2" name="material_description" required>{{ old('material_description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- INFORMASI COMPANY -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-building"></i> Informasi Company
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Company Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company_code"
                                       value="{{ old('company_code', '5300') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Company Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company_code_description"
                                       value="{{ old('company_code_description', 'UID Jawa Barat') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plant <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="plant"
                                       value="{{ old('plant', '5319') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plant Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="plant_description"
                                       value="{{ old('plant_description', 'PLN UP3 Cimahi') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Storage Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="storage_location"
                                       value="{{ old('storage_location', '2080') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Storage Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="storage_location_description"
                                       value="{{ old('storage_location_description', 'APJ Cimahi') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rak</label>
                                <input type="text" class="form-control" name="rak" value="{{ old('rak') }}">
                            </div>
                        </div>
                    </div>

                    <!-- INFORMASI MATERIAL TYPE -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-tags"></i> Informasi Material Type
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Material Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="material_type"
                                       value="{{ old('material_type', 'ZST1') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Material Type Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="material_type_description"
                                       value="{{ old('material_type_description', 'PLN Stock Materials') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Material Group <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="material_group"
                                       value="{{ old('material_group', 'ZM0106') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Unit of Measure <span class="text-danger">*</span></label>
                                <select class="form-control" name="base_unit_of_measure" required>
                                    <option value="">Pilih Unit</option>
                                    <option value="BH">BH</option>
                                    <option value="SET">SET</option>
                                    <option value="M">M</option>
                                    <option value="KG">KG</option>
                                    <option value="BTG">BTG</option>
                                    <option value="U">U</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- INFORMASI HARGA -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa fa-cubes"></i> Informasi Harga
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga Satuan (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="harga_satuan"
                                       value="{{ old('harga_satuan') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- HIDDEN FIELDS -->
                    <input type="hidden" name="valuation_type" value="NORMAL">
                    <input type="hidden" name="quality_inspection_stock" value="0">
                    <input type="hidden" name="blocked_stock" value="0">
                    <input type="hidden" name="in_transit_stock" value="0">
                    <input type="hidden" name="project_stock" value="0">
                    <input type="hidden" name="wbs_element" value="">
                    <input type="hidden" name="valuation_class" value="1000">
                    <input type="hidden" name="valuation_description" value="HAR-Material">

                    <!-- BUTTON -->
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>

                            <div>
                                <button type="reset" class="btn btn-warning">
                                    <i class="fa fa-undo"></i> Reset Form
                                </button>

                                <button type="submit" id="submit-btn" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Simpan Material
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
