<?php
namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\TindakLanjut;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Auth;

class TindakLanjutController extends Controller
{
    public function index()
    {
        $tindakLanjuts = TindakLanjut::with(['pengaduan.terlapor'])
            ->whereHas('pengaduan', fn($q) => $q->where('assigned_to', Auth::id()))
            ->latest()->paginate(15);

        return view('pengawas.tindak-lanjut.index', compact('tindakLanjuts'));
    }
}
