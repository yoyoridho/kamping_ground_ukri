<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function index()
    {
        $list = Fasilitas::orderByDesc('ID_FASILITAS')->get();
        return view('admin.fasilitas.index', compact('list'));
    }

    public function create()
    {
        return view('admin.fasilitas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'NAMA_FASILITAS' => 'required|string|max:120',
            'HARGA_FASILITAS' => 'required|integer|min:0',
            'STATUS' => 'required|in:AKTIF,NONAKTIF',
            'DESKRIPSI' => 'nullable|string|max:255',
        ]);

        Fasilitas::create($data);

        return redirect('/admin/fasilitas')->with('ok', 'Fasilitas berhasil ditambahkan');
    }

    public function edit($id)
    {
        $f = Fasilitas::findOrFail($id);
        return view('admin.fasilitas.edit', compact('f'));
    }

    public function update(Request $request, $id)
    {
        $f = Fasilitas::findOrFail($id);

        $data = $request->validate([
            'NAMA_FASILITAS' => 'required|string|max:120',
            'HARGA_FASILITAS' => 'required|integer|min:0',
            'STATUS' => 'required|in:AKTIF,NONAKTIF',
            'DESKRIPSI' => 'nullable|string|max:255',
        ]);

        $f->update($data);

        return redirect('/admin/fasilitas')->with('ok', 'Fasilitas berhasil diupdate');
    }

    public function destroy($id)
    {
        $f = Fasilitas::findOrFail($id);
        $f->delete();

        return redirect('/admin/fasilitas')->with('ok', 'Fasilitas berhasil dihapus');
    }
}
