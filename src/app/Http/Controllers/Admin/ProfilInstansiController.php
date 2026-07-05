<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilInstansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilInstansiController extends Controller
{
    public function edit()
    {
        $profil = ProfilInstansi::getInstance();
        return view('admin.pengaturan.profil-instansi', compact('profil'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_instansi'  => ['required', 'string', 'max:255'],
            'nama_kabupaten' => ['required', 'string', 'max:100'],
            'nama_provinsi'  => ['required', 'string', 'max:100'],
            'alamat'         => ['nullable', 'string'],
            'telepon'        => ['nullable', 'string', 'max:30'],
            'email'          => ['nullable', 'email'],
            'logo'           => ['nullable', 'image', 'max:2048'],
        ]);

        $profil = ProfilInstansi::getInstance();

        if ($request->hasFile('logo')) {
            if ($profil->logo_path && Storage::disk('public')->exists($profil->logo_path)) {
                Storage::disk('public')->delete($profil->logo_path);
            }
            $profil->logo_path = $request->file('logo')->store('instansi', 'public');
        }

        $profil->fill($request->except(['logo', '_token', '_method']));
        $profil->save();

        return back()->with('success', 'Profil instansi berhasil diperbarui.');
    }
}
