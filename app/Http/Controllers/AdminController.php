<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    /**
     * Menampilkan daftar semua admin. (READ)
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->latest()->paginate(10);
        return view('admins.index', compact('admins'));
    }

    /**
     * Menampilkan form untuk membuat admin baru. (CREATE form)
     */
    public function create()
    {
        return view('admins.create');
    }

    /**
     * Menyimpan admin baru ke database. (CREATE process)
     */
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
            'role' => 'admin', // Otomatis set peran sebagai 'admin'
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data admin. (UPDATE form)
     */
    public function edit(User $admin)
    {
        return view('admins.edit', compact('admin'));
    }

    /**
     * Memperbarui data admin di database. (UPDATE process)
     */
    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$admin->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $admin->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admins.index')->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * Menghapus data admin dari database. (DELETE)
     */
    public function destroy(User $admin)
    {
        $admin->delete();
        return redirect()->route('admins.index')->with('success', 'Data admin berhasil dihapus.');
    }
}