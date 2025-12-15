<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $nik = $notification->data['nik_nasabah'] ?? '';
            // Redirect ke riwayat dengan search otomatis
            return redirect()->route('karyawan.pinjaman.riwayat', ['search' => $nik]);
        }
        return back();
    }
}