<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = User::where('role', 'karyawan')->latest()->paginate(10);
        return view('karyawans.index', compact('karyawans'));
    }

    public function create()
    {
        return view('karyawans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'karyawan', // <-- Peran di-set sebagai 'karyawan'
        ]);

        return redirect()->route('karyawans.index')->with('success', 'Karyawan baru berhasil ditambahkan.');
    }

    public function edit(User $karyawan)
    {
        return view('karyawans.edit', compact('karyawan'));
    }

    public function update(Request $request, User $karyawan)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$karyawan->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $karyawan->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $karyawan->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('karyawans.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawans.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}