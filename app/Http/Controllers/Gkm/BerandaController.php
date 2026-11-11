<?php

namespace App\Http\Controllers\Gkm;

use App\Http\Controllers\Controller;
use App\Services\GkmAccreditationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function __construct(
        protected GkmAccreditationService $accreditationService
    ) {}

    /**
     * Dasbor Rekap Perwalian GKM Prodi PSTI — Universitas Pendidikan Indonesia (UPI).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filterOptions = $this->accreditationService->getAvailableFilters();

        $defaultTahun = $filterOptions['daftarTahunAkademik'][0] ?? 'Ganjil 2026/2027';
        $rawTahun = $request->input('tahun_akademik');
        $tahunAkademik = $request->has('tahun_akademik')
            ? (filled($rawTahun) ? (string) $rawTahun : null)
            : $defaultTahun;

        $angkatan = $request->filled('angkatan') ? (string) $request->input('angkatan') : null;
        $statusPerwalian = $request->filled('status_perwalian') ? (string) $request->input('status_perwalian') : null;
        $search = $request->filled('search') ? trim((string) $request->input('search')) : null;

        $gkm = [
            'nama' => $user?->name ?? 'Dr. Ratna Wijaya, S.T., M.T.',
            'nip' => $user?->nomor_induk ?? '198204122008122003',
            'jabatan' => 'Ketua Gugus Kendali Mutu (GKM)',
            'program_studi' => config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)'),
            'universitas' => config('simbim.universitas', 'Universitas Pendidikan Indonesia (UPI)'),
        ];

        $summary = $this->accreditationService->getDashboardSummary([
            'tahun_akademik' => $tahunAkademik,
            'angkatan' => $angkatan,
        ]);

        $daftarPerwalian = $this->accreditationService->getPerwalianQuery([
            'tahun_akademik' => $tahunAkademik,
            'angkatan' => $angkatan,
            'status_perwalian' => $statusPerwalian,
            'search' => $search,
        ])->paginate(6)->withQueryString();

        return view('gkm.beranda', [
            'gkm' => $gkm,
            'summary' => $summary,
            'daftarPerwalian' => $daftarPerwalian,
            'daftarAngkatan' => $filterOptions['daftarAngkatan'],
            'daftarTahunAkademik' => $filterOptions['daftarTahunAkademik'],
            'selectedTahunAkademik' => $tahunAkademik,
            'selectedAngkatan' => $angkatan,
            'selectedStatus' => $statusPerwalian,
            'search' => $search,
        ]);
    }
}
