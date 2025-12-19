<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilTiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TempatController extends Controller
{
    public function index()
    {
        $list = HasilTiket::orderBy('ID_TEMPAT', 'desc')->get();
        return view('admin.tempat.index', compact('list'));
    }

    public function create()
    {
        return view('admin.tempat.create');
    }

public function store(Request $request)
{
    $data = $request->validate([
        'NAMA_TEMPAT' => 'required|string|max:100',
        'HARGA_PER_MALAM' => 'required|numeric|min:0',
        'STATUS' => 'required|string|max:20',
        'FOTO_TEMPAT' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $payload = [
        'NAMA_TEMPAT' => $data['NAMA_TEMPAT'],
        'HARGA_PER_MALAM' => $data['HARGA_PER_MALAM'],
        'STATUS' => $data['STATUS'],
    ];

    if ($request->hasFile('FOTO_TEMPAT')) {
        $path = $request->file('FOTO_TEMPAT')->store('tempat', 'public');
        $payload['FOTO_TEMPAT'] = $path;
    }

    HasilTiket::create($payload);

    return redirect('/admin/tempat')->with('ok', 'Tempat dibuat');
}

    public function edit($id)
    {
        $t = HasilTiket::findOrFail($id);
        return view('admin.tempat.edit', compact('t'));
    }

public function update(Request $request, $id)
{
    $t = HasilTiket::findOrFail($id);

    $data = $request->validate([
        'NAMA_TEMPAT' => 'required|string|max:100',
        'HARGA_PER_MALAM' => 'required|numeric|min:0',
        'STATUS' => 'required|string|max:20',
        'FOTO_TEMPAT' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $payload = [
        'NAMA_TEMPAT' => $data['NAMA_TEMPAT'],
        'HARGA_PER_MALAM' => $data['HARGA_PER_MALAM'],
        'STATUS' => $data['STATUS'],
    ];

    if ($request->hasFile('FOTO_TEMPAT')) {
        // hapus lama
        if ($t->FOTO_TEMPAT) {
            Storage::disk('public')->delete($t->FOTO_TEMPAT);
        }
        $path = $request->file('FOTO_TEMPAT')->store('tempat', 'public');
        $payload['FOTO_TEMPAT'] = $path;
    }

    $t->update($payload);

    return redirect('/admin/tempat')->with('ok', 'Tempat diupdate');
}
}
