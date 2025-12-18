<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\User;
use App\Models\MaterialMasuk;
use App\Models\SuratJalan;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\MaterialExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Monitoring;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard
     */
    public function index()
    {
        $stats = [
            'total_materials' => Material::count(),
            // 'total_stock' => Material::sum('qty'),
            'total_stock' => Material::sum('unrestricted_use_stock'), 
            'total_material_masuk' => MaterialMasuk::count(),
            'total_surat_jalan' => SuratJalan::count(),
        ];
        $monitorings = Monitoring::all();
        return view('dashboard.index', compact('stats','monitorings'));
    }

    /**
     * Data untuk DataTables
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $materials = Material::with(['creator', 'updater'])
            ->whereNull('deleted_at')
            ->select('materials.*');

            return DataTables::of($materials)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] && strlen($request->search['value']) >= 2) {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->whereRaw('LOWER(material_code) LIKE ?', ['%' . strtolower($searchValue) . '%'])
                              ->orWhereRaw('LOWER(material_description) LIKE ?', ['%' . strtolower($searchValue) . '%']);
                        });
                    }
                })
                ->addIndexColumn()
                ->editColumn('qty', function ($row) {
                    return $row->unrestricted_use_stock;
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group" role="group">';
                    
                    // View Detail - semua role bisa lihat
                    $actions .= '<button type="button" class="btn btn-info btn-sm" onclick="viewDetail(' . $row->id . ')" title="Lihat Detail">';
                    $actions .= '<i class="fa fa-eye"></i>';
                    $actions .= '</button>';
                    
                    // Edit dan Delete - hanya admin yang bisa
                    if (auth()->user()->isAdmin()) {
                        // Edit
                        $actions .= '<button type="button" class="btn btn-warning btn-sm" onclick="editMaterial(' . $row->id . ')" title="Edit">';
                        $actions .= '<i class="fa fa-edit"></i>';
                        $actions .= '</button>';
                        
                        // Delete
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteMaterial(' . $row->id . ')" title="Hapus">';
                        $actions .= '<i class="fas fa-trash"></i>';
                        $actions .= '</button>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->editColumn('tanggal_terima', function ($row) {
                    return $row->tanggal_terima ? $row->tanggal_terima->format('d F Y') : '-';
                })
                ->editColumn('rak', function ($row) {
                    return $row->rak ?? '-';
                })
                ->editColumn('harga_satuan', function ($row) {
                    return 'Rp ' . number_format($row->harga_satuan, 0, ',', '.');
                })
                ->editColumn('total_harga', function ($row) {
                    return 'Rp ' . number_format($row->total_harga, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    /**
     * Tampilkan detail material
     */
    public function show($id)
    {
        $material = Material::with(['creator', 'updater'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $material
        ]);
    }

    /**
     * Hapus material
     */
    public function destroy($id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Material berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus material: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman stock opname
     */
    public function stockOpname()
    {
        return view('dashboard.stock-opname');
    }

    /**
     * Get dashboard statistics for API
     */
    public function getStats(Request $request)
    {
        try {
            $stats = [
                'total_materials' => Material::count(),
                'total_stock' => Material::sum('qty'),
                'total_material_masuk' => MaterialMasuk::count(),
                'total_surat_jalan' => SuratJalan::count(),
                'active_materials' => Material::where('status', Material::STATUS_BAIK)->count(),
                'low_stock_materials' => Material::where('qty', '<=', 10)->count(),
                'total_value' => Material::sum('total_harga'),
                'recent_materials' => Material::latest()->take(5)->get([
                    'id', 'material_code', 'material_description', 'qty', 'status', 'created_at'
                ])
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * Export data material ke Excel
     */
    public function export()
    {
        try {
            $fileName = 'material_export_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new MaterialExport, $fileName);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }
}