<?php


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fasilitas;

class AdminFasilitasController extends Controller
{
    public function index()
    {
        $items = Fasilitas::orderBy('NAMA_FASILITAS')->get();
        return view('admin.fasilitas.index', compact('items'));
    }

    public function updateStok(Request $request, $id)
    {
        $request->validate([
            'STOK' => 'required|integer|min:0'
        ]);

        Fasilitas::where('ID_FASILITAS', $id)
            ->update(['STOK' => $request->STOK]);

        return back()->with('ok', 'Stok diperbarui');
    }
}

    //
