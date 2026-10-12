<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManajemenAkunController extends Controller
{
    /**
     * Menampilkan daftar user (Perawat) dengan search, filter & pagination
     */
    public function index(Request $request)
    {
        // Ambil query parameter
        $search = $request->input('search');
        $status = $request->input('status_akun');
        $role   = $request->input('role');

        // Base query: selain admin
        $query = User::where('role', '!=', 'admin');

        // Filter search: name / email / nik (via relasi profile)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($sub) use ($search) {
                        $sub->where('nik', 'like', "%{$search}%");
                    });
            });
        }

        // Filter status akun
        if (!empty($status) && in_array($status, ['active', 'rejected', 'pending'])) {
            $query->where('status_akun', $status);
        }

        // Filter role
        if (!empty($role)) {
            $query->where('role', $role);
        }

        // Urutkan & paginate
        $users = $query->orderBy('created_at', 'desc')
            ->paginate(10)            // jumlah per halaman
            ->withQueryString();     // pertahankan query di URL

        // Ambil daftar role unik (untuk dropdown filter)
        $roles = User::where('role', '!=', 'admin')
            ->select('role')
            ->distinct()
            ->pluck('role');

        return view('admin.manajemen_akun.index', compact('users', 'roles'));
    }

    /**
     * Proses Approve atau Reject
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_akun' => 'required|in:active,rejected,pending'
        ]);

        if ((int) $id === (int) auth()->id()) {
            return redirect()->back()->with('swal', ['icon' => 'warning', 'title' => 'Tidak Diizinkan', 'text' => 'Anda tidak bisa mengubah atau menghapus akun Anda sendiri dari sini.']);
        }

        $user = User::findOrFail($id);
        $user->status_akun = $request->status_akun;
        $user->save();

        $pesan = '';
        if ($request->status_akun == 'active') {
            $pesan = 'Akun berhasil disetujui (Approved).';
        } elseif ($request->status_akun == 'rejected') {
            $pesan = 'Akun berhasil ditolak (Rejected).';
        } else {
            $pesan = 'Status akun diubah menjadi Pending.';
        }

        return redirect()->back()->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => $pesan,
        ]);
    }

    /**
     * Hapus user jika diperlukan (Opsional)
     */
    public function destroy($id)
    {
        if ((int) $id === (int) auth()->id()) {
            return redirect()->back()->with('swal', ['icon' => 'warning', 'title' => 'Tidak Diizinkan', 'text' => 'Anda tidak bisa mengubah atau menghapus akun Anda sendiri dari sini.']);
        }

        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('swal', [
            'icon' => 'success',
            'title' => 'Dihapus',
            'text' => 'Data akun pengguna berhasil dihapus.',
        ]);
    }
}
