<?php

namespace App\Http\Controllers\DewanSkripsi;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DewanSkripsiController extends Controller
{
    /**
     * 1. Dasbor Progres Skripsi:
     * Menampilkan status tahap tugas akhir mahasiswa secara detail
     * (misal: penyusunan proposal, proses bimbingan aktif, siap sidang, atau revisi final).
     */
    public function dashboard(Request $request): View
    {
        $selectedTahap = $request->query('tahap', 'semua');
        $search = trim((string) $request->query('search', ''));

        // Query dasar dengan eager loading untuk mencegah N+1
        $query = Skripsi::with(['mahasiswa', 'pembimbing1', 'pembimbing2', 'penguji1', 'penguji2'])
            ->latest('updated_at');

        if ($selectedTahap !== 'semua' && ! empty($selectedTahap)) {
            $query->where('tahap', $selectedTahap);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('bidang_minat', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $skripsis = $query->paginate(6)->withQueryString();

        // Jadwal Sidang terpaginasi 6 untuk memenuhi pemantauan jadwal & paginasi konsisten
        $jadwalSidang = Skripsi::with(['mahasiswa', 'pembimbing1', 'penguji1', 'penguji2'])
            ->whereNotNull('jadwal_sidang')
            ->orderBy('jadwal_sidang')
            ->paginate(6, ['*'], 'page_jadwal')
            ->withQueryString();

        // Statistik Progres Skripsi
        $stats = [
            'total' => Skripsi::count(),
            'proposal' => Skripsi::where('tahap', 'Penyusunan Proposal')->count(),
            'bimbingan_aktif' => Skripsi::where('tahap', 'Proses Bimbingan Aktif')->count(),
            'siap_sidang' => Skripsi::where('tahap', 'Siap Sidang')->count(),
            'revisi_final' => Skripsi::where('tahap', 'Revisi Final')->count(),
            'lulus' => Skripsi::where('tahap', 'Lulus/Selesai')->count(),
            'pending_approval' => Skripsi::where('status_pendaftaran_sidang', 'Menunggu Persetujuan')->count(),
            'pending_verifikasi' => Skripsi::where('tahap', 'Revisi Final')->where('status_kelulusan', '!=', 'Lulus/Selesai')->count(),
        ];

        // Ambil data user dewan skripsi untuk header
        $dewanUser = User::where('role', 'dewan_skripsi')->first() ?? (object) [
            'name' => 'Dr. Ir. Taufik Hidayat, M.Kom.',
            'nip' => '197803152005011002',
            'role' => 'dewan_skripsi',
            'avatar_url' => asset('assets/media/avatars/300-1.jpg'),
        ];

        return view('dewan-skripsi.dashboard', [
            'skripsis' => $skripsis,
            'jadwal_sidang' => $jadwalSidang,
            'stats' => $stats,
            'selectedTahap' => $selectedTahap,
            'search' => $search,
            'dewanUser' => $dewanUser,
        ]);
    }

    /**
     * 2. Plotting Dosen & Jadwal:
     * Halaman penetapan (menjodohkan) mahasiswa dengan dosen pembimbing dan penguji,
     * serta membuat jadwal pelaksanaan sidang.
     */
    public function plotting(Request $request): View
    {
        $statusPlotting = $request->query('status_plotting', 'semua');
        $search = trim((string) $request->query('search', ''));

        $query = Skripsi::with(['mahasiswa', 'pembimbing1', 'pembimbing2', 'penguji1', 'penguji2'])
            ->latest('updated_at');

        if ($statusPlotting === 'belum_lengkap') {
            $query->where(function ($q) {
                $q->whereNull('pembimbing_1_id')
                    ->orWhereNull('pembimbing_2_id')
                    ->orWhereNull('penguji_1_id')
                    ->orWhereNull('penguji_2_id');
            });
        } elseif ($statusPlotting === 'lengkap') {
            $query->whereNotNull('pembimbing_1_id')
                ->whereNotNull('pembimbing_2_id')
                ->whereNotNull('penguji_1_id')
                ->whereNotNull('penguji_2_id');
        } elseif ($statusPlotting === 'belum_terjadwal') {
            $query->whereNull('jadwal_sidang');
        } elseif ($statusPlotting === 'terjadwal') {
            $query->whereNotNull('jadwal_sidang');
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $skripsis = $query->paginate(10)->withQueryString();

        // Daftar dosen untuk dropdown plotting
        $dosens = User::whereIn('role', ['dosen', 'dewan_skripsi'])
            ->orderBy('name')
            ->get();

        $dewanUser = User::where('role', 'dewan_skripsi')->first() ?? (object) [
            'name' => 'Dr. Ir. Taufik Hidayat, M.Kom.',
            'nip' => '197803152005011002',
            'role' => 'dewan_skripsi',
            'avatar_url' => asset('assets/media/avatars/300-1.jpg'),
        ];

        return view('dewan-skripsi.plotting', [
            'skripsis' => $skripsis,
            'dosens' => $dosens,
            'statusPlotting' => $statusPlotting,
            'search' => $search,
            'dewanUser' => $dewanUser,
        ]);
    }

    /**
     * Simpan pembaruan plotting dosen pembimbing/penguji dan jadwal sidang.
     */
    public function updatePlotting(Request $request, Skripsi $skripsi): RedirectResponse
    {
        $validated = $request->validate([
            'pembimbing_1_id' => ['nullable', 'exists:users,id'],
            'pembimbing_2_id' => ['nullable', 'exists:users,id'],
            'penguji_1_id' => ['nullable', 'exists:users,id'],
            'penguji_2_id' => ['nullable', 'exists:users,id'],
            'pembimbing_1_nama' => ['nullable', 'string', 'max:255'],
            'pembimbing_2_nama' => ['nullable', 'string', 'max:255'],
            'penguji_1_nama' => ['nullable', 'string', 'max:255'],
            'penguji_2_nama' => ['nullable', 'string', 'max:255'],
            'jenis_sidang' => ['nullable', 'string', 'max:100'],
            'jadwal_sidang' => ['nullable', 'date'],
            'ruangan_sidang' => ['nullable', 'string', 'max:255'],
            'metode_sidang' => ['nullable', 'string', 'max:100'],
            'status_sidang' => ['nullable', 'string', 'max:50'],
            'tahap' => ['nullable', 'string'],
        ]);

        // Resolusi nama dosen jika id dipilih
        if (! empty($validated['pembimbing_1_id'])) {
            $userP1 = User::find($validated['pembimbing_1_id']);
            $validated['pembimbing_1_nama'] = $userP1?->name ?? $validated['pembimbing_1_nama'];
        }
        if (! empty($validated['pembimbing_2_id'])) {
            $userP2 = User::find($validated['pembimbing_2_id']);
            $validated['pembimbing_2_nama'] = $userP2?->name ?? $validated['pembimbing_2_nama'];
        }
        if (! empty($validated['penguji_1_id'])) {
            $userU1 = User::find($validated['penguji_1_id']);
            $validated['penguji_1_nama'] = $userU1?->name ?? $validated['penguji_1_nama'];
        }
        if (! empty($validated['penguji_2_id'])) {
            $userU2 = User::find($validated['penguji_2_id']);
            $validated['penguji_2_nama'] = $userU2?->name ?? $validated['penguji_2_nama'];
        }

        // Jika jadwal diisi dan status sidang kosong, set ke Dijadwalkan
        if (! empty($validated['jadwal_sidang']) && empty($validated['status_sidang'])) {
            $validated['status_sidang'] = 'Dijadwalkan';
        }

        // Jika mahasiswa masih tahap proposal dan sudah diplot pembimbing, otomatis naikkan ke bimbingan aktif jika diminta
        if ($skripsi->tahap === 'Penyusunan Proposal' && ! empty($validated['pembimbing_1_id']) && empty($validated['tahap'])) {
            $validated['tahap'] = 'Proses Bimbingan Aktif';
        }

        $skripsi->update($validated);

        return redirect()->back()->with('success', 'Plotting dosen dan jadwal sidang untuk mahasiswa '.($skripsi->mahasiswa?->name ?? '').' berhasil diperbarui.');
    }

    /**
     * 3. Validasi Pendaftaran Sidang (Approval):
     * Fitur untuk meninjau, menyetujui (ACC), atau menolak pengajuan sidang mahasiswa
     * berdasarkan rekam jejak bimbingan dan persetujuan dari dosen pembimbing.
     */
    public function validasiSidang(Request $request): View
    {
        $filterStatus = $request->query('status_pendaftaran', 'semua');
        $search = trim((string) $request->query('search', ''));

        $query = Skripsi::with(['mahasiswa.bimbingans', 'pembimbing1', 'pembimbing2', 'penguji1', 'penguji2'])
            ->latest('updated_at');

        if ($filterStatus !== 'semua' && ! empty($filterStatus)) {
            $query->where('status_pendaftaran_sidang', $filterStatus);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $skripsis = $query->paginate(10)->withQueryString();

        $dosens = User::whereIn('role', ['dosen', 'dewan_skripsi'])
            ->orderBy('name')
            ->get();

        $dewanUser = User::where('role', 'dewan_skripsi')->first() ?? (object) [
            'name' => 'Dr. Ir. Taufik Hidayat, M.Kom.',
            'nip' => '197803152005011002',
            'role' => 'dewan_skripsi',
            'avatar_url' => asset('assets/media/avatars/300-1.jpg'),
        ];

        return view('dewan-skripsi.validasi-sidang', [
            'skripsis' => $skripsis,
            'dosens' => $dosens,
            'filterStatus' => $filterStatus,
            'search' => $search,
            'dewanUser' => $dewanUser,
        ]);
    }

    /**
     * Menyetujui (ACC) pendaftaran sidang mahasiswa.
     */
    public function approveSidang(Request $request, Skripsi $skripsi): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_validasi_sidang' => ['nullable', 'string', 'max:1000'],
            'jadwal_sidang' => ['nullable', 'date'],
            'ruangan_sidang' => ['nullable', 'string', 'max:255'],
            'jenis_sidang' => ['nullable', 'string', 'max:100'],
        ]);

        $updateData = [
            'status_pendaftaran_sidang' => 'Disetujui',
            'tahap' => 'Siap Sidang',
            'catatan_validasi_sidang' => $validated['catatan_validasi_sidang'] ?? 'Pendaftaran sidang telah diverifikasi dan disetujui oleh Dewan Skripsi.',
            'tanggal_validasi_sidang' => Carbon::now(),
        ];

        if (! empty($validated['jadwal_sidang'])) {
            $updateData['jadwal_sidang'] = $validated['jadwal_sidang'];
            $updateData['status_sidang'] = 'Dijadwalkan';
        }
        if (! empty($validated['ruangan_sidang'])) {
            $updateData['ruangan_sidang'] = $validated['ruangan_sidang'];
        }
        if (! empty($validated['jenis_sidang'])) {
            $updateData['jenis_sidang'] = $validated['jenis_sidang'];
        }

        $skripsi->update($updateData);

        return redirect()->back()->with('success', 'Pendaftaran sidang mahasiswa '.($skripsi->mahasiswa?->name ?? '').' BERHASIL DISETUJUI (ACC).');
    }

    /**
     * Menolak pengajuan sidang mahasiswa dengan catatan alasan penolakan.
     */
    public function rejectSidang(Request $request, Skripsi $skripsi): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_validasi_sidang' => ['required', 'string', 'max:1000'],
        ], [
            'catatan_validasi_sidang.required' => 'Wajib memberikan catatan/alasan penolakan pengajuan sidang.',
        ]);

        $skripsi->update([
            'status_pendaftaran_sidang' => 'Ditolak',
            'catatan_validasi_sidang' => $validated['catatan_validasi_sidang'],
            'tanggal_validasi_sidang' => Carbon::now(),
        ]);

        return redirect()->back()->with('warning', 'Pengajuan sidang mahasiswa '.($skripsi->mahasiswa?->name ?? '').' telah ditolak dengan catatan.');
    }

    /**
     * 4. Verifikasi Berkas Pasca-Sidang:
     * Ruang untuk mengelola dokumen revisi final, menginput rekap nilai sidang,
     * dan mengeluarkan status "Lulus/Selesai" sebelum mahasiswa mendaftar yudisium.
     */
    public function verifikasiPascaSidang(Request $request): View
    {
        $filterStatus = $request->query('status_kelulusan', 'semua');
        $search = trim((string) $request->query('search', ''));

        // Mahasiswa yang berada di tahap Pasca-Sidang (Revisi Final atau Lulus/Selesai)
        $query = Skripsi::with(['mahasiswa', 'pembimbing1', 'pembimbing2', 'penguji1', 'penguji2'])
            ->whereIn('tahap', ['Revisi Final', 'Lulus/Selesai', 'Siap Sidang'])
            ->latest('updated_at');

        if ($filterStatus === 'belum_lulus') {
            $query->where('status_kelulusan', '!=', 'Lulus/Selesai');
        } elseif ($filterStatus === 'lulus') {
            $query->where('status_kelulusan', 'Lulus/Selesai');
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $skripsis = $query->paginate(10)->withQueryString();

        $dewanUser = User::where('role', 'dewan_skripsi')->first() ?? (object) [
            'name' => 'Dr. Ir. Taufik Hidayat, M.Kom.',
            'nip' => '197803152005011002',
            'role' => 'dewan_skripsi',
            'avatar_url' => asset('assets/media/avatars/300-1.jpg'),
        ];

        return view('dewan-skripsi.verifikasi-pasca-sidang', [
            'skripsis' => $skripsis,
            'filterStatus' => $filterStatus,
            'search' => $search,
            'dewanUser' => $dewanUser,
        ]);
    }

    /**
     * Input rekap nilai sidang (nilai pembimbing dan penguji).
     */
    public function simpanNilai(Request $request, Skripsi $skripsi): RedirectResponse
    {
        $validated = $request->validate([
            'nilai_pembimbing_1' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_pembimbing_2' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_penguji_1' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_penguji_2' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'catatan_dewan' => ['nullable', 'string', 'max:1000'],
        ], [
            'nilai_pembimbing_1.required' => 'Nilai Pembimbing 1 wajib diisi.',
            'nilai_penguji_1.required' => 'Nilai Penguji 1 wajib diisi.',
            'nilai_pembimbing_1.numeric' => 'Nilai harus berupa angka antara 0-100.',
            'nilai_penguji_1.numeric' => 'Nilai harus berupa angka antara 0-100.',
        ]);

        $calculated = $skripsi->hitungNilaiDanHuruf(
            (float) $validated['nilai_pembimbing_1'],
            isset($validated['nilai_pembimbing_2']) ? (float) $validated['nilai_pembimbing_2'] : null,
            (float) $validated['nilai_penguji_1'],
            isset($validated['nilai_penguji_2']) ? (float) $validated['nilai_penguji_2'] : null,
        );

        $skripsi->update(array_merge($validated, [
            'nilai_akhir' => $calculated['nilai_akhir'],
            'nilai_huruf' => $calculated['nilai_huruf'],
            'status_sidang' => 'Selesai',
        ]));

        return redirect()->back()->with('success', "Rekap nilai sidang berhasil disimpan. Nilai Akhir: {$calculated['nilai_akhir']} (Nilai Huruf: {$calculated['nilai_huruf']}).");
    }

    /**
     * Mengeluarkan status resmi "Lulus/Selesai" sebelum mahasiswa mendaftar yudisium.
     */
    public function keluarkanStatusLulus(Request $request, Skripsi $skripsi): RedirectResponse
    {
        $request->validate([
            'catatan_dewan' => ['nullable', 'string', 'max:1000'],
            'no_surat_keterangan' => ['nullable', 'string', 'max:100'],
        ]);

        $nomorSurat = $request->input('no_surat_keterangan')
            ?: 'SKL/FT-USK/SKR/'.date('Y').'/'.str_pad((string) $skripsi->id, 3, '0', STR_PAD_LEFT);

        $skripsi->update([
            'status_kelulusan' => 'Lulus/Selesai',
            'tahap' => 'Lulus/Selesai',
            'status_sidang' => 'Selesai',
            'tanggal_lulus' => Carbon::now()->toDateString(),
            'no_surat_keterangan' => $nomorSurat,
            'catatan_dewan' => $request->input('catatan_dewan', 'Mahasiswa dinyatakan telah menyelesaikan seluruh rangkaian ujian dan revisi skripsi dengan baik serta dinyatakan Lulus/Selesai untuk mendaftar yudisium.'),
        ]);

        return redirect()->back()->with('success', "Status LULUS/SELESAI resmi dikeluarkan untuk mahasiswa {$skripsi->mahasiswa?->name}. Nomor Surat: {$nomorSurat}.");
    }

    /**
     * Tampilkan Surat Keterangan Lulus Sidang Skripsi (SKL / Bukti Bebas Skripsi) siap cetak.
     */
    public function cetakSuratKeterangan(Skripsi $skripsi): View
    {
        $skripsi->load(['mahasiswa', 'pembimbing1', 'pembimbing2', 'penguji1', 'penguji2']);

        $dewanUser = User::where('role', 'dewan_skripsi')->first() ?? (object) [
            'name' => 'Dr. Ir. Taufik Hidayat, M.Kom.',
            'nip' => '197803152005011002',
            'role' => 'dewan_skripsi',
        ];

        return view('dewan-skripsi.surat-keterangan', [
            'skripsi' => $skripsi,
            'dewanUser' => $dewanUser,
        ]);
    }
}
