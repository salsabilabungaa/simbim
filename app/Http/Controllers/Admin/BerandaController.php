<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        try {
            $totalPengguna = User::count();
            $totalMahasiswa = User::where('role', 'mahasiswa')->count();
            $totalDosen = User::where('role', 'dosen')->count();
            $totalDewanSkripsi = User::where('role', 'dewan_skripsi')->count();
            $totalGkm = User::where('role', 'gkm')->count();
            $totalAdmin = User::where('role', 'admin')->count();
            $penggunaTerbaru = User::latest()->take(6)->get();
        } catch (\Throwable) {
            $totalPengguna = 0;
            $totalMahasiswa = 0;
            $totalDosen = 0;
            $totalDewanSkripsi = 0;
            $totalGkm = 0;
            $totalAdmin = 0;
            $penggunaTerbaru = collect();
        }

        $ringkasanPeran = [
            'mahasiswa' => [
                'total' => $totalMahasiswa,
                'label' => 'Mahasiswa',
                'deskripsi' => 'Pengguna bimbingan akademik & skripsi',
                'badge' => 'primary',
                'icon' => 'fas fa-user-graduate',
                'url_monitoring' => route('admin.monitoring.mahasiswa'),
            ],
            'dosen' => [
                'total' => $totalDosen,
                'label' => 'Dosen Pembimbing & Wali',
                'deskripsi' => 'Pengampu bimbingan & verifikator bab',
                'badge' => 'success',
                'icon' => 'fas fa-chalkboard-teacher',
                'url_monitoring' => route('admin.monitoring.dosen'),
            ],
            'dewan_skripsi' => [
                'total' => $totalDewanSkripsi,
                'label' => 'Dewan Skripsi',
                'deskripsi' => 'Penetapan sidang, sempro & tim penguji',
                'badge' => 'warning',
                'icon' => 'fas fa-balance-scale',
                'url_monitoring' => route('admin.monitoring.dewan-skripsi'),
            ],
            'gkm' => [
                'total' => $totalGkm,
                'label' => 'Gugus Kendali Mutu (GKM)',
                'deskripsi' => 'Audit standar & kepatuhan sesi bimbingan',
                'badge' => 'info',
                'icon' => 'fas fa-shield-alt',
                'url_monitoring' => route('admin.monitoring.gkm'),
            ],
        ];

        return view('admin.beranda', compact(
            'totalPengguna',
            'totalMahasiswa',
            'totalDosen',
            'totalDewanSkripsi',
            'totalGkm',
            'totalAdmin',
            'penggunaTerbaru',
            'ringkasanPeran'
        ));
    }
}
