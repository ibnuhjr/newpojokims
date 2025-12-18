<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class BeritaAcaraController extends Controller
{
    // LIST / TABLE
    public function index()
    {
        $beritaAcaras = BeritaAcara::all();
        return view('material.berita-acara', compact('beritaAcaras'));
    }

    // FORM CREATE
    public function create()
    {
        return view('material.berita-acara-create');
    }

    // STORE DATA
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => 'required',
            'tanggal' => 'required|date',
            'tanggal_teks' => 'required',
            'mengetahui' => 'required',
            'jabatan_mengetahui' => 'required',
            'pembuat' => 'required',
            'jabatan_pembuat' => 'required',
        ]);

        BeritaAcara::create($validated);

        return redirect()
            ->route('berita-acara.index')
            ->with('success', 'Berita Acara berhasil dibuat!');
    }

    // SHOW SURAT
    public function show($id)
    {
        $ba = BeritaAcara::findOrFail($id);
        return view('material.berita-acara-show', compact('ba'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $ba = BeritaAcara::findOrFail($id);
        return view('material.berita-acara-edit', compact('ba'));
    }

    // UPDATE DATA
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'hari' => 'required',
            'tanggal' => 'required|date',
            'tanggal_teks' => 'required',
            'mengetahui' => 'required',
            'jabatan_mengetahui' => 'required',
            'pembuat' => 'required',
            'jabatan_pembuat' => 'required',
        ]);

        $ba = BeritaAcara::findOrFail($id);
        $ba->update($validated);

        return redirect()
            ->route('berita-acara.index')
            ->with('success', 'Berita Acara berhasil diperbarui!');
    }

    // DELETE
    public function destroy($id)
    {
        $ba = BeritaAcara::findOrFail($id);
        $ba->delete();

        return redirect()
            ->route('berita-acara.index')
            ->with('success', 'Berita Acara berhasil dihapus!');
    }

    // PDF
    public function pdf($id)
{
    $ba = BeritaAcara::findOrFail($id);

    $pdf = Pdf::loadView('exports.berita-acara-pdf', compact('ba'))
                ->setPaper('A4', 'portrait');

    return $pdf->stream('Berita Acara '.$ba->id.'.pdf');
}
}
