<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function edit()
    {
        return view('pengawas.profil.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'no_telp'      => ['nullable', 'string', 'max:20'],
            'jabatan'      => ['nullable', 'string', 'max:100'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'old_password' => ['required_with:password'],
        ]);

        $user = Auth::user();

        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'Password lama tidak sesuai.']);
            }
        }

        $data = $request->only('name', 'no_telp', 'jabatan');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
