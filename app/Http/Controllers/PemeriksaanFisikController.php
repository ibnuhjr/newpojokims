<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\MaterialHistory;

class PemeriksaanFisikController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan;

        if (!$bulan) {
            return view('material.pemeriksaan-fisik', [
                'materials' => null,
                'bulan'     => null,
                'tahun'     => null,
            ]);
        }

        [$tahun, $bulanAngka] = explode('-', $bulan);

        $materials = Material::orderBy('material_code')->get();

        foreach ($materials as $m) {
            $m->stock_realtime = MaterialHistory::stokBulanan($m->id, $bulanAngka, $tahun);
        }

        return view('material.pemeriksaan-fisik', compact('materials', 'bulan', 'tahun'));
    }
}
