<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Material;
use App\Models\StockOpname;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MaterialImport;
use Illuminate\Validation\Rule;
use App\Models\MaterialHistory;



class MaterialController extends Controller
{
    /**
     * Tampilkan daftar material
     */
    public function index()
    {
        $materials = Material::orderBy('nomor_kr')->paginate(15);
        return view('material.index', compact('materials'));
    }

    /**
     * Tampilkan form tambah material
     */
    public function create()
    {
        return view('material.create');
    }

    /**
     * Simpan material baru
     */
    public function store(Request $request)
    {
         \Log::info('Masuk ke MaterialController@store', [
        'method' => $request->method(),
        'data' => $request->all()
    ]);
        $validator = Validator::make($request->all(), [
            'company_code' => 'required|string|max:10',
            'company_code_description' => 'required|string|max:100',
            'plant' => 'required|string|max:10',
            'plant_description' => 'required|string|max:100',
            'storage_location' => 'required|string|max:10',
            'storage_location_description' => 'required|string|max:100',
            'material_type' => 'required|string|max:10',
            'material_type_description' => 'required|string|max:100',
             'material_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('materials')->whereNull('deleted_at'),
            ],
            'material_description' => 'required|string|max:255',
            'material_group' => 'required|string|max:20',
            'base_unit_of_measure' => 'required|string|max:10',
            'valuation_type' => 'required|string|max:20',
            // 'unrestricted_use_stock' => 'required|numeric|min:0',
            'quality_inspection_stock' => 'nullable|numeric|min:0',
            'blocked_stock' => 'nullable|numeric|min:0',
            'in_transit_stock' => 'nullable|numeric|min:0',
            'project_stock' => 'nullable|numeric|min:0',
            'valuation_class' => 'required|string|max:10',
            'valuation_description' => 'required|string|max:100',
            'harga_satuan' => 'required|numeric|min:0',
            // 'total_harga' => 'required|numeric|min:0',
            // 'currency' => 'required|string|max:3',
            // 'pabrikan' => 'required|string|max:100',
            // 'qty' => 'required|integer|min:1',
            // 'tanggal_terima' => 'required|date',
            // 'keterangan' => 'nullable|string|max:255',
            'rak' => 'nullable|string|max:50',
            // 'status' => 'required|in:' . implode(',', [
            //     Material::STATUS_BAIK,
            //     Material::STATUS_RUSAK,
            //     Material::STATUS_DALAM_PERBAIKAN
            // ]),
        ], [
            'company_code.required' => 'Company Code wajib diisi',
            'plant.required' => 'Plant wajib diisi',
            'material_code.required' => 'Material Code wajib diisi',
            'material_code.unique' => 'Material Code sudah digunakan',
            'material_description.required' => 'Deskripsi Material wajib diisi',
            'qty.required' => 'Quantity wajib diisi',
            'qty.min' => 'Quantity minimal 1',
            'tanggal_terima.required' => 'Tanggal terima wajib diisi',
            'tanggal_terima.date' => 'Format tanggal tidak valid',
            'harga_satuan.required' => 'Harga satuan wajib diisi',
            'total_harga.required' => 'Total harga wajib diisi',
            'pabrikan.required' => 'Pabrikan wajib diisi',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Log untuk debugging
            Log::info('=== MATERIAL CREATE ATTEMPT ===');
            Log::info('Request data:', $request->all());
            Log::info('User ID: ' . auth()->id());
            
            // Generate nomor otomatis
            $lastMaterial = Material::orderBy('nomor', 'desc')->first();
            $nextNomor = $lastMaterial ? $lastMaterial->nomor + 1 : 1;

            $materialData = $request->all();
            $materialData['nomor'] = $nextNomor;
            // $materialData['tanggal_terima'] = Carbon::parse($request->tanggal_terima);
            
            \Illuminate\Support\Facades\Log::info('Material data to be saved:', $materialData);

            $material = Material::create($materialData);
            
            Log::info('Material created successfully with ID: ' . $material->id);

            return redirect()->route('dashboard')->with('success', 'Material berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Material creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Gagal menyimpan material: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Import material dari file Excel
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ], [
            'file.required' => 'File Excel wajib dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $file = $request->file('file');
            
            DB::beginTransaction();
            
            // Import using Laravel Excel
            $import = new MaterialImport();
            Excel::import($import, $file);
            
            $results = $import->getResults();
            $successCount = $results['success_count'];
            $errorCount = $results['error_count'];
            $errors = $results['errors'];
            
            // Note: Tidak melakukan soft delete karena:
            // 1. Material duplicate yang di-update tidak boleh di-delete
            // 2. Material lama yang tidak ada di Excel tetap dipertahankan
            // 3. Hanya material baru yang ditambahkan atau material existing yang di-update
            
            DB::commit();
            
            $message = "Import berhasil! {$successCount} data berhasil diimport";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} data gagal diimport";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'details' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'errors' => array_slice($errors, 0, 10) // Limit error messages
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Material import failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimport data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan form edit material
     */
    public function edit($id)
    {
        $material = Material::findOrFail($id);
        return view('material.edit', compact('material'));
    }

    /**
     * Update material
     */
    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'company_code' => 'required|string|max:10',
            'company_code_description' => 'required|string|max:100',
            'plant' => 'required|string|max:10',
            'plant_description' => 'required|string|max:100',
            'storage_location' => 'required|string|max:10',
            'storage_location_description' => 'required|string|max:100',
            'material_type' => 'required|string|max:10',
            'material_type_description' => 'required|string|max:100',
            'material_code' => 'required|string|max:50|unique:materials,material_code,' . $id,
            'material_description' => 'required|string|max:255',
            'material_group' => 'required|string|max:20',
            'base_unit_of_measure' => 'required|string|max:10',
            // 'valuation_type' => 'required|string|max:20',
            'unrestricted_use_stock' => 'required|numeric|min:0',
            // 'quality_inspection_stock' => 'nullable|numeric|min:0',
            // 'blocked_stock' => 'nullable|numeric|min:0',
            // 'in_transit_stock' => 'nullable|numeric|min:0',
            'project_stock' => 'nullable|numeric|min:0',
            // 'valuation_class' => 'required|string|max:10',
            // 'valuation_description' => 'required|string|max:100',
            // 'harga_satuan' => 'required|numeric|min:0',
            // 'total_harga' => 'required|numeric|min:0',
            // 'currency' => 'required|string|max:3',
            // 'pabrikan' => 'required|string|max:100',
            // 'qty' => 'required|integer|min:1',
            // 'tanggal_terima' => 'required|date',
            // 'keterangan' => 'nullable|string|max:255',
            'rak' => 'nullable|string|max:50',
            // 'status' => 'required|in:' . implode(',', [
            //     Material::STATUS_BAIK,
            //     Material::STATUS_RUSAK,
            //     Material::STATUS_DALAM_PERBAIKAN
            // ]),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $materialData = $request->all();
            // $materialData['tanggal_terima'] = Carbon::parse($request->tanggal_terima);

            $material->update($materialData);

            return redirect()->route('dashboard')->with('success', 'Material berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui material: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Template kosong untuk Input Material Masuk
     */
    public function inputMaterialMasuk()
    {
        return view('material.input-masuk');
    }

    /**
     * Template kosong untuk Surat Jalan
     */
    public function suratJalan()
    {
        return view('material.surat-jalan');
    }

    /**
     * Tampilkan detail material
     */
    public function show(Material $material)
    {
        return view('material.show', compact('material'));
    }

    /**
     * Tampilkan form stock opname
     */
    public function stockOpname()
    {
        $materials = Material::select('id', 'nomor_kr', 'material_description', 'qty', 'base_unit_of_measure')
                            ->orderBy('nomor_kr')
                            ->get();
        
        return view('material.stock-opname', compact('materials'));
    }

    /**
     * Proses stock opname - update quantity material
     */
    public function processStockOpname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id',
            'new_qty' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255'
        ], [
            'material_id.required' => 'Material harus dipilih',
            'material_id.exists' => 'Material tidak ditemukan',
            'new_qty.required' => 'Quantity baru wajib diisi',
            'new_qty.integer' => 'Quantity harus berupa angka',
            'new_qty.min' => 'Quantity tidak boleh kurang dari 0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $material = Material::findOrFail($request->material_id);
            $oldQty = $material->qty;
            
            // Update quantity material
            $material->update([
                'qty' => $request->new_qty,
                'keterangan' => $request->keterangan ? $request->keterangan : $material->keterangan,
                'updated_by' => auth()->id()
            ]);

            $message = "Stock opname berhasil! Material '{$material->nomor_kr}' diupdate dari {$oldQty} menjadi {$request->new_qty} {$material->base_unit_of_measure}";
            
            return redirect()->route('material.stock-opname')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal melakukan stock opname: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * API untuk pencarian material (AJAX)
     */
public function search(Request $request)
{
    // $term = $request->get('term', '');
    
    $term = strtolower($request->get('term', ''));

$materials = Material::query()
    ->whereRaw('LOWER(material_description) LIKE ?', ["%{$term}%"])
    ->orWhereRaw('LOWER(material_code) LIKE ?', ["%{$term}%"])
    ->take(20)
    ->get(['id', 'material_code', 'material_description', 'base_unit_of_measure']);


    // Format hasil agar lebih rapi dan tidak error
    return response()->json($materials->map(function ($item) {
        return [
            'id' => $item->id,
            'material_code' => $item->material_code,
            'material_description' => $item->material_description,
            // 'satuan' => $item->base_unit_of_measure ?? 'PCS', // fallback PCS
            'satuan' => $item->base_unit_of_measure ?? 'BH',

        ];
    }));
}


public function getMaterialById($id)
{
    try {
        $material = \App\Models\Material::find($id);

        if (!$material) {
            return response()->json(['error' => 'Material tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $material->id,
            'material_code' => $material->material_code, // ← ini yang akan diisi ke kolom Normalisasi
            'material_description' => $material->material_description,
            // 'satuan' => $material->satuan ?? 'PCS',
            'satuan' => $material->base_unit_of_measure ?? 'BH',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}


    /**
     * API untuk autocomplete material berdasarkan normalisasi
     */
    public function autocomplete(Request $request)
{
    // support semua kemungkinan
    $query = $request->get('term') 
             ?? $request->get('q') 
             ?? $request->get('query') 
             ?? '';

    $query = strtolower($query);

    if (strlen($query) < 1) {
        return response()->json([]);
    }

    $materials = Material::where(function ($q) use ($query) {
            $q->whereRaw('LOWER(material_description) LIKE ?', ["%{$query}%"])
              ->orWhereRaw('LOWER(material_code) LIKE ?', ["%{$query}%"]);
        })
        ->select('id', 'material_code', 'material_description', 'base_unit_of_measure', 'unrestricted_use_stock')
        ->limit(10)
        ->get();

    return response()->json($materials->map(function ($m) {
        return [
            'id' => $m->id,
            'material_code' => $m->material_code,
            'material_description' => $m->material_description,
            'unrestricted_use_stock' => (int) $m->unrestricted_use_stock,
            'base_unit_of_measure' => $m->base_unit_of_measure ?? 'BH',
            'satuan' => $m->base_unit_of_measure ?? 'BH', // ✅ Ganti ini!
        ];
    }));
}


    /**
     * Get data untuk DataTables AJAX endpoint
     */
    public function getDataForDataTables(Request $request)
    {
        $materials = Material::select([
            'id',
            'material_code',
            'material_description',
            'qty',
            'base_unit_of_measure',
            'unrestricted_use_stock',
            'harga_satuan',
            'total_harga',
            'currency',
            'pabrikan',
            'rak',
            'status',
            'created_at'
        ]);

        return datatables($materials)
            ->addIndexColumn()
            ->editColumn('harga_satuan', function($row) {
                return number_format($row->harga_satuan, 0, ',', '.');
            })
            ->editColumn('total_harga', function($row) {
                return number_format($row->total_harga, 0, ',', '.');
            })
            ->editColumn('status', function($row) {
                $badgeClass = [
                    Material::STATUS_BAIK => 'success',
                    Material::STATUS_RUSAK => 'danger',
                    Material::STATUS_DALAM_PERBAIKAN => 'warning'
                ];
                $class = $badgeClass[$row->status] ?? 'secondary';
                return '<span class="badge badge-' . $class . '">' . $row->status . '</span>';
            })
            ->addColumn('action', function($row) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('material.show', $row->id) . '" class="btn btn-sm btn-info" title="Detail"><i class="fa fa-eye"></i></a>';
                $actions .= '<a href="' . route('material.edit', $row->id) . '" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-edit"></i></a>';
                $actions .= '<button class="btn btn-sm btn-danger" onclick="deleteMaterial(' . $row->id . ')" title="Hapus"><i class="fa fa-trash"></i></button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Get data untuk DataTable stock opname
     */
    public function getStockOpnameData(Request $request)
    {
        $stockOpnames = StockOpname::with(['material', 'createdBy'])
                                  ->select('stock_opnames.*');

        return datatables($stockOpnames)
            ->addIndexColumn()
            ->editColumn('created_at', function($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->editColumn('stock_system', function($row) {
                return number_format($row->stock_system, 2);
            })
            ->editColumn('stock_fisik', function($row) {
                return number_format($row->stock_fisik, 2);
            })
            ->editColumn('selisih', function($row) {
                $selisih = $row->selisih;
                $class = $selisih > 0 ? 'text-success' : ($selisih < 0 ? 'text-danger' : 'text-muted');
                $sign = $selisih > 0 ? '+' : '';
                return '<span class="' . $class . '">' . $sign . number_format($selisih, 2) . '</span>';
            })
            ->editColumn('keterangan', function($row) {
                return $row->keterangan ?: '-';
            })
            ->rawColumns(['selisih'])
            ->make(true);
    }

    /**
     * Simpan data stock opname
     */
    public function storeStockOpname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id',
            'stock_fisik' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Ambil data material
            $material = Material::findOrFail($request->material_id);
            $stockSystem = $material->unrestricted_use_stock;
            $stockFisik = $request->stock_fisik;
            $selisih = $stockFisik - $stockSystem;

            // Insert ke tabel stock_opnames
            $stockOpname = StockOpname::create([
                'material_id' => $material->id,
                'material_description' => $material->material_description,
                'stock_fisik' => $stockFisik,
                'stock_system' => $stockSystem,
                'selisih' => $selisih,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id()
            ]);

            // Update unrestricted_stock di tabel materials
            $material->update([
                'unrestricted_use_stock' => $stockFisik
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock opname berhasil disimpan',
                'data' => $stockOpname
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan stock opname: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus material
     */
    public function destroy(Material $material)
    {
        try {
            $materialName = $material->nomor_kr;
            $material->delete();
            
            return redirect()->route('material.index')->with('success', "Material '{$materialName}' berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->route('material.index')->with('error', 'Gagal menghapus material: ' . $e->getMessage());
        }
    }



    // ==================== API METHODS ====================

    /**
     * API List materials
     */
    public function apiList(Request $request)
    {
        $query = Material::query();
        
        // Filter berdasarkan parameter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('material_code', 'LIKE', "%{$search}%")
                  ->orWhere('material_description', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $materials = $query->orderBy('material_code')
                          ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $materials->items(),
            'pagination' => [
                'current_page' => $materials->currentPage(),
                'last_page' => $materials->lastPage(),
                'per_page' => $materials->perPage(),
                'total' => $materials->total()
            ]
        ]);
    }

    /**
     * Get material by kode
     */
    public function getByKode($kode)
    {
        $material = Material::where('material_code', $kode)->first();
        
        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $material
        ]);
    }

}