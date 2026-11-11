<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\SkripsiDokumen;
use App\Models\User;
use App\Services\SkripsiRevisiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BimbinganSkripsiController extends BaseMahasiswaController
{
    public function __construct(
        protected SkripsiRevisiService $revisiService
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $context = $this->resolveMahasiswaContext($user);
        $mahasiswa = $context['mahasiswa'];
        $dokumenModel = $context['dokumen'];
        $isPersistedStudent = $context['is_persisted_student'];

        if ($dokumenModel !== null) {
            $workspace = $this->revisiService->serializeWorkspace($dokumenModel);
            $dosenPembimbing = $this->resolveDosenPembimbing(true, $dokumenModel, $isPersistedStudent);

            $mahasiswa['judul_skripsi'] = $dokumenModel->judul_skripsi;
            $reviewDokumen = [
                'dokumen_id' => $dokumenModel->id,
                'nama_file' => $workspace['dokumen']['nama_file'],
                'versi' => $workspace['dokumen']['versi'],
                'status_draf' => $workspace['dokumen']['status_draf'],
                'badge_status' => $workspace['dokumen']['badge_status'],
                'tanggal_upload' => $workspace['dokumen']['updated_label'],
                'has_uploaded_file' => $workspace['dokumen']['has_uploaded_file'],
                'is_pdf' => $workspace['dokumen']['is_pdf'],
                'sections' => $workspace['sections'],
                'konten_naskah' => $workspace['dokumen']['konten_naskah'],
                'catatan_review' => array_map(fn (array $kom): array => [
                    'id' => $kom['id'],
                    'halaman' => $kom['lokasi_dokumen'],
                    'kutipan' => $kom['kutipan_teks'],
                    'quote_in_doc' => $kom['quote_in_doc'] ?? false,
                    'catatan' => $kom['isi_komentar'],
                    'status' => $kom['status'],
                    'badge' => $kom['badge'],
                    'pengirim' => $kom['pengirim'],
                    'role_label' => $kom['role_label'],
                    'waktu' => $kom['waktu'],
                    'is_resolved' => $kom['is_resolved'],
                    'balasan' => $kom['balasan'],
                ], $workspace['komentars']),
            ];

            $diskusiSkripsi = $workspace['diskusi'];
            $totalSesi = $isPersistedStudent ? (int) $dokumenModel->jumlah_sesi : max(12, (int) $dokumenModel->jumlah_sesi);
            [$sesiLabel, $sesiBadge] = match (true) {
                $totalSesi >= 8 => ['Memenuhi Syarat Sidang', 'success'],
                $totalSesi > 0 => ['Bimbingan Berjalan', 'primary'],
                default => ['Belum Ada Sesi', 'secondary'],
            };

            $ringkasan = [
                'total_sesi' => $totalSesi,
                'persen_progres' => (int) $dokumenModel->progres_persen,
                'bab_aktif' => $dokumenModel->bab_aktif,
                'target_selesai' => 'Desember 2026',
                'status_sesi_label' => $sesiLabel,
                'status_sesi_badge' => $sesiBadge,
            ];
            $activeDokumenId = $dokumenModel->id;
        } elseif ($isPersistedStudent) {
            $dosenPembimbing = $this->resolveDosenPembimbing(true, null, true);
            $mahasiswa['judul_skripsi'] = 'Belum ada judul skripsi terdaftar';
            $reviewDokumen = [
                'dokumen_id' => null,
                'nama_file' => 'Belum ada draf skripsi diunggah',
                'versi' => '-',
                'status_draf' => 'Belum Ada Draf',
                'badge_status' => 'secondary',
                'tanggal_upload' => '-',
                'has_uploaded_file' => false,
                'is_pdf' => false,
                'sections' => [],
                'konten_naskah' => '',
                'catatan_review' => [],
            ];
            $diskusiSkripsi = [];
            $ringkasan = [
                'total_sesi' => 0,
                'persen_progres' => 0,
                'bab_aktif' => 'Belum Ada Draf',
                'target_selesai' => 'Belum Ditentukan',
                'status_sesi_label' => 'Belum Memulai Bimbingan',
                'status_sesi_badge' => 'secondary',
            ];
            $activeDokumenId = null;
        } else {
            $dosenPembimbing = $this->resolveDosenPembimbing(true);
            $mahasiswa['judul_skripsi'] = 'Rancang Bangun Sistem Informasi Bimbingan Tugas Akhir Berbasis Web Menggunakan Arsitektur Microservices';
            $reviewDokumen = [
                'dokumen_id' => null,
                'nama_file' => 'Draft_BAB3_Diagram_v1.2.pdf',
                'versi' => 'v1.2',
                'status_draf' => 'Menunggu Review Dosen',
                'badge_status' => 'warning',
                'tanggal_upload' => '09 Okt 2026, 14.30 WIB',
                'has_uploaded_file' => false,
                'is_pdf' => false,
                'sections' => [],
                'konten_naskah' => '',
                'catatan_review' => [
                    [
                        'id' => null,
                        'halaman' => 'Halaman 12 (Paragraf 2)',
                        'kutipan' => null,
                        'catatan' => 'Tolong tambahkan diagram alur pengujian performa sistem dan latency microservices.',
                        'status' => 'Perlu Revisi',
                        'badge' => 'danger',
                        'pengirim' => $dosenPembimbing['nama'],
                        'role_label' => 'Dosen Pembimbing',
                        'waktu' => '09 Okt 2026, 14:40 WIB',
                        'is_resolved' => false,
                        'balasan' => [],
                    ],
                    [
                        'id' => null,
                        'halaman' => 'Halaman 18 (Diagram 3.2)',
                        'kutipan' => null,
                        'catatan' => 'Class diagram sudah sesuai dengan kebutuhan ERD data.',
                        'status' => 'Disetujui',
                        'badge' => 'success',
                        'pengirim' => $dosenPembimbing['nama'],
                        'role_label' => 'Dosen Pembimbing',
                        'waktu' => '09 Okt 2026, 14:45 WIB',
                        'is_resolved' => true,
                        'balasan' => [],
                    ],
                    [
                        'id' => null,
                        'halaman' => 'Halaman 24 (Daftar Pustaka)',
                        'kutipan' => null,
                        'catatan' => 'Format sitasi gunakan standar IEEE.',
                        'status' => 'Perlu Revisi',
                        'badge' => 'warning',
                        'pengirim' => $dosenPembimbing['nama'],
                        'role_label' => 'Dosen Pembimbing',
                        'waktu' => '09 Okt 2026, 14:50 WIB',
                        'is_resolved' => false,
                        'balasan' => [],
                    ],
                ],
            ];

            $diskusiSkripsi = [
                [
                    'id' => null,
                    'pengirim' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'peran_pengirim' => 'dosen',
                    'role' => 'Dosen Pembimbing',
                    'badge_role' => 'primary',
                    'waktu' => 'Kemarin, 16.20 WIB',
                    'pesan' => 'Draft BAB III sudah saya baca. Ada beberapa poin diagram arsitektur yang perlu disesuaikan sebelum bimbingan besok.',
                ],
                [
                    'id' => null,
                    'pengirim' => $mahasiswa['nama'],
                    'peran_pengirim' => 'mahasiswa',
                    'role' => 'Mahasiswa',
                    'badge_role' => 'info',
                    'waktu' => 'Kemarin, 17.05 WIB',
                    'pesan' => 'Baik Pak, untuk diagram class diagram apakah perlu ditambahkan relasi ke modul authentication?',
                ],
                [
                    'id' => null,
                    'pengirim' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'peran_pengirim' => 'dosen',
                    'role' => 'Dosen Pembimbing',
                    'badge_role' => 'primary',
                    'waktu' => 'Hari Ini, 08.15 WIB',
                    'pesan' => 'Ya betul, tambahkan relasi token JWT di modul authentication agar arsitektur microservices-nya lengkap.',
                ],
            ];

            $ringkasan = [
                'total_sesi' => 12,
                'persen_progres' => 65,
                'bab_aktif' => 'BAB III (Metodologi Penelitian)',
                'target_selesai' => 'Desember 2026',
                'status_sesi_label' => 'Memenuhi Syarat Sidang',
                'status_sesi_badge' => 'success',
            ];
            $activeDokumenId = null;
        }

        if ($isPersistedStudent) {
            if ($dokumenModel !== null) {
                [$rowStatus, $rowBadge] = match ($dokumenModel->status_draf) {
                    'Siap Sidang Skripsi' => ['Selesai', 'success'],
                    'Draf Sedang Direview', 'Bimbingan Terjadwal' => ['ACC', 'primary'],
                    default => ['Proses', 'warning'],
                };

                $jadwalTerdekat = [
                    'judul' => $dokumenModel->bab_aktif,
                    'tanggal' => $dokumenModel->updated_at?->translatedFormat('d M Y') ?? '-',
                    'waktu' => ($dokumenModel->updated_at?->translatedFormat('H.i') ?? '-').' WIB',
                    'tempat' => (string) config('simbim.ruang_prodi'),
                    'sisa' => $dokumenModel->status_draf,
                    'is_h1' => $dokumenModel->status_draf === 'Bimbingan Terjadwal',
                ];

                $riwayatBimbingan = [
                    [
                        'id' => 1,
                        'tanggal' => $dokumenModel->updated_at?->translatedFormat('d M Y'),
                        'waktu' => ($dokumenModel->updated_at?->translatedFormat('H.i') ?? '-').' WIB',
                        'topik' => $dokumenModel->bab_aktif,
                        'dokumen' => $dokumenModel->nama_file,
                        'status' => $rowStatus,
                        'badge' => $rowBadge,
                        'catatan' => $dokumenModel->catatan_akhir_dosen ?: ('Status draf: '.$dokumenModel->status_draf),
                        'foto' => null,
                    ],
                ];

                $catatanPembimbing = $dokumenModel->komentars
                    ->sortByDesc('id')
                    ->take(5)
                    ->map(fn ($kom): array => [
                        'tanggal' => $kom->created_at?->translatedFormat('d M Y') ?? '-',
                        'bab' => $kom->lokasi_dokumen ?: $dokumenModel->bab_aktif,
                        'catatan' => $kom->isi_komentar,
                        'badge' => $kom->is_resolved ? 'success' : 'warning',
                    ])
                    ->values()
                    ->all();

                if ($catatanPembimbing === [] && ! empty($dokumenModel->catatan_akhir_dosen)) {
                    $catatanPembimbing[] = [
                        'tanggal' => $dokumenModel->updated_at?->translatedFormat('d M Y') ?? '-',
                        'bab' => $dokumenModel->bab_aktif,
                        'catatan' => $dokumenModel->catatan_akhir_dosen,
                        'badge' => $dokumenModel->badge_status ?: 'info',
                    ];
                }

                $notifikasi = [
                    [
                        'judul' => 'Draf '.$dokumenModel->nama_file.' ('.$dokumenModel->status_draf.')',
                        'waktu' => $dokumenModel->updated_at?->diffForHumans() ?? 'Baru saja',
                    ],
                ];
            } else {
                $jadwalTerdekat = [
                    'judul' => 'Belum ada jadwal bimbingan skripsi',
                    'tanggal' => 'Belum Ada Jadwal',
                    'waktu' => '-',
                    'tempat' => (string) config('simbim.ruang_prodi'),
                    'sisa' => 'Menunggu Pengajuan',
                    'is_h1' => false,
                ];
                $riwayatBimbingan = [];
                $catatanPembimbing = [];
                $notifikasi = [
                    [
                        'judul' => 'Belum ada pengajuan atau draf skripsi tercatat',
                        'waktu' => 'Hari ini',
                    ],
                ];
            }

            $progresBab = $this->computeProgresBab($dokumenModel);
        } else {
            $jadwalTerdekat = [
                'judul' => 'Review Draft BAB III & Evaluasi Diagram Arsitektur System',
                'tanggal' => 'Sabtu, 11 Oktober 2026',
                'waktu' => '09.30 – 11.00 WIB',
                'tempat' => config('simbim.ruang_prodi').' / Zoom',
                'sisa' => 'Besok, 09:30 WIB (H-1)',
                'is_h1' => true,
            ];
            $riwayatBimbingan = [
                [
                    'id' => 1,
                    'tanggal' => null,
                    'waktu' => null,
                    'topik' => 'BAB III: Metodologi Penelitian & Perancangan System Diagram',
                    'dokumen' => $reviewDokumen['nama_file'],
                    'status' => 'Proses',
                    'badge' => 'warning',
                    'catatan' => 'Pengajuan bimbingan baru dikirim. Menunggu konfirmasi jadwal Dosen Pembimbing.',
                    'foto' => null,
                ],
                [
                    'id' => 2,
                    'tanggal' => '11 Okt 2026',
                    'waktu' => '09.30 - 11.00 WIB',
                    'topik' => 'BAB III: Review Flowchart & Class Diagram Sistem',
                    'dokumen' => $reviewDokumen['nama_file'],
                    'status' => 'ACC',
                    'badge' => 'primary',
                    'catatan' => 'Jadwal telah disetujui Dosen Pembimbing. Harap siapkan & unggah printout draft diagram.',
                    'foto' => null,
                ],
                [
                    'id' => 3,
                    'tanggal' => '28 Sep 2026',
                    'waktu' => '13.30 - 15.00 WIB',
                    'topik' => 'BAB II: Tinjauan Pustaka & Matriks Comparative Literature',
                    'dokumen' => 'Draft_BAB2_ACC.pdf',
                    'status' => 'Selesai',
                    'badge' => 'success',
                    'catatan' => 'BAB II dinyatakan ACC dengan catatan kecil pada penulisan sitasi IEEE.',
                    'foto' => null,
                ],
                [
                    'id' => 4,
                    'tanggal' => '10 Sep 2026',
                    'waktu' => '10.00 - 11.30 WIB',
                    'topik' => 'BAB I: Pendahuluan, Rumusan Masalah & Latar Belakang',
                    'dokumen' => 'Draft_BAB1_Final.pdf',
                    'status' => 'Selesai',
                    'badge' => 'success',
                    'catatan' => 'BAB I disetujui penuh. Melanjutkan pengerjaan BAB II.',
                    'foto' => 'assets/media/svg/avatars/002-boy-2.svg',
                ],
            ];
            $progresBab = [
                ['bab' => 'BAB I: Pendahuluan', 'persen' => 100, 'status' => 'ACC & Selesai', 'badge' => 'success'],
                ['bab' => 'BAB II: Tinjauan Pustaka', 'persen' => 100, 'status' => 'ACC & Selesai', 'badge' => 'success'],
                ['bab' => 'BAB III: Metodologi Penelitian', 'persen' => max(60, $ringkasan['persen_progres']), 'status' => 'Dalam Pengerjaan', 'badge' => 'primary'],
                ['bab' => 'BAB IV: Hasil & Pembahasan', 'persen' => 0, 'status' => 'Belum Dimulai', 'badge' => 'secondary'],
                ['bab' => 'BAB V: Kesimpulan & Saran', 'persen' => 0, 'status' => 'Belum Dimulai', 'badge' => 'secondary'],
            ];
            $catatanPembimbing = [
                [
                    'tanggal' => '28 Sep 2026',
                    'bab' => 'BAB II Tinjauan Pustaka',
                    'catatan' => 'Perbaiki sitasi jurnal internasional 3 tahun terakhir pada bagian arsitektur REST API. Struktur kajian sudah baik.',
                    'badge' => 'success',
                ],
                [
                    'tanggal' => '10 Sep 2026',
                    'bab' => 'BAB I Pendahuluan',
                    'catatan' => 'Latar belakang masalah sudah tajam. Rumusan masalah & tujuan penelitian telah sesuai dengan ruang lingkup skripsi.',
                    'badge' => 'info',
                ],
            ];
            $notifikasi = [
                [
                    'judul' => 'Komentar review dokumen skripsi telah disinkronkan',
                    'waktu' => 'Baru saja',
                ],
                [
                    'judul' => 'Dosen Pembimbing memberikan catatan pada BAB II',
                    'waktu' => '3 hari yang lalu',
                ],
            ];
        }

        $riwayatBimbinganPaginated = $this->paginateItems($riwayatBimbingan, 6);

        return view('mahasiswa.bimbingan_skripsi', [
            'mahasiswa' => $mahasiswa,
            'dosen_pembimbing' => $dosenPembimbing,
            'dosen_options' => $this->fetchDosenOptions(),
            'can_create_initial' => $isPersistedStudent,
            'is_persisted_student' => $isPersistedStudent,
            'active_dokumen_id' => $activeDokumenId,
            'ringkasan' => $ringkasan,
            'jadwal_terdekat' => $jadwalTerdekat,
            'riwayat_bimbingan' => $riwayatBimbinganPaginated,
            'progres_bab' => $progresBab,
            'catatan_pembimbing' => $catatanPembimbing,
            'review_dokumen' => $reviewDokumen,
            'diskusi_skripsi' => $diskusiSkripsi,
            'notifikasi' => $notifikasi,
        ]);
    }

    /**
     * Buat atau perbarui draf awal pengajuan skripsi mahasiswa agar langsung tersinkron ke Dosen Pembimbing.
     */
    public function storeInitialDraft(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->exists, 403);

        $existing = SkripsiDokumen::where('mahasiswa_id', $user->id)
            ->latest('updated_at')
            ->first();

        $validated = $request->validate([
            'dosen_pembimbing_id' => [
                $existing?->dosen_pembimbing_id ? 'nullable' : 'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'dosen')),
            ],
            'judul_skripsi' => [$existing ? 'nullable' : 'required', 'string', 'max:255'],
            'bab_aktif' => ['required', 'string', 'max:120'],
            'detail_agenda' => ['required', 'string', 'max:3000'],
            'file_draft' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:15360'],
        ], [
            'dosen_pembimbing_id.required' => 'Silakan pilih Dosen Pembimbing Prodi PSTI terlebih dahulu.',
            'judul_skripsi.required' => 'Judul rencana skripsi wajib diisi.',
            'bab_aktif.required' => 'Tahap / BAB bimbingan wajib dipilih.',
            'detail_agenda.required' => 'Catatan pengajuan bimbingan wajib diisi.',
        ]);

        $file = $request->file('file_draft');

        if ($existing === null) {
            $dokumen = $this->revisiService->createStudentInitialDraft($user, $validated, $file);

            return back()->with('success', 'Pengajuan & draf awal skripsi ('.$dokumen->nama_file.') berhasil dikirim ke Dosen Pembimbing.')
                ->with('active_tab', $file !== null ? 'review' : 'tabel');
        }

        if ($file !== null || ! empty(trim((string) ($validated['detail_agenda'] ?? '')))) {
            $this->revisiService->storeStudentRevisionUpload(
                $existing,
                $file,
                $file === null ? trim((string) $validated['detail_agenda']) : null,
                $validated['bab_aktif']
            );
        }

        if (! empty($validated['judul_skripsi'])) {
            $existing->update(['judul_skripsi' => trim($validated['judul_skripsi'])]);
        }

        return back()->with('success', 'Pengajuan bimbingan skripsi terbaru berhasil diperbarui dan disinkronkan ke Dosen Pembimbing.')
            ->with('active_tab', $file !== null ? 'review' : 'tabel');
    }

    /**
     * Susun rincian progres Bab I s.d Bab V secara dinamis mengikuti progres dokumen skripsi di database.
     *
     * @return array<int, array{bab: string, persen: int, status: string, badge: string}>
     */
    protected function computeProgresBab(?SkripsiDokumen $dokumen): array
    {
        $chapters = [
            1 => 'BAB I: Pendahuluan',
            2 => 'BAB II: Tinjauan Pustaka',
            3 => 'BAB III: Metodologi Penelitian',
            4 => 'BAB IV: Hasil & Pembahasan',
            5 => 'BAB V: Kesimpulan & Saran',
        ];

        if ($dokumen === null) {
            return array_values(array_map(fn (string $label): array => [
                'bab' => $label,
                'persen' => 0,
                'status' => 'Belum Dimulai',
                'badge' => 'secondary',
            ], $chapters));
        }

        $babUpper = strtoupper($dokumen->bab_aktif);
        $activeIndex = match (true) {
            str_contains($babUpper, 'BAB V'), str_contains($babUpper, 'BAB 5'), str_contains($babUpper, 'SIDANG') => 5,
            str_contains($babUpper, 'BAB IV'), str_contains($babUpper, 'BAB 4') => 4,
            str_contains($babUpper, 'BAB III'), str_contains($babUpper, 'BAB 3') => 3,
            str_contains($babUpper, 'BAB II'), str_contains($babUpper, 'BAB 2') => 2,
            default => 1,
        };

        $persen = max(0, min(100, (int) $dokumen->progres_persen));
        $result = [];

        foreach ($chapters as $idx => $label) {
            if ($idx < $activeIndex || ($idx === 5 && $dokumen->status_draf === 'Siap Sidang Skripsi')) {
                $result[] = ['bab' => $label, 'persen' => 100, 'status' => 'ACC & Selesai', 'badge' => 'success'];
            } elseif ($idx === $activeIndex) {
                $result[] = [
                    'bab' => $label,
                    'persen' => max(10, $persen),
                    'status' => $dokumen->status_draf === 'Siap Sidang Skripsi' ? 'ACC & Selesai' : 'Dalam Pengerjaan',
                    'badge' => $dokumen->status_draf === 'Siap Sidang Skripsi' ? 'success' : 'primary',
                ];
            } else {
                $result[] = ['bab' => $label, 'persen' => 0, 'status' => 'Belum Dimulai', 'badge' => 'secondary'];
            }
        }

        return $result;
    }

    /**
     * Simpan balasan atau komentar klarifikasi mahasiswa pada komentar dokumen dosen.
     */
    public function storeKomentar(Request $request, SkripsiDokumen $dokumen): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $this->authorizeMahasiswaAccess($user, $dokumen);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer'],
            'lokasi_dokumen' => ['nullable', 'string', 'max:150'],
            'kutipan_teks' => ['nullable', 'string', 'max:2000'],
            'isi_komentar' => ['required', 'string', 'max:3000'],
        ], [
            'isi_komentar.required' => 'Isi balasan atau komentar wajib diisi.',
        ]);

        $validated['status'] = 'Catatan';
        $komentar = $this->revisiService->storeKomentar($dokumen, $user, $validated);

        if ($komentar->parent_id !== null) {
            $parent = $dokumen->komentars()->find($komentar->parent_id);
            $payload = $parent ? $this->revisiService->formatKomentar($parent) : $this->revisiService->formatKomentar($komentar);
        } else {
            $payload = $this->revisiService->formatKomentar($komentar);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Komentar / balasan berhasil disimpan.',
                'komentar' => $payload,
            ], 201);
        }

        return back()->with('success', 'Balasan komentar dokumen berhasil dikirim ke dosen pembimbing.')
            ->with('active_tab', 'review');
    }

    /**
     * Kirim pesan diskusi dua arah dari portal mahasiswa menuju dosen pembimbing.
     */
    public function storeDiskusi(Request $request, SkripsiDokumen $dokumen): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $this->authorizeMahasiswaAccess($user, $dokumen);

        $validated = $request->validate([
            'pesan' => ['required', 'string', 'max:2000'],
            'referensi_komentar_id' => ['nullable', 'integer'],
        ], [
            'pesan.required' => 'Pesan diskusi tidak boleh kosong.',
        ]);

        $pesan = $this->revisiService->storeDiskusiPesan($dokumen, $user, $validated);
        $formatted = $this->revisiService->formatDiskusiPesan($pesan);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pesan diskusi berhasil dikirim.',
                'pesan' => $formatted,
            ], 201);
        }

        return back()->with('success', 'Pesan diskusi berhasil dikirim ke dosen pembimbing.')
            ->with('active_tab', 'diskusi');
    }

    /**
     * Unggah berkas atau teks revisi draf skripsi terbaru dari mahasiswa.
     */
    public function uploadRevision(Request $request, SkripsiDokumen $dokumen): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $this->authorizeMahasiswaAccess($user, $dokumen);

        $validated = $request->validate([
            'file_draft' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:15360'],
            'konten_naskah' => ['nullable', 'string', 'max:100000'],
            'bab_aktif' => ['nullable', 'string', 'max:120'],
        ]);

        $file = $request->file('file_draft');
        if ($file === null && empty(trim((string) ($validated['konten_naskah'] ?? '')))) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unggah berkas draf atau isi pembaruan teks naskah.'], 422);
            }

            return back()->withErrors(['file_draft' => 'Unggah berkas draf atau isi pembaruan teks naskah.']);
        }

        $updated = $this->revisiService->storeStudentRevisionUpload(
            $dokumen,
            $file,
            $validated['konten_naskah'] ?? null,
            $validated['bab_aktif'] ?? null
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Revisi draf berhasil diperbarui.',
                'nama_file' => $updated->nama_file,
                'versi' => $updated->versi,
                'status_draf' => $updated->status_draf,
            ]);
        }

        return back()->with('success', 'Revisi draf skripsi ('.$updated->versi.') berhasil disinkronkan dari berkas '.$updated->nama_file.' dan dikirim ke dosen pembimbing.')
            ->with('active_tab', 'review');
    }

    /**
     * Unduh draf skripsi mahasiswa.
     */
    public function downloadFile(SkripsiDokumen $dokumen): Response
    {
        $this->authorizeMahasiswaAccess(Auth::user(), $dokumen);

        if (! empty($dokumen->file_path) && Storage::disk('local')->exists($dokumen->file_path)) {
            return Storage::disk('local')->download($dokumen->file_path, $dokumen->nama_file);
        }

        $safeBase = pathinfo((string) $dokumen->nama_file, PATHINFO_FILENAME) ?: 'Draf_Skripsi';
        $content = $dokumen->judul_skripsi."\n\n".($dokumen->konten_naskah ?? '');

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$safeBase.'.txt"',
        ]);
    }

    /**
     * Pratinjau inline berkas PDF draf skripsi mahasiswa.
     */
    public function previewFile(SkripsiDokumen $dokumen): Response
    {
        $this->authorizeMahasiswaAccess(Auth::user(), $dokumen);

        if (empty($dokumen->file_path) || ! Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($dokumen->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$dokumen->nama_file.'"',
        ]);
    }

    protected function authorizeMahasiswaAccess(?User $user, SkripsiDokumen $dokumen): void
    {
        abort_if(! $user, 403);

        if ($user->isAdmin()) {
            return;
        }

        abort_if(
            (int) $dokumen->mahasiswa_id !== (int) $user->id,
            403,
            'Anda tidak memiliki akses ke dokumen skripsi mahasiswa lain.'
        );
    }
}
