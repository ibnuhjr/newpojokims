<?php
namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    use App\Models\Material;
    use App\Models\SuratJalan;
    use App\Models\SuratJalanDetail;
    use Maatwebsite\Excel\Facades\Excel;
    use App\Exports\SuratJalanExport;
    use Barryvdh\DomPDF\Facade\Pdf;
    use App\Models\MaterialHistory;
    use App\Models\PengembalianHistory;



    class SuratJalanController extends Controller
    {

        public function __construct()
        {
            $this->middleware(function ($request, $next) {
                $user = auth()->user();

                // 👀 Guest: hanya boleh GET (view-only)
                if ($user && $user->role === 'guest' && !$request->isMethod('GET')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses ditolak! Guest hanya dapat melihat data.'
                    ], 403);
                }

                // 👷 Petugas & Admin: boleh semua action (create, edit, approve, dll)
                return $next($request);
            });
        }


        /**
         * Tampilkan halaman surat jalan
         */
        public function index()
        {
            return view('material.surat-jalan');
        }

        /**
         * Data untuk DataTables surat jalan
         */
        public function getData(Request $request)
        {
            $suratJalans = SuratJalan::with(['creator', 'approver'])
                                    ->select('surat_jalan.*')
                                    ->orderBy('tanggal', 'desc');

            return datatables($suratJalans)
        ->filter(function ($query) use ($request) {
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->whereRaw('LOWER(nomor_surat) LIKE ?', ['%' . strtolower($searchValue) . '%'])
                  ->orWhereRaw('LOWER(kepada) LIKE ?', ['%' . strtolower($searchValue) . '%'])
                  ->orWhereRaw('LOWER(berdasarkan) LIKE ?', ['%' . strtolower($searchValue) . '%'])
                  ->orWhereRaw('LOWER(keterangan) LIKE ?', ['%' . strtolower($searchValue) . '%']);
            });
        }

        // ✅ Tambahan untuk filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    })
    ->filterColumn('status', function($query, $keyword) {
        if (!empty($keyword)) {
            $query->where('status', $keyword);
        }
    })

                ->addIndexColumn()
                ->editColumn('tanggal', function($row) {
                    return $row->tanggal->format('d/m/Y');
                })
                ->editColumn('berdasarkan', function($row) {
                    return $row->berdasarkan ?? '-';
                })
                ->editColumn('keterangan', function($row) {
                    return $row->keterangan ?? '-';
                })
                ->editColumn('status', function($row) {
    switch ($row->status) {
        case 'BUTUH_PERSETUJUAN':
            $badge = 'warning'; // kuning
            break;
        case 'APPROVED':
            $badge = 'success'; // hijau
            break;
        case 'SELESAI':
            $badge = 'primary'; // 🔵 biru
            break;
        default:
            $badge = 'secondary'; // fallback
            break;
    }

    return '<span class="badge badge-' . $badge . '">' . strtoupper($row->status) . '</span>';
})

                ->editColumn('created_by', function($row) {
                    return $row->creator->nama ?? '-';
                })

                ->addColumn('action', function($row) {
                    $user = auth()->user();
                    $actions = '';

                    // Semua role bisa View detail (pakai modal AJAX)
                    $actions .= '<button type="button" class="btn btn-sm btn-info mr-1" title="View"
                                    onclick="showDetailSuratJalan(' . $row->id . ')">
                                    <i class="fa fa-eye"></i>
                                </button>';

                    // Guest: hanya bisa lihat
                    if ($user->role === 'guest') {
                        return $actions;
                    }

                    // Petugas & Admin: bisa full action
                    if (in_array($row->status, ['BUTUH_PERSETUJUAN', 'APPROVED'])) {
                        $actions .= '<a href="' . route('surat-jalan.edit', $row->id) . '" class="btn btn-sm btn-primary mr-1" title="Edit"><i class="fa fa-edit"></i></a>';
                    }


                    if ($row->status === 'APPROVED') {
                        $actions .= '<button class="btn btn-sm btn-success mr-1" onclick="printSuratJalan(' . $row->id . ')" title="Print"><i class="fa fa-print"></i></button>';
                    }

                    $actions .= '<button class="btn btn-sm btn-danger" onclick="deleteSuratJalan(' . $row->id . ')" title="Delete"><i class="fa fa-trash"></i></button>';

                    return $actions;
                })


                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        /**
         * Tampilkan form create surat jalan
         */
        public function create()
        {
            $nomorSurat = SuratJalan::generateNomorSurat('Normal');
            $materials = Material::all();
            return view('material.surat-jalan-create', compact('nomorSurat', 'materials'));
        }
        
        /**
         * Generate nomor surat untuk AJAX call
         */
        public function generateNomor(Request $request)
{
    $jenis = $request->get('jenis', 'Normal');
    $nomorSurat = SuratJalan::generateNomorSurat($jenis);

    return response()->json([
        'nomor_surat' => $nomorSurat
    ]);
}


        /**
         * Simpan surat jalan baru
         */
public function store(Request $request)
{
    Log::info('=== SURAT JALAN FORM SUBMISSION ===');
    Log::info('Request data:', $request->all());
    Log::info('User ID: ' . auth()->id());
    

    $validator = Validator::make($request->all(), [
        'nomor_surat' => 'required|string',
        'jenis_surat_jalan' => 'required|in:Normal,Garansi,Peminjaman,Perbaikan,Manual', // ✅ tambahkan Manual
        'tanggal' => 'required|date',
        'kepada' => 'required|string|max:255',
        'berdasarkan' => 'required|string',
        'security' => 'nullable|string|max:255',
        'keterangan' => 'nullable|string',
        'kendaraan' => 'nullable|string|max:255',
        'no_polisi' => 'nullable|string|max:20',
        'pengemudi' => 'nullable|string|max:255',
        'materials' => 'required|array|min:1',
    ]);

    // ✅ Tambahkan validasi dinamis tergantung jenis surat
    if ($request->jenis_surat_jalan === 'Manual') {
        $validator->addRules([
            'materials.*.nama_barang' => 'required|string|max:255',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.satuan' => 'required|string',
        ]);
    } else {
        $validator->addRules([
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.satuan' => 'required|string',
        ]);
    }

    if ($validator->fails()) {
        Log::error('Validation failed:', $validator->errors()->toArray());
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        DB::beginTransaction();

        $suratJalan = SuratJalan::create([
            'nomor_surat' => $request->nomor_surat,
            'jenis_surat_jalan' => $request->jenis_surat_jalan,
            'tanggal' => $request->tanggal,
            'kepada' => $request->kepada,
            'berdasarkan' => $request->berdasarkan,
            'security' => $request->security,
            'keterangan' => $request->keterangan,
            'kendaraan' => $request->kendaraan,
            'no_polisi' => $request->no_polisi,
            'pengemudi' => $request->pengemudi,
            'status' => 'BUTUH_PERSETUJUAN',
            'created_by' => auth()->id(),
        ]);

        foreach ($request->materials as $item) {
    if ($request->jenis_surat_jalan === 'Manual') {
        SuratJalanDetail::create([
            'surat_jalan_id' => $suratJalan->id,
            'is_manual' => true,
            'material_id' => null,
            'nama_barang_manual' => $item['nama_barang'],
            'satuan_manual' => $item['satuan'],
            'quantity' => $item['quantity'],
            'satuan' => $item['satuan'],
            'keterangan' => $item['keterangan'] ?? null,
        ]);
    } else {
        $detail = SuratJalanDetail::create([
            'surat_jalan_id' => $suratJalan->id,
            'is_manual' => false,
            'material_id' => $item['material_id'],
            'quantity' => $item['quantity'],
            'satuan' => $item['satuan'],
            'keterangan' => $item['keterangan'] ?? null,
        ]);

        // ✅ Tambahkan histori otomatis
        try {
    $material = Material::find($item['material_id']);
    $stockSisa = $material ? $material->qty - $item['quantity'] : 0;

    // MaterialHistory::create([
    //     'material_id' => (int) $item['material_id'],
    //     'tanggal' => $request->tanggal,
    //     'tipe' => 'KELUAR',
    //     'no_slip' => $request->berdasarkan ?? '-',
    //     'masuk' => 0,
    //     'keluar' => (int) $item['quantity'],
    //     'sisa_persediaan' => $stockSisa,
    //     'catatan' => 'Dari Surat Jalan #' . $request->nomor_surat,
    //     'surat_jalan_id' => $suratJalan->id,
    // ]);

    // Kurangi stok material (kalau mau otomatis)
    if ($material) {
        // $material->decrement('qty', $item['quantity']);
    }

    Log::info('✅ MaterialHistory tersimpan');
} catch (\Exception $e) {
    Log::error('❌ Gagal insert histori:', ['error' => $e->getMessage()]);
}

    }
}


        DB::commit();
        return redirect()->route('surat-jalan.index')
            ->with('success', 'Surat jalan berhasil dibuat dan menunggu persetujuan.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating surat jalan:', ['message' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Gagal membuat surat jalan: ' . $e->getMessage());
    }
}

        /**
         * Tampilkan detail surat jalan
         */
        public function show(SuratJalan $suratJalan)
        {
            $suratJalan->load('details.material', 'creator', 'approver');
            return view('material.surat-jalan-show', compact('suratJalan'));
        }

        /**
         * Tampilkan detail surat jalan untuk modal (tanpa layout)
         */
    public function getModalDetail($id)
    {
        try {
            $suratJalan = \App\Models\SuratJalan::with(['details.material', 'creator', 'approver'])->findOrFail($id);
            $html = view('material.surat-jalan-modal-detail', compact('suratJalan'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal load modal detail surat jalan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail surat jalan: ' . $e->getMessage()
            ]);
        }
    }

        /**
         * Tampilkan form edit surat jalan
         */
        public function edit(SuratJalan $suratJalan)
{
    // Pastikan hanya status tertentu yang boleh diedit
    if (!in_array($suratJalan->status, ['BUTUH_PERSETUJUAN', 'APPROVED'])) {
        return redirect()->route('surat-jalan.index')
                         ->with('swal_error', 'Surat jalan yang sudah selesai tidak dapat diedit!');
    }

    // ✅ Muat relasi detail dan material (agar muncul di view)
    $suratJalan->load(['details.material']);

    // ✅ Ambil semua material untuk autocomplete
    $materials = Material::all();

    // ✅ Kirim data ke view
    return view('material.surat-jalan-edit', compact('suratJalan', 'materials'));
}



        /**
         * Update surat jalan
         */
public function update(Request $request, SuratJalan $suratJalan)
{
    Log::info('=== SURAT JALAN UPDATE STARTED ===');
    $action = $request->input('action');

    // 🔵 Jika tombol "Tandai Selesai" ditekan
    if ($action === 'selesai') {
        Log::info("🟦 Tombol 'Tandai Selesai' ditekan untuk Surat Jalan ID {$suratJalan->id}");

        // Cegah jika belum di-approve
        if ($suratJalan->status !== 'APPROVED') {
            return redirect()->back()->with('swal_error', '❌ Surat jalan belum di-approve, tidak bisa diselesaikan.');
        }

        // Jalankan dulu update form-nya (simpan semua perubahan)
        $this->handleSuratJalanUpdate($request, $suratJalan);

        // Baru ubah status ke SELESAI
        $suratJalan->update(['status' => 'SELESAI']);

        Log::info("✅ Surat Jalan #{$suratJalan->nomor_surat} disimpan & ditandai SELESAI");

        return redirect()->route('surat-jalan.index')
            ->with('swal_success', '✅ Surat jalan berhasil disimpan dan ditandai sebagai SELESAI.');
    }

    // 🟢 Jika tombol biasa "Update Surat Jalan"
    $this->handleSuratJalanUpdate($request, $suratJalan);

    return redirect()->route('surat-jalan.index')
        ->with('swal_success', '✅ Surat jalan berhasil diperbarui.');
}
/**
 * Fungsi internal untuk update data surat jalan (tanpa ubah status)
 */
private function handleSuratJalanUpdate(Request $request, SuratJalan $suratJalan)
{
    Log::info('🧩 handleSuratJalanUpdate() dijalankan untuk ID ' . $suratJalan->id);

    $validator = Validator::make($request->all(), [
        'nomor_surat' => 'required|string',
        'jenis_surat_jalan' => 'required|in:Normal,Garansi,Peminjaman,Perbaikan,Manual',
        'tanggal' => 'required|date',
        'kepada' => 'required|string|max:255',
        'berdasarkan' => 'required|string',
        'materials' => 'required|array|min:1',
    ]);

    if ($request->jenis_surat_jalan === 'Manual') {
        $validator->addRules([
            'materials.*.nama_barang' => 'required|string|max:255',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.satuan' => 'required|string',
        ]);
    } else {
        $validator->addRules([
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.satuan' => 'required|string',
        ]);
    }

    $validator->validate();

    DB::transaction(function () use ($request, $suratJalan) {
        // 🧱 Update data utama surat jalan
        $suratJalan->update([
            'nomor_surat' => $request->nomor_surat,
            'jenis_surat_jalan' => $request->jenis_surat_jalan,
            'tanggal' => $request->tanggal,
            'kepada' => $request->kepada,
            'berdasarkan' => $request->berdasarkan,
            'security' => $request->security,
            'keterangan' => $request->keterangan,
            'kendaraan' => $request->kendaraan,
            'no_polisi' => $request->no_polisi,
            'pengemudi' => $request->pengemudi,
        ]);

        // 🧹 Hapus semua detail lama
        $suratJalan->details()->delete();

        // 🔁 Buat ulang detail
        foreach ($request->materials as $item) {
            $detail = SuratJalanDetail::create([
                'surat_jalan_id' => $suratJalan->id,
                'is_manual' => ($request->jenis_surat_jalan === 'Manual'),
                'material_id' => $item['material_id'] ?? null,
                'nama_barang_manual' => $item['nama_barang'] ?? null,
                'satuan_manual' => $item['satuan'] ?? null,
                'quantity' => $item['quantity'],
                'satuan' => $item['satuan'],
                'keterangan' => $item['keterangan'] ?? null,
            ]);

            // 🧩 Update histori material
            if (!$detail->is_manual && isset($item['material_id'])) {
                $material = Material::find($item['material_id']);
                $stockSisa = $material ? $material->qty - $item['quantity'] : 0;

                // MaterialHistory::updateOrCreate(
                //     [
                //         'surat_jalan_id' => $suratJalan->id,
                //         'material_id' => $item['material_id'],
                //     ],
                //     [
                //         'tanggal' => $request->tanggal,
                //         'tipe' => 'KELUAR',
                //         'no_slip' => $request->berdasarkan ?? '-',
                //         'masuk' => 0,
                //         'keluar' => $item['quantity'],
                //         'sisa_persediaan' => $stockSisa,
                //         'catatan' => 'Diperbarui dari Surat Jalan #' . $request->nomor_surat,
                //     ]
                // );
            }
        }
    });
}


        /**
         * Hapus surat jalan
         */
        public function destroy(SuratJalan $suratJalan)
        {
            

            try {
                $suratJalan->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Surat jalan berhasil dihapus.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus surat jalan: ' . $e->getMessage()
                ]);
            }
        }

        /**
         * Tampilkan halaman approval surat jalan
         */
        public function approval()
        {
            return view('material.surat-jalan-approval');
        }

        /**
         * Data untuk DataTables approval surat jalan
         */
    public function getApprovalData(Request $request)
    {
        $status = $request->get('status', 'BUTUH_PERSETUJUAN');
        
        $suratJalans = SuratJalan::with(['creator', 'approver'])
                                ->byStatus($status)
                                ->select('surat_jalan.*');

        return datatables($suratJalans)
            ->addIndexColumn()
            ->editColumn('tanggal', function($row) {
                return $row->tanggal->format('d/m/Y');
            })
            ->editColumn('status', function($row) {
                $badge = $row->status == 'APPROVED' ? 'success' : 'warning';
                return '<span class="badge badge-' . $badge . '">' . $row->status . '</span>';
            })
            ->editColumn('created_by', function($row) {
                return $row->creator->nama ?? '-';
            })
            ->editColumn('approved_by', function($row) {
                return $row->approver->nama ?? '-';
            })
            ->editColumn('approved_at', function($row) {
                return $row->approved_at ? $row->approved_at->format('d/m/Y H:i') : '-';
            })
            ->addColumn('action', function($row) {
                $actions = '';

                // Semua status bisa dilihat
                // $actions .= '<button class="btn btn-sm btn-info mr-1" onclick="viewDetail(' . $row->id . ')">
                //                 <i class="fa fa-eye"></i>
                //              </button>';

                if (!in_array($suratJalan->status, ['BUTUH_PERSETUJUAN', 'APPROVED'])) {
                    return redirect()->route('surat-jalan.index')
                                ->with('swal_error', 'Surat jalan tidak bisa diedit karena statusnya sudah SELESAI!');
                }



                if ($row->status === 'APPROVED') {
                    $actions .= '<button class="btn btn-sm btn-primary" onclick="printSuratJalan(' . $row->id . ')">
                                    <i class="fa fa-print"></i> Print
                                </button>';
                }

                return $actions;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }


        /**
         * Approve surat jalan
         */
public function approve(Request $request, SuratJalan $suratJalan)
{
    $user = auth()->user();

    // ✅ Hanya admin & petugas yang bisa approve
    if (!in_array($user->role, ['admin', 'petugas'])) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Admin dan Petugas yang dapat menyetujui surat jalan.'
            ], 403);
        }

        return redirect()->back()->with('swal_error', 'Akses ditolak! Hanya Admin dan Petugas yang dapat menyetujui surat jalan.');
    }

    if ($suratJalan->status != 'BUTUH_PERSETUJUAN') {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Surat jalan sudah disetujui sebelumnya.'
            ]);
        }

        return redirect()->back()->with('swal_error', 'Surat jalan sudah disetujui sebelumnya.');
    }

    DB::beginTransaction();
    try {
        foreach ($suratJalan->details as $detail) {

    // 🟩 Abaikan pengecekan stok untuk item Manual
    if ($detail->is_manual) {
        continue;
    }

    $materialModel = Material::find($detail->material_id);

    if (!$materialModel) {
        DB::rollBack();
        $message = "Material dengan ID {$detail->material_id} tidak ditemukan.";

        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $message])
            : redirect()->back()->with('swal_error', $message);
    }

    if ($materialModel->unrestricted_use_stock < $detail->quantity) {
        DB::rollBack();
        $message = "❌ Stok material '{$materialModel->material_description}' tidak mencukupi. ".
                  "(tersedia: {$materialModel->qty}, dibutuhkan: {$detail->quantity})";

        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $message])
            : redirect()->back()->with('swal_error', $message);
    }

    // 🔻 Kurangi stok hanya untuk NON-MANUAL
    $materialModel->decrement('qty', $detail->quantity);
    $materialModel->decrement('unrestricted_use_stock', $detail->quantity);
    MaterialHistory::create([
    'material_id' => $detail->material_id,
    'tanggal'     => now(),
    'tipe'        => 'KELUAR',
    'no_slip'     => $suratJalan->berdasarkan,
    'masuk'       => 0,
    'keluar'      => $detail->quantity,
    'sisa_persediaan' => $materialModel->qty, // stok SETELAH berkurang
    'catatan'     => $suratJalan->kepada,
]);

}

        // ✅ Update status surat jalan setelah stok diverifikasi
        $suratJalan->update([
            'status' => 'APPROVED',
            'approved_by' => $user->id,
            'approved_at' => now()
        ]);

        DB::commit();

        $message = 'Surat jalan berhasil disetujui ✅ Stok material telah diperbarui.';

        // ✅ Return sesuai tipe request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('surat-jalan.index')
            ->with('swal_success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('❌ Gagal approve surat jalan', ['error' => $e->getMessage()]);

        $message = 'Gagal menyetujui surat jalan: ' . $e->getMessage();

        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $message])
            : redirect()->back()->with('swal_error', $message);
    }
}
        // fungsi approve->selesai
        public function markAsSelesai(SuratJalan $suratJalan)
        {
            if ($suratJalan->status !== 'APPROVED') {
                return back()->with('swal_error', '❌ Surat jalan harus di-approve dulu sebelum bisa diselesaikan.');
            }

            $suratJalan->update([
                'status' => 'SELESAI'
            ]);

            return back()->with('swal_success', '✅ Surat jalan berhasil ditandai sebagai SELESAI.');
        }


        /**
         * Export surat jalan ke PDF
         */
        public function export(SuratJalan $suratJalan)
        {
            if ($suratJalan->status != 'APPROVED') {
                return redirect()->back()
                            ->with('error', 'Hanya surat jalan yang sudah disetujui yang dapat dicetak.');
            }

            $suratJalan->load('details.material', 'creator', 'approver');
            
            // Calculate number of pages needed
            $totalPages = $this->calculatePagesNeeded($suratJalan);
            
            // Replace invalid filename characters
            $filename = 'surat-jalan-' . str_replace(['/', '\\'], '-', $suratJalan->nomor_surat) . '.pdf';
            
            $pdf = Pdf::loadView('exports.surat-jalan-pdf', compact('suratJalan', 'totalPages'))
                    ->setPaper('a4', 'portrait');
            
            return $pdf->download($filename);
            
        }

        /**
         * Calculate the number of pages needed for the PDF
         */
        private function calculatePagesNeeded(SuratJalan $suratJalan)
        {
            // Base content takes approximately 1 page
            $baseContentHeight = 1;
            
            // Each material row takes approximately 25px
            // A4 page has approximately 700px usable height after margins
            // Header, info tables, and signature take about 400px
            // So we have about 300px for material table per page
            $materialsPerPage = 24; // Approximately 20 rows per page
            
            $totalMaterials = $suratJalan->details->count();
            
            if ($totalMaterials <= $materialsPerPage) {
                return 1; // All fits in one page
            }
            
            // Calculate additional pages needed for materials
            $additionalPages = ceil(($totalMaterials - $materialsPerPage) / 15); // 15 rows per additional page
            
            return $baseContentHeight + $additionalPages;
        }
        
        /**
         * Export surat jalan ke Excel (method lama untuk backup)
         */
        public function exportExcel(SuratJalan $suratJalan)
        {
            if ($suratJalan->status != 'APPROVED') {
                return redirect()->back()
                            ->with('error', 'Hanya surat jalan yang sudah disetujui yang dapat dicetak.');
            }

            $suratJalan->load('details.material', 'creator', 'approver');
            
            // Replace invalid filename characters
            $filename = 'surat-jalan-' . str_replace(['/', '\\'], '-', $suratJalan->nomor_surat) . '.xlsx';
            
            return Excel::download(new SuratJalanExport($suratJalan), $filename);
        }
        public function masa(Request $request)
{
    $jenis = ucfirst(strtolower($request->get('jenis')));

    // Jenis yang diperbolehkan masuk masa pengeluaran
    $allowedJenis = ['Garansi', 'Peminjaman', 'Perbaikan'];

    // Jika jenis tidak termasuk dalam daftar → stop
    if ($jenis && !in_array($jenis, $allowedJenis)) {
        return redirect()->back()
            ->with('swal_error', "Jenis surat jalan '$jenis' tidak termasuk masa pengeluaran.");
    }

    $suratJalans = SuratJalan::whereIn('status', ['APPROVED', 'SELESAI'])
                    ->whereIn('jenis_surat_jalan', $allowedJenis)
                    ->when($jenis, function ($query) use ($jenis) {
                        return $query->where('jenis_surat_jalan', $jenis);
                    })
                    ->with(['details.material'])
                    ->orderBy('tanggal', 'desc')
                    ->get();

    return view('material.masa', compact('suratJalans', 'jenis'));
}



public function kembalikan(Request $request, SuratJalan $surat, SuratJalanDetail $detail)
{
    \Log::info('MASUK KEMBALIKAN', [
        'surat_id' => $surat->id,
        'detail_id' => $detail->id,
        'request_data' => $request->all()
    ]);

    // ✅ Simpan hasil validasi ke variabel
    $validated = $request->validate([
        'nomor_surat_masuk' => 'required|string|max:255',
        'tanggal_masuk' => 'required|date',
        'jumlah_kembali' => 'required|integer|min:1|max:' . ($detail->quantity - ($detail->jumlah_kembali ?? 0)),
        'keterangan' => 'nullable|string',
    ]);

    // ✅ Simpan ke tabel pengembalian_histories
    PengembalianHistory::create([
        'surat_jalan_detail_id' => $detail->id,
        'nomor_surat_masuk' => $validated['nomor_surat_masuk'],
        'tanggal_masuk' => $validated['tanggal_masuk'],
        'jumlah_kembali' => $validated['jumlah_kembali'],
        'keterangan' => $validated['keterangan'] ?? null,
    ]);

    // 🧮 Update jumlah kembali di detail
    $jumlahKembaliBaru = $validated['jumlah_kembali'];
    $jumlahLama = $detail->jumlah_kembali ?? 0;
    $totalKembali = $jumlahLama + $jumlahKembaliBaru;

    $detail->update([
        'jumlah_kembali' => $totalKembali,
        'tanggal_kembali' => $validated['tanggal_masuk'],
    ]);

    return redirect()
        ->route('surat.masa', strtolower($surat->jenis_surat_jalan))
        ->with('success', 'Barang berhasil ditandai kembali.');
}


public function hapusDetailMasa(SuratJalan $surat, SuratJalanDetail $detail)
{
    if ($detail->surat_jalan_id !== $surat->id) {
        abort(404);
    }

    try {
        $detail->delete();
        return redirect()->route('surat.masa', ['jenis' => strtolower($surat->jenis_surat_jalan)])
                         ->with('success', 'Detail masa berhasil dihapus.');
    } catch (\Exception $e) {
        return redirect()->route('surat.masa', ['jenis' => strtolower($surat->jenis_surat_jalan)])
                         ->with('error', 'Gagal menghapus detail: ' . $e->getMessage());
    }
}

public function showReturnForm($suratId, $detailId)
{
    $surat = SuratJalan::findOrFail($suratId);
    $detail = $surat->details()
    ->with('pengembalianHistories')
    ->findOrFail($detailId);


    return view('material.pengembalian-masa', compact('surat', 'detail'));
}

public function getHistory(SuratJalanDetail $detail)
{
    $detail->loadMissing('pengembalianHistories', 'material', 'suratJalan');

    // Total kembali dari DB
    $totalMasuk = $detail->pengembalianHistories()->sum('jumlah_kembali');

    $sisa = max($detail->quantity - $totalMasuk, 0);

    return response()->json([
        'success' => true,
        'detail' => [
            'nomor_surat'    => optional($detail->suratJalan)->nomor_surat,
            'tanggal_keluar' => optional(optional($detail->suratJalan)->tanggal)
                                 ?->format('d/m/Y'),
            'material'       => optional($detail->material)->material_description 
                                 ?? $detail->nama_barang_manual,
            'keluar'         => $detail->quantity,
            'kembali'        => $totalMasuk,
            'sisa'           => $sisa,
        ],

        'history' => $detail->pengembalianHistories()
            ->orderBy('tanggal_masuk')
            ->get(['nomor_surat_masuk','tanggal_masuk','jumlah_kembali','keterangan'])
            ->map(function ($item, $i) {
                return [
                    'no' => $i + 1,
                    'nomor_surat_masuk' => $item->nomor_surat_masuk,
                    'tanggal_masuk' => \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y'),
                    'jumlah_kembali' => $item->jumlah_kembali,
                    'keterangan' => $item->keterangan ?? '-',
                ];
            }),
    ]);
}
    }