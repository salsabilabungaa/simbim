<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Lulus Sidang Skripsi — {{ $skripsi->mahasiswa?->name ?? 'Mahasiswa' }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #f5f8fa;
            font-family: 'Poppins', sans-serif;
            color: #181c32;
        }
        .page-container {
            max-width: 820px;
            margin: 30px auto;
            background: #fff;
            padding: 50px 60px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 8px;
        }
        .kop-surat {
            border-bottom: 3px double #1e1e2d;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .page-container {
                box-shadow: none;
                margin: 0;
                padding: 30px 40px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4 no-print text-center">
        <div class="d-inline-flex gap-2">
            <a href="{{ route('dewan-skripsi.verifikasi-pasca-sidang') }}" class="btn btn-light fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Verifikasi Pasca-Sidang
            </a>
            <button onclick="window.print()" class="btn btn-primary fw-bolder">
                <i class="fas fa-print me-1"></i> Cetak Dokumen SKL (Print / PDF)
            </button>
        </div>
    </div>

    <div class="page-container">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat d-flex align-items-center">
            <div class="me-4">
                <img src="{{ asset('assets/media/logos/favicon.ico') }}" alt="Logo USK" style="width: 75px; height: 75px; object-fit: contain;" />
            </div>
            <div class="text-center flex-grow-1">
                <h6 class="text-uppercase mb-0 fs-7 fw-bold" style="letter-spacing: 1px;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h6>
                <h4 class="text-uppercase mb-0 fw-bolder fs-4" style="letter-spacing: 1.5px;">UNIVERSITAS SYIAH KUALA</h4>
                <h5 class="text-uppercase mb-1 fw-bold fs-6">FAKULTAS TEKNIK — JURUSAN INFORMATIKA</h5>
                <p class="mb-0 fs-8 text-muted">
                    Jl. Syech Abdurrauf No. 7, Kopelma Darussalam, Banda Aceh 23111<br>
                    Laman: informatika.usk.ac.id | Email: info@informatika.usk.ac.id
                </p>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="text-center mb-6">
            <h4 class="fw-bolder text-uppercase mb-1" style="text-decoration: underline; letter-spacing: 1px;">
                SURAT KETERANGAN LULUS SIDANG SKRIPSI
            </h4>
            <span class="fs-7 fw-bold text-dark font-monospace">
                Nomor: {{ $skripsi->no_surat_keterangan ?: 'SKL/FT-USK/SKR/' . date('Y') . '/' . str_pad($skripsi->id, 3, '0', STR_PAD_LEFT) }}
            </span>
        </div>

        <p class="fs-7 mb-4 text-justify" style="line-height: 1.8;">
            Dewan Sidang Skripsi Jurusan Informatika, Fakultas Teknik, Universitas Syiah Kuala dengan ini menerangkan bahwa mahasiswa yang tersebut di bawah ini:
        </p>

        <!-- Identitas Mahasiswa -->
        <table class="table table-borderless fs-7 mb-4 ms-3" style="width: 90%;">
            <tbody>
                <tr>
                    <td class="w-200px fw-bold py-1">Nama Mahasiswa</td>
                    <td class="w-10px py-1">:</td>
                    <td class="fw-bolder py-1 text-dark">{{ $skripsi->mahasiswa?->name ?? 'Andi Pratama' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold py-1">Nomor Induk Mahasiswa (NIM)</td>
                    <td class="py-1">:</td>
                    <td class="fw-bold py-1 text-dark">{{ $skripsi->mahasiswa?->nim ?? '2108107010001' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold py-1">Program Studi</td>
                    <td class="py-1">:</td>
                    <td class="py-1 text-dark">S1 Informatika</td>
                </tr>
                <tr>
                    <td class="fw-bold py-1">Bidang Keahlian / Minat</td>
                    <td class="py-1">:</td>
                    <td class="py-1 text-dark">{{ $skripsi->bidang_minat ?? 'Software Engineering' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold py-1 align-top">Judul Tugas Akhir / Skripsi</td>
                    <td class="py-1 align-top">:</td>
                    <td class="fw-bold py-1 text-dark align-top" style="line-height: 1.6;">
                        "{{ $skripsi->judul }}"
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Hasil Sidang & Nilai -->
        <p class="fs-7 mb-3 text-justify" style="line-height: 1.8;">
            Telah melaksanakan Ujian Sidang Skripsi / Tugas Akhir dan menyelesaikan seluruh kewajiban revisi naskah akhir yang disyaratkan oleh Dewan Penguji dengan rincian kelulusan sebagai berikut:
        </p>

        <div class="border rounded p-4 mb-5 bg-light">
            <div class="row g-2 fs-7">
                <div class="col-6">
                    <strong>Tanggal Pelaksanaan Sidang:</strong><br>
                    <span>{{ $skripsi->jadwal_sidang?->translatedFormat('d F Y') ?? date('d F Y') }}</span>
                </div>
                <div class="col-6">
                    <strong>Status Kelulusan:</strong><br>
                    <span class="badge badge-success fs-8 fw-bolder px-3 py-1">LULUS / SELESAI</span>
                </div>
                <div class="col-6 mt-3">
                    <strong>Nilai Angka / Rata-rata Akhir:</strong><br>
                    <span class="fs-5 fw-bolder text-primary">{{ $skripsi->nilai_akhir ?? '88.13' }}</span>
                </div>
                <div class="col-6 mt-3">
                    <strong>Nilai Mutu / Nilai Huruf:</strong><br>
                    <span class="fs-5 fw-bolder text-success">{{ $skripsi->nilai_huruf ?? 'A' }}</span>
                </div>
            </div>
        </div>

        <p class="fs-7 mb-6 text-justify" style="line-height: 1.8;">
            Surat keterangan ini diterbitkan sebagai bukti resmi penyelesaian Tugas Akhir Skripsi dan dapat dipergunakan oleh mahasiswa bersangkutan sebagai salah satu syarat untuk mendaftar <strong>Yudisium Sarjana Komputer (S.Kom.)</strong>.
        </p>

        <!-- Tanda Tangan -->
        <div class="d-flex justify-content-between mt-8 pt-4">
            <div class="text-center" style="width: 250px;">
                <p class="mb-1 fs-8">Mengetahui,</p>
                <p class="fw-bold fs-7 mb-15">Dosen Pembimbing Utama</p>
                <p class="fw-bolder fs-7 mb-0 text-dark" style="text-decoration: underline;">
                    {{ $skripsi->nama_pembimbing_1 }}
                </p>
                <span class="fs-8 text-muted">NIP. {{ $skripsi->pembimbing1?->nip ?? '198005122008011003' }}</span>
            </div>

            <div class="text-center" style="width: 250px;">
                <p class="mb-1 fs-8">Darussalam, {{ $skripsi->tanggal_lulus?->translatedFormat('d F Y') ?? date('d F Y') }}</p>
                <p class="fw-bold fs-7 mb-15">Koordinator Dewan Skripsi</p>
                <p class="fw-bolder fs-7 mb-0 text-dark" style="text-decoration: underline;">
                    {{ $dewanUser->name ?? 'Dr. Ir. Taufik Hidayat, M.Kom.' }}
                </p>
                <span class="fs-8 text-muted">NIP. {{ $dewanUser->nip ?? '197803152005011002' }}</span>
            </div>
        </div>
    </div>
</body>
</html>

