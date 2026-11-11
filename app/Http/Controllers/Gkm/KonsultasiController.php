<?php

namespace App\Http\Controllers\Gkm;

use App\Http\Controllers\Controller;
use App\Models\KonsultasiAkademik;
use App\Services\GkmAccreditationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class KonsultasiController extends Controller
{
    public function __construct(
        protected GkmAccreditationService $accreditationService
    ) {}

    /**
     * Tampilkan riwayat dan log konsultasi bimbingan akademik Prodi PSTI UPI (Strictly View Only).
     */
    public function index(Request $request): View
    {
        $filterOptions = $this->accreditationService->getAvailableFilters();

        $filters = [
            'tahun_akademik' => $request->filled('tahun_akademik') ? (string) $request->input('tahun_akademik') : null,
            'angkatan' => $request->filled('angkatan') ? (string) $request->input('angkatan') : null,
            'status_sesi' => $request->filled('status_sesi') ? (string) $request->input('status_sesi') : null,
            'dosen_wali_id' => $request->filled('dosen_wali_id') ? (string) $request->input('dosen_wali_id') : null,
            'tanggal_dari' => $request->filled('tanggal_dari') ? (string) $request->input('tanggal_dari') : null,
            'tanggal_sampai' => $request->filled('tanggal_sampai') ? (string) $request->input('tanggal_sampai') : null,
            'search' => $request->filled('search') ? trim((string) $request->input('search')) : null,
        ];

        $statistik = $this->accreditationService->getKonsultasiSummary($filters);
        $daftarKonsultasi = $this->accreditationService->getKonsultasiQuery($filters)
            ->paginate(6)
            ->withQueryString();

        return view('gkm.riwayat_konsultasi', [
            'daftarKonsultasi' => $daftarKonsultasi,
            'statistik' => $statistik,
            'daftarAngkatan' => $filterOptions['daftarAngkatan'],
            'daftarTahunAkademik' => $filterOptions['daftarTahunAkademik'],
            'daftarDosenWali' => $filterOptions['daftarDosenWali'],
            'filters' => $filters,
        ]);
    }

    /**
     * Tampilkan detail satu sesi log konsultasi akademik dalam mode View Only.
     */
    public function show(KonsultasiAkademik $konsultasi): View
    {
        $konsultasi->load([
            'mahasiswa:id,name,email,nomor_induk,program_studi',
            'dosenWali:id,name,email,nomor_induk,program_studi',
            'perwalianMahasiswa',
        ]);

        return view('gkm.riwayat_konsultasi_show', [
            'konsultasi' => $konsultasi,
        ]);
    }
}
