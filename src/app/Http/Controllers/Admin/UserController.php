<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%$s%")->orWhere('email','like',"%$s%"));
        }

        if ($request->filled('role')) $query->where('role',$request->role);

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','unique:users'],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()],
            'role'     => ['required','in:admin,pengawas,viewer'],
            'nip'      => ['nullable','string','max:30'],
            'jabatan'  => ['nullable','string','max:100'],
            'no_telp'  => ['nullable','string','max:20'],
            'is_active'=> ['boolean'],
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'nip'       => $request->nip,
            'jabatan'   => $request->jabatan,
            'no_telp'   => $request->no_telp,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success','User baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','unique:users,email,'.$user->id],
            'password' => ['nullable', Password::min(8)->letters()->mixedCase()->numbers()],
            'role'     => ['required','in:admin,pengawas,viewer'],
            'nip'      => ['nullable','string','max:30'],
            'jabatan'  => ['nullable','string','max:100'],
            'no_telp'  => ['nullable','string','max:20'],
        ]);

        $data = $request->only('name','email','role','nip','jabatan','no_telp');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success','Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error','Tidak dapat menghapus akun yang sedang digunakan.');
        }

        $user->delete();

        return back()->with('success','User berhasil dihapus.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error','Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $msg = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success',"User {$user->name} berhasil $msg.");
    }
}