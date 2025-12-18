<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Material;
use App\Models\MaterialMasuk;
use App\Models\MaterialMasukDetail;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MaterialHistory;


class MaterialMasukController extends Controller
{
    public function index()
    {
        return view('material.material-masuk-index');
    }

    public function getData(Request $request)
    {
        $materialMasuk = MaterialMasuk::with(['details.material', 'creator'])
            ->select('material_masuk.*')
            ->orderBy('tanggal_masuk', 'desc');

        return DataTables::of($materialMasuk)
            ->addIndexColumn()
            ->addColumn('material_info', function ($row) {
                $materials = $row->details->map(function ($detail) {
                    $materialDesc = $detail->material->material_description ?? 'Material tidak diketahui';
                    return $materialDesc . ' (' . $detail->quantity . ' ' . $detail->satuan . ')';
                })->take(2)->implode('<br>');

                if ($row->details->count() > 2) {
                    $materials .= '<br><small class="text-muted">+' . ($row->details->count() - 2) . ' material lainnya</small>';
                }

                return $materials ?: '-';
            })
            ->addColumn('total_quantity', fn($row) => $row->details->sum('quantity'))
            ->addColumn('creator_name', fn($row) => $row->creator->nama ?? '-')
            ->addColumn('tanggal_masuk_formatted', fn($row) => Carbon::parse($row->tanggal_masuk)->format('d/m/Y'))
            ->addColumn('tanggal_keluar_formatted', fn($row) => $row->tanggal_keluar ? Carbon::parse($row->tanggal_keluar)->format('d/m/Y') : '-')
            ->addColumn('status_sap', function ($row) {
                return $row->status_sap === 'Selesai SAP'
                    ? '<span class="badge bg-success">Selesai SAP</span>'
                    : '<span class="badge bg-warning text-dark">Belum Selesai SAP</span>';
            })
            ->addColumn('action', function ($row) {
                $user = auth()->user();

                $btn = '<div class="btn-group" role="group">';
                $btn .= '<button type="button" class="btn btn-sm btn-info" onclick="showDetail(' . $row->id . ')" title="Detail"><i class="fa fa-eye"></i></button>';

                if ($user->role !== 'guest') {
                    if (strtolower(trim($row->status_sap)) !== 'selesai sap') {
                        $btn .= '<a href="' . route('material-masuk.edit', $row->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a>';
                    }

                    // 🗑️ Hapus tetap tampil
                    $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteMaterialMasuk(' . $row->id . ')" title="Hapus"><i class="fa fa-trash"></i></button>';
                }

                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['material_info', 'status_sap', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('material.material-masuk-create');
    }

    /** ========================= EDIT ========================= */
    public function edit($id)
    {
        $materialMasuk = MaterialMasuk::with('details.material')->findOrFail($id);
        $materials = Material::whereNotNull('material_description')
            ->select('id', 'material_description')
            ->get();

        return view('material.material-masuk-edit', compact('materialMasuk', 'materials'));
    }

    private function recordHistory($material_id, $qty, $no_slip, $catatan, $tanggal)
    {
        \App\Models\MaterialHistory::record(
            $material_id,
            'MASUK',
            $qty,
            $no_slip ?: '-',
            $catatan ?: null,
            $tanggal ?: now()
        );
    }

    /** ========================= STORE ========================= */
    
    public function store(Request $request)
    {
        \Log::info('🟦 MATERIAL DIKIRIM FRONTEND:', $request->materials);
        $request->validate([
            'tanggal_masuk' => 'required|date',
            'materials' => 'required|array|min:1',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.satuan' => 'required|string|max:50',
        ]);
         // 🟥 LOG FULL MATERIALS
    \Log::info('🟥 FULL MATERIALS REQUEST:', $request->materials);

        $materials = $request->input('materials');

        foreach ($materials as $index => $item) {
            $material = null;
            
            if (!empty($item['material_id'])) {
                $material = Material::find($item['material_id']);
            }
            if (!$material && !empty($item['material_name'])) {
                // $material = Material::whereRaw('LOWER(material_description) = ?', [strtolower(trim($item['material_name']))])->first();
                // $material = Material::whereRaw('LOWER(material_description) LIKE ?', ['%' . strtolower(trim($item['material_name'])) . '%'])->first();
                $material = Material::whereRaw('LOWER(material_description) = ?', [strtolower(trim($item['material_name']))])->first();


            }
            if (!$material) {
                return back()->withErrors([
                    "materials.{$index}.material_name" =>
                        "Material tidak dikenali. Silakan pilih material dari daftar atau pastikan nama material yang Anda ketik sudah benar."
                ])->withInput();
            }
            $materials[$index]['material_id'] = $material->id;
        }

        $request->merge(['materials' => $materials]);

        DB::beginTransaction();
        try {
            $materialMasuk = MaterialMasuk::create([
                'nomor_kr' => $request->nomor_kr,
                'pabrikan' => $request->pabrikan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_keluar' => $request->tanggal_keluar,
                'jenis' => $request->jenis,
                'nomor_po' => $request->nomor_po,
                'nomor_doc' => $request->nomor_doc,
                'tugas_4' => $request->tugas_4,
                'keterangan' => $request->keterangan,
                'status_sap' => 'Belum Selesai SAP',
                'created_by' => auth()->id(),
            ]);

            foreach ($materials as $item) {
                $material = Material::findOrFail($item['material_id']);
                MaterialMasukDetail::create([
                    'material_masuk_id' => $materialMasuk->id,
                    'material_id' => $material->id,
                    'quantity' => $item['quantity'],
                    'satuan' => $item['satuan'],
                    'normalisasi' => $item['normalisasi'] ?? null,
                ]);

                // $material->safeIncrement('qty', $item['quantity']);
                // $material->safeIncrement('unrestricted_use_stock', $item['quantity']);
                // 🧾 Catat histori material MASUK
                // MaterialHistory::create([
                //     'material_id'      => $material->id,
                //     'tanggal'          => $request->tanggal_masuk ?? now(),
                //     'tipe'             => 'MASUK',
                //     'no_slip'          => $request->nomor_doc ?? '-',
                //     'masuk'            => (int) $item['quantity'],
                //     'keluar'           => 0,
                //     'sisa_persediaan'  => $material->qty,
                //     'catatan'          => 'Material masuk dari ' . ($request->pabrikan ?? 'Penerimaan Material'),
                // ]);
                \Log::info('Calling record()', [
    'material_id' => $material->id,
    'qty' => $item['quantity'],
    'no_slip' => $request->nomor_doc,
    'tanggal' => $request->tanggal_masuk
]);


                $this->recordHistory(
            $material->id,
            $item['quantity'],
            $request->nomor_doc,
            $request->pabrikan,
            $request->tanggal_masuk
        );



            }

            DB::commit();
            return redirect()->route('material-masuk.index')->with('success', 'Material masuk berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan material masuk: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'tanggal_masuk' => 'required|date',
        'materials' => 'required|array|min:1',
        'materials.*.material_id' => 'required|exists:materials,id',
        'materials.*.quantity' => 'required|integer|min:1',
        'materials.*.satuan' => 'required|string|max:50',
    ]);

    \Log::info('=== SUBMIT EDIT MATERIAL MASUK ===');
    \Log::info('Request all:', $request->all());

    DB::beginTransaction();

    try {
        $materialMasuk = MaterialMasuk::with('details')->findOrFail($id);
        \Log::info("Ditemukan material_masuk ID: {$id}");

        $materialMasuk->update($request->only([
            'nomor_kr', 'pabrikan', 'tanggal_masuk', 'tanggal_keluar',
            'jenis', 'nomor_po', 'nomor_doc', 'tugas_4', 'keterangan'
        ]));

        \Log::info("Berhasil update material_masuk utama");

        $existingDetailIds = [];

        foreach ($request->materials as $item) {
            \Log::info("Proses material ID: {$item['material_id']} (Detail ID: {$item['detail_id']})");

            $materialBaru = Material::findOrFail($item['material_id']);

            if (!empty($item['detail_id'])) {
                $detail = MaterialMasukDetail::findOrFail($item['detail_id']);

                \Log::info("Update detail ID: {$detail->id}");

                $diff = $item['quantity'] - $detail->quantity;

                $detail->update([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'satuan' => $item['satuan'],
                    'normalisasi' => $item['normalisasi'] ?? null,
                ]);
                $this->recordHistory(
                $item['material_id'],
                $item['quantity'],
                $request->nomor_doc,
                $request->pabrikan,
                // 'Perubahan jumlah material masuk',
                $request->tanggal_masuk
            );

//                 MaterialHistory::create([
//     'material_id'      => $item['material_id'],
//     'tanggal'          => $request->tanggal_masuk ?? now(),
//     'tipe'             => 'MASUK',
//     'no_slip'          => $request->nomor_doc ?? '-',
//     'masuk'            => (int)$item['quantity'],
//     'keluar'           => 0,
//     'sisa_persediaan'  => Material::find($item['material_id'])->qty,
//     'catatan'          => 'TEST pencatatan MASUK'
// ]);


                $existingDetailIds[] = $detail->id;

            } else {
                \Log::info("Tambah detail baru");

                $detail = MaterialMasukDetail::create([
                    'material_masuk_id' => $materialMasuk->id,
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'satuan' => $item['satuan'],
                    'normalisasi' => $item['normalisasi'] ?? null,
                ]);

                $existingDetailIds[] = $detail->id;
            }
        }

        \Log::info("Cek dan hapus detail yang dihapus user");

        $deletedDetails = $materialMasuk->details()->whereNotIn('id', $existingDetailIds)->get();
        foreach ($deletedDetails as $detail) {
            \Log::info("Hapus detail ID: {$detail->id}");
            $detail->delete();
        }

        DB::commit();
        \Log::info("✅ MaterialMasuk berhasil diupdate ID: " . $materialMasuk->id);

        return redirect()->route('material-masuk.index')->with('success', 'Material masuk berhasil diperbarui.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('❌ Gagal update material masuk: ' . $e->getMessage());
        return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
    }
}



    /** ========================= UPDATE & SELESAI SAP ========================= */
public function updateDanSelesaiSAP(Request $request, $id)
{
    $request->validate([
        'tanggal_masuk' => 'required|date',
        'materials' => 'required|array|min:1',
        'materials.*.quantity' => 'required|integer|min:1',
        'materials.*.satuan' => 'required|string|max:50',
    ]);

    DB::beginTransaction();
    try {
        $materialMasuk = MaterialMasuk::with('details')->findOrFail($id);

        // UPDATE DATA UTAMA
        $materialMasuk->update(array_merge(
            $request->only([
                'nomor_kr', 'pabrikan', 'tanggal_masuk', 'tanggal_keluar',
                'jenis', 'nomor_po', 'nomor_doc', 'tugas_4', 'keterangan'
            ]),
            [
                'status_sap' => 'Selesai SAP',
                'tanggal_sap' => now(),
            ]
        ));

        $existingDetailIds = [];

        foreach ($request->materials as $item) {
            $material = Material::findOrFail($item['material_id']);

            // CASE 1 — DETAIL LAMA
            if (!empty($item['detail_id'])) {

                $detail = MaterialMasukDetail::find($item['detail_id']);

                $diff = $item['quantity'] - $detail->quantity;

                // update detail
                $detail->update([
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'satuan' => $item['satuan'],
                    'normalisasi' => $item['normalisasi'] ?? null,
                ]);

                // update stok
                if ($diff > 0) {
                    $material->safeIncrement('qty', $diff);
                    $material->safeIncrement('unrestricted_use_stock', $diff);
                } elseif ($diff < 0) {
                    $material->safeDecrement('qty', abs($diff));
                    $material->safeDecrement('unrestricted_use_stock', abs($diff));
                }

                // PENCATATAN HISTORY FIX
                MaterialHistory::record(
                    $material->id,
                    'MASUK',
                    $item['quantity'],
                    $request->nomor_doc ?? '-',
                    'SAP Material Masuk (Update)',
                    $request->tanggal_masuk
                );

                $existingDetailIds[] = $detail->id;
            }

            // CASE 2 — DETAIL BARU
            else {
                $detail = MaterialMasukDetail::create([
                    'material_masuk_id' => $materialMasuk->id,
                    'material_id' => $material->id,
                    'quantity' => $item['quantity'],
                    'satuan' => $item['satuan'],
                    'normalisasi' => $item['normalisasi'] ?? null,
                ]);

                $material->safeIncrement('qty', $item['quantity']);
                $material->safeIncrement('unrestricted_use_stock', $item['quantity']);

                // HISTORY BARU
                MaterialHistory::record(
                    $material->id,
                    'MASUK',
                    $item['quantity'],
                    $request->nomor_doc ?? '-',
                    'SAP Material Masuk',
                    $request->tanggal_masuk
                );

                $existingDetailIds[] = $detail->id;
            }
        }

        // CASE 3 — DETAIL DIHAPUS USER
        $removed = $materialMasuk->details()->whereNotIn('id', $existingDetailIds)->get();
        foreach ($removed as $detail) {
            $material = Material::find($detail->material_id);

            if ($material) {
                $material->safeDecrement('qty', $detail->quantity);
                $material->safeDecrement('unrestricted_use_stock', $detail->quantity);
            }

            $detail->delete();
        }

        DB::commit();
        return redirect()->route('material-masuk.index')
            ->with('success', 'Material masuk berhasil disimpan & Selesai SAP.');
    }

    catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal SAP: ' . $e->getMessage())->withInput();
    }
}

/** ========================= SHOW / VIEW DETAIL ========================= */
        public function show($id)
        {
            $materialMasuk = MaterialMasuk::with(['details.material', 'creator'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $materialMasuk->id,
                    'nomor_kr' => $materialMasuk->nomor_kr ?? '-',
                    'pabrikan' => $materialMasuk->pabrikan ?? '-',
                    'tanggal_masuk' => $materialMasuk->tanggal_masuk
                        ? Carbon::parse($materialMasuk->tanggal_masuk)->format('d/m/Y')
                        : '-',
                    'tanggal_keluar' => $materialMasuk->tanggal_keluar
                        ? Carbon::parse($materialMasuk->tanggal_keluar)->format('d/m/Y')
                        : '-',
                    'nomor_po' => $materialMasuk->nomor_po ?? '-',
                    'nomor_doc' => $materialMasuk->nomor_doc ?? '-',
                    'jenis' => $materialMasuk->jenis ?? '-',
                    'status_sap' => $materialMasuk->status_sap ?? '-',
                    'tanggal_sap' => $materialMasuk->tanggal_sap
                        ? Carbon::parse($materialMasuk->tanggal_sap)->format('d/m/Y H:i')
                        : '-',
                    'tugas_4' => $materialMasuk->tugas_4 ?? '-',
                    'keterangan' => $materialMasuk->keterangan ?? '-',
                    'created_by' => $materialMasuk->creator->nama ?? '-',
                    'created_at' => $materialMasuk->created_at
                        ? $materialMasuk->created_at->format('d/m/Y H:i')
                        : '-',
                    'details' => $materialMasuk->details->map(function ($detail) {
                        return [
                            'material_code' => $detail->material->material_code ?? '-',
                            'material_description' => $detail->material->material_description ?? '-',
                            'quantity' => $detail->quantity,
                            'satuan' => $detail->satuan,
                            'normalisasi' => $detail->normalisasi ?? '-',
                        ];
                    }),
                ]
            ]);
        }

        /** ========================= DELETE / DESTROY ========================= */
        public function destroy($id)
        {
            DB::beginTransaction();
            try {
                $materialMasuk = MaterialMasuk::with('details')->findOrFail($id);

                foreach ($materialMasuk->details as $detail) {
                    $material = Material::find($detail->material_id);
                    if ($material) {
                        $material->safeDecrement('qty', $detail->quantity);
                        $material->safeDecrement('unrestricted_use_stock', $detail->quantity);
                    }
                }

                MaterialHistory::whereIn('material_id', $materialMasuk->details->pluck('material_id'))
                ->where('tipe', 'MASUK')
                ->where('no_slip', $materialMasuk->nomor_doc ?? '-')
                ->delete();

                $materialMasuk->details()->delete();
                $materialMasuk->delete();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Material masuk berhasil dihapus.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus material masuk: ' . $e->getMessage()
                ], 500);
            }
        }



    /** ========================= AUTOCOMPLETE MATERIAL ========================= */
    public function autocompleteMaterial(Request $request)
{
    $query = $request->get('q');

    $materials = Material::where(function ($qBuilder) use ($query) {
        $qBuilder->whereRaw('LOWER(material_description) LIKE ?', ['%' . strtolower($query) . '%'])
                 ->orWhereRaw('LOWER(material_code) LIKE ?', ['%' . strtolower($query) . '%']);
    })
    ->select('id', 'material_description', 'material_code', 'normalisasi', 'base_unit_of_measure')
    ->limit(10)
    ->get();

    return response()->json($materials->map(function ($m) {
        return [
            'id' => $m->id,
            'text' => $m->material_description,
            'normalisasi' => $m->normalisasi ?? $m->material_code ?? '-',
            'satuan' => $m->base_unit_of_measure ?? '-',
        ];
    }));
}

public function autocomplete(Request $request)
{
    $query = $request->get('term') ?? $request->get('q');

    $materials = Material::where(function ($q) use ($query) {
        $q->whereRaw('LOWER(material_description) LIKE ?', ['%' . strtolower($query) . '%'])
          ->orWhereRaw('LOWER(material_code) LIKE ?', ['%' . strtolower($query) . '%']);
    })
    ->select('id', 'material_description', 'material_code', 'base_unit_of_measure')
    ->limit(15)
    ->get();

    return response()->json($materials->map(function ($m) {
        return [
            'id' => $m->id,
            'label' => $m->material_code . " - " . $m->material_description,
            'value' => $m->material_description, // ini yang tampil di textbox
            'kode' => $m->material_code,
            'satuan' => $m->base_unit_of_measure ?? '-'
        ];
    }));
}


    /** ========================= AUTOCOMPLETE NORMALISASI ========================= */
    public function autocompleteNormalisasi(Request $request)
    {
        $query = $request->get('q');

        $materials = Material::whereRaw('LOWER(material_code) LIKE ?', ['%' . strtolower($query) . '%'])
            ->select('id', 'material_description', 'material_code', 'base_unit_of_measure')
            ->limit(10)
            ->get();

        return response()->json($materials->map(function ($material) {
            return [
                'id' => $material->id,
                'text' => $material->material_code,
                'material_description' => $material->material_description,
                'normalisasi' => $material->material_code,
                'satuan' => $material->base_unit_of_measure
            ];
        }));
    }
}
