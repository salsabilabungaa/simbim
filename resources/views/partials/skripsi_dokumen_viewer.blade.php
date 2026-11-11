@php
    $roleMode = $roleMode ?? 'dosen'; // 'dosen' | 'mahasiswa'
    $isDosenMode = $roleMode === 'dosen';
    $dokumenId = $dokumen['id'] ?? $dokumen['dokumen_id'] ?? null;
    $sections = $sections ?? [];
    $komentars = $komentars ?? [];
    $storeKomentarUrl = $storeKomentarUrl ?? '#';
    $downloadUrl = $downloadUrl ?? null;
    $previewUrl = $previewUrl ?? null;
    $hasPdfPreview = ! empty($dokumen['has_uploaded_file']) && ! empty($dokumen['is_pdf']) && ! empty($previewUrl);
    $resolveUrlPattern = $resolveUrlPattern ?? '';
    $canInlineSelectComment = $canInlineSelectComment ?? ($dokumenId !== null);
    $currentUserName = $currentUserName ?? auth()->user()?->name ?? ($isDosenMode ? 'Dosen Pembimbing' : 'Mahasiswa');
@endphp

<style>
    .skripsi-doc-wrapper {
        position: relative;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1.5rem;
    }
    .skripsi-doc-paper {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        padding: 2.25rem 2.5rem;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        font-family: Georgia, 'Times New Roman', serif;
        color: #1e293b;
        line-height: 1.8;
        user-select: text;
        max-height: 680px;
        overflow-y: auto;
    }
    .skripsi-doc-paper h4.doc-h2 {
        font-family: Poppins, sans-serif;
        font-weight: 700;
        font-size: 1.15rem;
        color: #0f172a;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.4rem;
        margin-top: 0.25rem;
        margin-bottom: 1rem;
    }
    .skripsi-doc-paper h5.doc-h3 {
        font-family: Poppins, sans-serif;
        font-weight: 600;
        font-size: 1rem;
        color: #1d4ed8;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }
    .doc-block-row {
        position: relative;
        padding-right: 2.4rem;
        border-radius: 0.35rem;
        transition: background-color 0.15s ease;
    }
    .doc-render-target {
        white-space: pre-line;
    }
    .doc-block-row:hover {
        background-color: rgba(248, 250, 252, 0.9);
    }
    .doc-paragraph-comment-btn {
        position: absolute;
        top: 0.15rem;
        right: 0.25rem;
        opacity: 0.45;
        transition: opacity 0.15s ease, transform 0.15s ease;
        font-family: Poppins, sans-serif;
    }
    .doc-block-row:hover .doc-paragraph-comment-btn,
    .doc-paragraph-comment-btn:focus {
        opacity: 1;
        transform: scale(1.05);
    }
    mark.doc-highlight {
        background-color: #fde68a;
        color: #1e293b;
        padding: 0.08rem 0.15rem;
        border-radius: 0.2rem;
        border-bottom: 2px solid #f59e0b;
        cursor: pointer;
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }
    mark.doc-highlight.doc-highlight-resolved {
        background-color: #dcfce7;
        border-bottom-color: #22c55e;
    }
    mark.doc-highlight.doc-highlight-active {
        background-color: #facc15;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.35);
    }
    .floating-gdocs-comment {
        position: absolute;
        z-index: 40;
        width: 320px;
        max-width: calc(100% - 2rem);
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 0.85rem;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.18);
        padding: 1rem;
        display: none;
    }
    .comment-card-item {
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .comment-card-item.comment-card-active {
        border-color: #3699ff !important;
        box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.18);
    }
</style>

<div class="row g-6 align-items-start" id="skripsi_revisi_workspace_root">
    <!-- Kolom Kiri: Dokumen Naskah yang Dapat Diberi Komentar Langsung -->
    <div class="col-xl-7">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-4 rounded-3 bg-light-primary border border-primary border-dashed">
            <div class="d-flex align-items-center">
                <i class="fas fa-file-alt text-primary fs-2x me-3"></i>
                <div>
                    <div class="fw-bolder text-dark fs-6" id="doc_header_filename">{{ $dokumen['nama_file'] ?? 'Draft_Skripsi.pdf' }}</div>
                    <div class="text-muted fs-8">
                        Versi: <strong id="doc_header_versi">{{ $dokumen['versi'] ?? 'v1.0' }}</strong>
                        &bull; Status:
                        <span class="badge badge-light-{{ $dokumen['badge_status'] ?? 'warning' }} fw-bolder fs-9 ms-1" id="doc_header_status">
                            {{ $dokumen['status_draf'] ?? 'Menunggu Review Dosen' }}
                        </span>
                        @if (! empty($dokumen['tanggal_upload']) || ! empty($dokumen['updated_label']))
                            &bull; Diperbarui: {{ $dokumen['updated_label'] ?? $dokumen['tanggal_upload'] }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if ($hasPdfPreview)
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary fw-bold active" id="btn_mode_naskah" onclick="switchDocViewerMode('naskah')">
                            <i class="fas fa-highlighter me-1"></i> Naskah Komentar
                        </button>
                        <button type="button" class="btn btn-light-primary fw-bold" id="btn_mode_pdf" onclick="switchDocViewerMode('pdf')">
                            <i class="fas fa-file-pdf me-1"></i> File PDF
                        </button>
                    </div>
                @endif
                @if (! empty($downloadUrl))
                    <a href="{{ $downloadUrl }}" class="btn btn-sm btn-primary fw-bolder">
                        <i class="fas fa-download me-1"></i> Unduh Draf
                    </a>
                @endif
            </div>
        </div>

        @if ($canInlineSelectComment)
            <div class="alert alert-light-warning d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 border border-warning border-dashed">
                <div class="d-flex align-items-center fs-8 text-gray-800 fw-semibold">
                    <i class="fas fa-highlighter text-warning fs-4 me-3"></i>
                    <span>
                        @if ($isDosenMode)
                            <strong>Mode Komentar Langsung:</strong> Blok teks pada dokumen di bawah untuk memberi komentar seperti Google Docs, atau klik ikon komentar <i class="fas fa-comment-medical text-primary"></i> di samping paragraf.
                        @else
                            <strong>Sorotan Review Pembimbing:</strong> Klik sorotan kuning pada naskah untuk melihat komentar dosen pembimbing, atau balas komentar pada panel sebelah kanan.
                        @endif
                    </span>
                </div>
            </div>
        @endif

        <!-- Container Dokumen Gaya Google Docs -->
        <div id="doc_mode_naskah_panel" class="skripsi-doc-wrapper">
            <div class="skripsi-doc-paper" id="doc_paper_surface">
                @forelse ($sections as $sec)
                    <div class="doc-block-row mb-3"
                         data-sec-id="{{ $sec['id'] }}"
                         data-heading="{{ $sec['heading'] }}"
                         data-block-type="{{ $sec['type'] }}"
                         data-raw-text="{{ $sec['text'] }}">
                        @if ($sec['type'] === 'h2')
                            <h4 class="doc-h2 doc-render-target">{{ $sec['text'] }}</h4>
                        @elseif ($sec['type'] === 'h3')
                            <h5 class="doc-h3 doc-render-target">{{ $sec['text'] }}</h5>
                        @else
                            <p class="mb-2 doc-render-target">{{ $sec['text'] }}</p>
                            @if ($canInlineSelectComment)
                                <button type="button"
                                        class="btn btn-icon btn-sm btn-light-primary doc-paragraph-comment-btn"
                                        title="Beri komentar pada paragraf ini"
                                        onclick="openParagraphCommentBox(this)">
                                    <i class="fas fa-comment-medical fs-7"></i>
                                </button>
                            @endif
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted py-8 font-sans-serif">
                        <i class="fas fa-file-alt fs-2x mb-2 text-gray-400 d-block"></i>
                        @if ($hasPdfPreview)
                            <div class="fw-bold text-gray-800 mb-2">
                                Naskah teks tidak dapat diekstrak otomatis dari berkas <strong>{{ $dokumen['nama_file'] }}</strong> (kemungkinan PDF hasil pindaian/gambar).
                            </div>
                            <button type="button" class="btn btn-sm btn-primary fw-bolder mt-2" onclick="switchDocViewerMode('pdf')">
                                <i class="fas fa-file-pdf me-1"></i> Buka File PDF Asli
                            </button>
                        @else
                            Konten naskah dokumen belum tersedia untuk pratinjau teks interaktif.
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($canInlineSelectComment)
                <!-- Floating Google Docs Comment Popover Box -->
                <div class="floating-gdocs-comment" id="floating_doc_comment_box">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="symbol symbol-30px symbol-circle bg-primary text-white fw-bolder fs-8 d-flex align-items-center justify-content-center">
                                {{ strtoupper(substr($currentUserName, 0, 1)) }}
                            </div>
                            <span class="fw-bolder text-dark fs-7">{{ $currentUserName }}</span>
                        </div>
                        <button type="button" class="btn btn-icon btn-xs btn-light" onclick="closeFloatingCommentBox()">
                            <i class="fas fa-times fs-8"></i>
                        </button>
                    </div>

                    <div class="p-2 mb-2 rounded bg-warning bg-opacity-25 border-start border-3 border-warning fs-8 text-gray-800 fst-italic text-truncate" id="floating_quote_preview" title="">
                        "..."
                    </div>

                    <form id="floating_comment_form" method="POST" action="{{ $storeKomentarUrl }}" onsubmit="submitFloatingComment(event)">
                        @csrf
                        <input type="hidden" name="kutipan_teks" id="floating_kutipan_teks" value="" />
                        <div class="mb-2">
                            <input type="text" name="lokasi_dokumen" id="floating_lokasi_dokumen" class="form-control form-control-sm form-control-solid fs-8" placeholder="Lokasi / Sub-bab (mis. Sub-bab 3.2)" required />
                        </div>
                        @if ($isDosenMode)
                            <div class="mb-2">
                                <select name="status" id="floating_status_select" class="form-select form-select-sm form-select-solid fs-8" data-control="select2" data-dropdown-parent="#floating_doc_comment_box" data-placeholder="Pilih Status">
                                    <option value="Perlu Revisi">Perlu Revisi</option>
                                    <option value="Catatan">Catatan</option>
                                    <option value="Disetujui">Disetujui</option>
                                </select>
                            </div>
                        @endif
                        <div class="mb-3">
                            <textarea name="isi_komentar" id="floating_isi_komentar" rows="3" class="form-control form-control-sm border border-primary fs-8" placeholder="Beri komentar langsung pada teks terpilih..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-light fw-bold py-1 px-3 fs-8" onclick="closeFloatingCommentBox()">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary fw-bolder py-1 px-4 fs-8" id="btn_submit_floating_comment">
                                Komentar
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        @if ($hasPdfPreview)
            <div id="doc_mode_pdf_panel" style="display: none;">
                <iframe src="{{ $previewUrl }}" class="w-100 rounded border border-gray-300" style="height: 680px;" title="Pratinjau Dokumen PDF"></iframe>
            </div>
        @endif
    </div>

    <!-- Kolom Kanan: Daftar Komentar Langsung Dokumen & Formulir Manual -->
    <div class="col-xl-5">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm mb-5">
            <div class="card-header border-0 pt-5 pb-3">
                <h4 class="card-title fw-bolder text-dark fs-5 mb-0">
                    <i class="fas fa-comment-dots text-warning me-2"></i>
                    Komentar Langsung Dokumen
                    <span class="badge badge-light-primary fw-bolder ms-2" id="doc_komentar_count">{{ count($komentars) }}</span>
                </h4>
                @if ($canInlineSelectComment)
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-xs btn-light-primary fw-bolder px-3 py-2" data-bs-toggle="collapse" data-bs-target="#collapse_manual_comment_form">
                            <i class="fas fa-plus me-1"></i> Tambah Komentar
                        </button>
                    </div>
                @endif
            </div>

            <div class="card-body pt-0">
                @if ($canInlineSelectComment)
                    <div class="collapse mb-4 p-4 rounded bg-light border border-primary border-dashed" id="collapse_manual_comment_form">
                        <form id="manual_comment_form" method="POST" action="{{ $storeKomentarUrl }}" onsubmit="submitManualComment(event)">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label fw-bold fs-8 required">Bagian / Halaman Dokumen</label>
                                <input type="text" name="lokasi_dokumen" id="manual_lokasi_dokumen" class="form-control form-control-sm form-control-solid fs-8" placeholder="Contoh: Halaman 14 (Sub-bab 3.2)" required />
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold fs-8">Kutipan Teks Dokumen (Opsional)</label>
                                <input type="text" name="kutipan_teks" id="manual_kutipan_teks" class="form-control form-control-sm form-control-solid fs-8" placeholder="Salin kalimat dari dokumen untuk disorot kuning..." />
                            </div>
                            @if ($isDosenMode)
                                <div class="mb-2">
                                    <label class="form-label fw-bold fs-8">Status Komentar</label>
                                    <select name="status" id="manual_status_select" class="form-select form-select-sm form-select-solid fs-8" data-control="select2" data-dropdown-parent="#collapse_manual_comment_form" data-placeholder="Pilih Status">
                                        <option value="Perlu Revisi">Perlu Revisi</option>
                                        <option value="Catatan">Catatan</option>
                                        <option value="Disetujui">Disetujui</option>
                                    </select>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label fw-bold fs-8 required">Komentar</label>
                                <textarea name="isi_komentar" id="manual_isi_komentar" rows="2" class="form-control form-control-sm form-control-solid fs-8" placeholder="Tuliskan komentar revisi dokumen..." required></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-xs btn-light" data-bs-toggle="collapse" data-bs-target="#collapse_manual_comment_form">Tutup</button>
                                <button type="submit" class="btn btn-xs btn-primary fw-bolder">Kirim Komentar</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="d-flex flex-column gap-3 scroll-y" style="max-height: 520px;" id="doc_comments_list">
                    @forelse ($komentars as $rev)
                        @php
                            $cId = $rev['id'] ?? null;
                            $lokasi = $rev['lokasi_dokumen'] ?? $rev['halaman'] ?? 'Dokumen Skripsi';
                            $kutipan = $rev['kutipan_teks'] ?? $rev['kutipan'] ?? null;
                            $isi = $rev['isi_komentar'] ?? $rev['catatan'] ?? '';
                            $statusTxt = $rev['status'] ?? 'Catatan';
                            $badgeCls = $rev['badge'] ?? 'warning';
                            $isResolved = ! empty($rev['is_resolved']);
                            $pengirimNama = $rev['pengirim'] ?? ($isDosenMode ? $dokumen['dosen_nama'] ?? 'Dosen' : 'Dosen Pembimbing');
                            $roleTxt = $rev['role_label'] ?? 'Dosen Pembimbing';
                            $waktuTxt = $rev['waktu'] ?? '';
                            $balasanList = $rev['balasan'] ?? [];
                        @endphp
                        <div class="p-4 rounded-3 bg-light border border-gray-200 comment-card-item {{ $isResolved ? 'opacity-75' : '' }}"
                             id="comment-card-{{ $cId ?? $loop->index }}"
                             data-comment-id="{{ $cId ?? '' }}">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div>
                                    <span class="fw-bolder fs-7 text-primary d-block">{{ $lokasi }}</span>
                                    <span class="text-muted fs-9">
                                        <strong>{{ $pengirimNama }}</strong> ({{ $roleTxt }}) @if ($waktuTxt) &bull; {{ $waktuTxt }} @endif
                                    </span>
                                </div>
                                <span class="badge badge-light-{{ $badgeCls }} fw-bolder px-2 py-1 fs-9 comment-status-badge">
                                    {{ $statusTxt }}
                                </span>
                            </div>

                            @if (! empty($kutipan))
                                <div class="p-2 mb-2 rounded bg-warning bg-opacity-25 border-start border-3 border-warning fs-8 text-gray-800 fst-italic {{ ($rev['quote_in_doc'] ?? true) ? 'cursor-pointer' : '' }}"
                                     @if ($rev['quote_in_doc'] ?? true)
                                         onclick="focusHighlightInDoc('{{ $cId }}')"
                                         title="Klik untuk menyorot di dokumen"
                                     @endif>
                                    "{{ $kutipan }}"
                                    @if (isset($rev['quote_in_doc']) && ! $rev['quote_in_doc'])
                                        <span class="d-block not-italic text-muted fs-9 mt-1">
                                            <i class="fas fa-info-circle me-1"></i> Berasal dari draf versi sebelumnya (teks telah diperbarui mahasiswa)
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <p class="text-gray-800 fs-7 fw-semibold mb-2">
                                {{ $isi }}
                            </p>

                            @if (! empty($balasanList))
                                <div class="ps-3 border-start border-2 border-primary d-flex flex-column gap-2 mb-2 comment-replies-list">
                                    @foreach ($balasanList as $reply)
                                        <div class="p-2 rounded bg-white border border-gray-200 fs-8">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bolder text-dark">{{ $reply['pengirim'] }} <span class="badge badge-light-{{ $reply['badge_role'] }} fs-9 ms-1">{{ $reply['role_label'] }}</span></span>
                                                <span class="text-muted fs-9">{{ $reply['waktu'] }}</span>
                                            </div>
                                            <div class="text-gray-700 mt-1">{{ $reply['isi_komentar'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="ps-3 border-start border-2 border-primary d-flex flex-column gap-2 mb-2 comment-replies-list"></div>
                            @endif

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top border-gray-200">
                                <div class="d-flex gap-2">
                                    @if (! empty($kutipan) && $cId && ($rev['quote_in_doc'] ?? true))
                                        <button type="button" class="btn btn-xs btn-light-warning fw-bold fs-9 py-1 px-2" onclick="focusHighlightInDoc('{{ $cId }}')">
                                            <i class="fas fa-highlighter me-1"></i> Lihat di Naskah
                                        </button>
                                    @endif
                                    @if ($canInlineSelectComment && $cId)
                                        <button type="button" class="btn btn-xs btn-light-primary fw-bold fs-9 py-1 px-2" onclick="toggleReplyForm('{{ $cId }}')">
                                            <i class="fas fa-reply me-1"></i> Balas
                                        </button>
                                    @endif
                                </div>

                                @if ($isDosenMode && $cId && ! empty($resolveUrlPattern))
                                    <form method="POST" action="{{ str_replace('__COMMENT_ID__', (string) $cId, $resolveUrlPattern) }}" onsubmit="submitToggleResolve(event, {{ $cId }})">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-xs {{ $isResolved ? 'btn-light' : 'btn-light-success' }} fw-bolder fs-9 py-1 px-2 btn-resolve-toggle">
                                            <i class="fas {{ $isResolved ? 'fa-undo' : 'fa-check-double' }} me-1"></i>
                                            {{ $isResolved ? 'Buka Lagi' : 'Tandai Selesai' }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if ($canInlineSelectComment && $cId)
                                <form method="POST"
                                      action="{{ $storeKomentarUrl }}"
                                      class="mt-2 pt-2 border-top border-dashed reply-comment-form"
                                      id="reply_form_{{ $cId }}"
                                      style="display: none;"
                                      onsubmit="submitInlineReply(event, {{ $cId }})">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $cId }}" />
                                    <input type="hidden" name="lokasi_dokumen" value="{{ $lokasi }}" />
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="isi_komentar" class="form-control form-control-solid fs-8" placeholder="Tulis balasan komentar..." required />
                                        <button type="submit" class="btn btn-primary fw-bolder fs-8">Kirim</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-6 fs-7" id="empty_komentar_placeholder">
                            Belum ada komentar langsung pada dokumen ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @if (! empty($rightSlotPartial))
            @include($rightSlotPartial, $rightSlotData ?? [])
        @endif
    </div>
</div>

@once
@push('scripts')
<script>
    window.__skripsiState = {
        storeKomentarUrl: @json($storeKomentarUrl),
        resolveUrlPattern: @json($resolveUrlPattern),
        isDosenMode: @json($isDosenMode),
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        komentars: @json($komentars),
    };

    function escapeDocHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function switchDocViewerMode(mode) {
        const naskahPanel = document.getElementById('doc_mode_naskah_panel');
        const pdfPanel = document.getElementById('doc_mode_pdf_panel');
        const btnNaskah = document.getElementById('btn_mode_naskah');
        const btnPdf = document.getElementById('btn_mode_pdf');
        if (!naskahPanel || !pdfPanel) return;

        if (mode === 'pdf') {
            naskahPanel.style.display = 'none';
            pdfPanel.style.display = 'block';
            btnPdf?.classList.add('btn-primary', 'active');
            btnPdf?.classList.remove('btn-light-primary');
            btnNaskah?.classList.remove('btn-primary', 'active');
            btnNaskah?.classList.add('btn-light-primary');
        } else {
            pdfPanel.style.display = 'none';
            naskahPanel.style.display = 'block';
            btnNaskah?.classList.add('btn-primary', 'active');
            btnNaskah?.classList.remove('btn-light-primary');
            btnPdf?.classList.remove('btn-primary', 'active');
            btnPdf?.classList.add('btn-light-primary');
        }
    }

    function renderDocumentHighlights() {
        const paper = document.getElementById('doc_paper_surface');
        if (!paper) return;

        const comments = Array.isArray(window.__skripsiState?.komentars) ? window.__skripsiState.komentars : [];
        const rows = paper.querySelectorAll('.doc-block-row');

        rows.forEach(row => {
            const rawText = row.getAttribute('data-raw-text') ?? '';
            const target = row.querySelector('.doc-render-target');
            if (!target) return;

            const ranges = [];
            comments.forEach(c => {
                const quote = (c.kutipan_teks || c.kutipan || '').trim();
                if (!quote || quote.length < 3) return;
                const idx = rawText.indexOf(quote);
                if (idx !== -1) {
                    ranges.push({
                        start: idx,
                        end: idx + quote.length,
                        id: c.id,
                        resolved: Boolean(c.is_resolved),
                    });
                }
            });

            if (ranges.length === 0) {
                target.textContent = rawText;
                return;
            }

            ranges.sort((a, b) => a.start - b.start || (b.end - b.start) - (a.end - a.start));

            let html = '';
            let cursor = 0;
            ranges.forEach(r => {
                if (r.start < cursor) {
                    return;
                }
                if (r.start > cursor) {
                    html += escapeDocHtml(rawText.slice(cursor, r.start));
                }
                const cls = r.resolved ? 'doc-highlight doc-highlight-resolved' : 'doc-highlight';
                html += `<mark class="${cls}" data-comment-id="${escapeDocHtml(r.id)}" onclick="focusCommentCard('${escapeDocHtml(r.id)}')">${escapeDocHtml(rawText.slice(r.start, r.end))}</mark>`;
                cursor = r.end;
            });
            if (cursor < rawText.length) {
                html += escapeDocHtml(rawText.slice(cursor));
            }
            target.innerHTML = html;
        });
    }

    function focusCommentCard(commentId) {
        document.querySelectorAll('.comment-card-item').forEach(el => el.classList.remove('comment-card-active'));
        const card = document.getElementById('comment-card-' + commentId);
        if (card) {
            card.classList.add('comment-card-active');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function focusHighlightInDoc(commentId) {
        document.querySelectorAll('mark.doc-highlight').forEach(el => el.classList.remove('doc-highlight-active'));
        const marks = document.querySelectorAll('mark.doc-highlight[data-comment-id="' + commentId + '"]');
        if (marks.length > 0) {
            marks.forEach(m => m.classList.add('doc-highlight-active'));
            marks[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        focusCommentCard(commentId);
    }

    function openFloatingCommentBoxAt(clientX, clientY, selectedText, heading) {
        const box = document.getElementById('floating_doc_comment_box');
        const wrapper = document.getElementById('doc_mode_naskah_panel');
        if (!box || !wrapper) return;

        const wrapperRect = wrapper.getBoundingClientRect();
        const boxWidth = 320;
        let left = clientX - wrapperRect.left - 20;
        left = Math.max(12, Math.min(left, wrapperRect.width - boxWidth - 12));
        let top = clientY - wrapperRect.top + 14;
        top = Math.max(12, top);

        document.getElementById('floating_kutipan_teks').value = selectedText;
        document.getElementById('floating_quote_preview').textContent = '"' + selectedText + '"';
        document.getElementById('floating_quote_preview').title = selectedText;
        document.getElementById('floating_lokasi_dokumen').value = heading || 'Bagian Terpilih Dokumen';
        document.getElementById('floating_isi_komentar').value = '';

        box.style.left = left + 'px';
        box.style.top = top + 'px';
        box.style.display = 'block';
        setTimeout(() => document.getElementById('floating_isi_komentar')?.focus(), 50);
    }

    function closeFloatingCommentBox() {
        const box = document.getElementById('floating_doc_comment_box');
        if (box) box.style.display = 'none';
    }

    function openParagraphCommentBox(btn) {
        const row = btn.closest('.doc-block-row');
        if (!row) return;
        const rawText = (row.getAttribute('data-raw-text') || '').trim();
        const heading = row.getAttribute('data-heading') || 'Bagian Dokumen';
        const firstSentence = rawText.split(/[.!?]\s/)[0] || rawText;
        const snippet = firstSentence.slice(0, 120).trim();
        const rect = btn.getBoundingClientRect();
        openFloatingCommentBoxAt(rect.left - 240, rect.bottom, snippet, heading);
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderDocumentHighlights();

        const paper = document.getElementById('doc_paper_surface');
        const floatingBox = document.getElementById('floating_doc_comment_box');
        if (paper && floatingBox) {
            paper.addEventListener('mouseup', function(e) {
                if (e.target.closest('.doc-paragraph-comment-btn')) return;

                const selection = window.getSelection();
                if (!selection || selection.isCollapsed) return;

                const selectedText = selection.toString().trim();
                if (selectedText.length < 3) return;

                const anchorNode = selection.anchorNode;
                const parentEl = anchorNode?.nodeType === Node.TEXT_NODE ? anchorNode.parentElement : anchorNode;
                const row = parentEl?.closest?.('.doc-block-row');
                if (!row || !paper.contains(row)) return;

                const range = selection.getRangeAt(0);
                const rect = range.getBoundingClientRect();
                const heading = row.getAttribute('data-heading') || 'Bagian Terpilih';

                openFloatingCommentBoxAt(rect.right, rect.bottom, selectedText, heading);
            });
        }
    });

    function buildCommentCardHtml(c) {
        const kutipanHtml = c.kutipan_teks
            ? `<div class="p-2 mb-2 rounded bg-warning bg-opacity-25 border-start border-3 border-warning fs-8 text-gray-800 fst-italic cursor-pointer" onclick="focusHighlightInDoc('${escapeDocHtml(c.id)}')">"${escapeDocHtml(c.kutipan_teks)}"</div>`
            : '';

        const resolveBtnHtml = window.__skripsiState.isDosenMode && window.__skripsiState.resolveUrlPattern
            ? `<form method="POST" action="${escapeDocHtml(window.__skripsiState.resolveUrlPattern.replace('__COMMENT_ID__', String(c.id)))}" onsubmit="submitToggleResolve(event, ${Number(c.id)})">
                    <input type="hidden" name="_token" value="${escapeDocHtml(window.__skripsiState.csrfToken)}" />
                    <input type="hidden" name="_method" value="PATCH" />
                    <button type="submit" class="btn btn-xs ${c.is_resolved ? 'btn-light' : 'btn-light-success'} fw-bolder fs-9 py-1 px-2 btn-resolve-toggle">
                        <i class="fas ${c.is_resolved ? 'fa-undo' : 'fa-check-double'} me-1"></i>
                        ${c.is_resolved ? 'Buka Lagi' : 'Tandai Selesai'}
                    </button>
               </form>`
            : '';

        const replyFormHtml = `<form method="POST" action="${escapeDocHtml(window.__skripsiState.storeKomentarUrl)}" class="mt-2 pt-2 border-top border-dashed reply-comment-form" id="reply_form_${Number(c.id)}" style="display: none;" onsubmit="submitInlineReply(event, ${Number(c.id)})">
                <input type="hidden" name="_token" value="${escapeDocHtml(window.__skripsiState.csrfToken)}" />
                <input type="hidden" name="parent_id" value="${Number(c.id)}" />
                <input type="hidden" name="lokasi_dokumen" value="${escapeDocHtml(c.lokasi_dokumen)}" />
                <div class="input-group input-group-sm">
                    <input type="text" name="isi_komentar" class="form-control form-control-solid fs-8" placeholder="Tulis balasan komentar..." required />
                    <button type="submit" class="btn btn-primary fw-bolder fs-8">Kirim</button>
                </div>
            </form>`;

        return `
            <div class="p-4 rounded-3 bg-light border border-gray-200 comment-card-item ${c.is_resolved ? 'opacity-75' : ''}" id="comment-card-${Number(c.id)}" data-comment-id="${Number(c.id)}">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <div>
                        <span class="fw-bolder fs-7 text-primary d-block">${escapeDocHtml(c.lokasi_dokumen)}</span>
                        <span class="text-muted fs-9"><strong>${escapeDocHtml(c.pengirim)}</strong> (${escapeDocHtml(c.role_label)}) &bull; ${escapeDocHtml(c.waktu)}</span>
                    </div>
                    <span class="badge badge-light-${escapeDocHtml(c.badge)} fw-bolder px-2 py-1 fs-9 comment-status-badge">${escapeDocHtml(c.status)}</span>
                </div>
                ${kutipanHtml}
                <p class="text-gray-800 fs-7 fw-semibold mb-2">${escapeDocHtml(c.isi_komentar)}</p>
                <div class="ps-3 border-start border-2 border-primary d-flex flex-column gap-2 mb-2 comment-replies-list"></div>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top border-gray-200">
                    <div class="d-flex gap-2">
                        ${c.kutipan_teks ? `<button type="button" class="btn btn-xs btn-light-warning fw-bold fs-9 py-1 px-2" onclick="focusHighlightInDoc('${Number(c.id)}')"><i class="fas fa-highlighter me-1"></i> Lihat di Naskah</button>` : ''}
                        <button type="button" class="btn btn-xs btn-light-primary fw-bold fs-9 py-1 px-2" onclick="toggleReplyForm('${Number(c.id)}')"><i class="fas fa-reply me-1"></i> Balas</button>
                    </div>
                    ${resolveBtnHtml}
                </div>
                ${replyFormHtml}
            </div>
        `;
    }

    async function postJsonComment(payload) {
        const res = await fetch(window.__skripsiState.storeKomentarUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.__skripsiState.csrfToken,
            },
            body: JSON.stringify(payload),
        });
        if (!res.ok) throw new Error('Gagal menyimpan komentar.');
        return res.json();
    }

    function appendCommentToUi(comment) {
        document.getElementById('empty_komentar_placeholder')?.remove();
        window.__skripsiState.komentars.push(comment);
        const list = document.getElementById('doc_comments_list');
        if (list) {
            list.insertAdjacentHTML('beforeend', buildCommentCardHtml(comment));
            list.scrollTop = list.scrollHeight;
        }
        const countBadge = document.getElementById('doc_komentar_count');
        if (countBadge) {
            countBadge.textContent = String(window.__skripsiState.komentars.length);
        }
        renderDocumentHighlights();
        focusHighlightInDoc(comment.id);
    }

    async function submitFloatingComment(e) {
        e.preventDefault();
        const btn = document.getElementById('btn_submit_floating_comment');
        const payload = {
            kutipan_teks: document.getElementById('floating_kutipan_teks').value,
            lokasi_dokumen: document.getElementById('floating_lokasi_dokumen').value,
            isi_komentar: document.getElementById('floating_isi_komentar').value,
            status: document.getElementById('floating_status_select')?.value || 'Catatan',
        };
        if (!payload.isi_komentar.trim()) return;

        if (btn) btn.disabled = true;
        try {
            const data = await postJsonComment(payload);
            closeFloatingCommentBox();
            appendCommentToUi(data.komentar);
        } catch (err) {
            e.target.submit();
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    async function submitManualComment(e) {
        e.preventDefault();
        const payload = {
            lokasi_dokumen: document.getElementById('manual_lokasi_dokumen').value,
            kutipan_teks: document.getElementById('manual_kutipan_teks').value,
            isi_komentar: document.getElementById('manual_isi_komentar').value,
            status: document.getElementById('manual_status_select')?.value || 'Catatan',
        };
        if (!payload.isi_komentar.trim()) return;

        try {
            const data = await postJsonComment(payload);
            e.target.reset();
            window.SimbimUI?.syncSelect2Value('#manual_status_select', 'Perlu Revisi');
            const collapseEl = document.getElementById('collapse_manual_comment_form');
            if (collapseEl && window.bootstrap) {
                bootstrap.Collapse.getOrCreateInstance(collapseEl).hide();
            }
            appendCommentToUi(data.komentar);
        } catch (err) {
            e.target.submit();
        }
    }

    function toggleReplyForm(commentId) {
        const form = document.getElementById('reply_form_' + commentId);
        if (!form) return;
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        if (form.style.display === 'block') {
            form.querySelector('input[name="isi_komentar"]')?.focus();
        }
    }

    async function submitInlineReply(e, parentId) {
        e.preventDefault();
        const input = e.target.querySelector('input[name="isi_komentar"]');
        const lokasi = e.target.querySelector('input[name="lokasi_dokumen"]')?.value || 'Balasan Komentar';
        if (!input || !input.value.trim()) return;

        try {
            const data = await postJsonComment({
                parent_id: parentId,
                lokasi_dokumen: lokasi,
                isi_komentar: input.value.trim(),
            });
            const card = document.getElementById('comment-card-' + parentId);
            const repliesContainer = card?.querySelector('.comment-replies-list');
            if (repliesContainer) {
                const replyObj = data.komentar?.balasan?.length
                    ? data.komentar.balasan[data.komentar.balasan.length - 1]
                    : data.komentar;
                repliesContainer.insertAdjacentHTML('beforeend', `
                    <div class="p-2 rounded bg-white border border-gray-200 fs-8">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bolder text-dark">${escapeDocHtml(replyObj.pengirim)} <span class="badge badge-light-${escapeDocHtml(replyObj.badge_role)} fs-9 ms-1">${escapeDocHtml(replyObj.role_label)}</span></span>
                            <span class="text-muted fs-9">${escapeDocHtml(replyObj.waktu)}</span>
                        </div>
                        <div class="text-gray-700 mt-1">${escapeDocHtml(replyObj.isi_komentar)}</div>
                    </div>
                `);
            }
            input.value = '';
            e.target.style.display = 'none';
        } catch (err) {
            e.target.submit();
        }
    }

    async function submitToggleResolve(e, commentId) {
        e.preventDefault();
        try {
            const res = await fetch(e.target.action, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.__skripsiState.csrfToken,
                },
            });
            if (!res.ok) throw new Error('Gagal memperbarui status.');
            const data = await res.json();
            const updated = data.komentar;

            const idx = window.__skripsiState.komentars.findIndex(k => Number(k.id) === Number(commentId));
            if (idx !== -1) {
                window.__skripsiState.komentars[idx] = updated;
            }
            renderDocumentHighlights();

            const card = document.getElementById('comment-card-' + commentId);
            if (card) {
                card.classList.toggle('opacity-75', Boolean(updated.is_resolved));
                const badge = card.querySelector('.comment-status-badge');
                if (badge) {
                    badge.className = 'badge badge-light-' + escapeDocHtml(updated.badge) + ' fw-bolder px-2 py-1 fs-9 comment-status-badge';
                    badge.textContent = updated.status;
                }
                const btn = card.querySelector('.btn-resolve-toggle');
                if (btn) {
                    btn.className = 'btn btn-xs ' + (updated.is_resolved ? 'btn-light' : 'btn-light-success') + ' fw-bolder fs-9 py-1 px-2 btn-resolve-toggle';
                    btn.innerHTML = `<i class="fas ${updated.is_resolved ? 'fa-undo' : 'fa-check-double'} me-1"></i> ${updated.is_resolved ? 'Buka Lagi' : 'Tandai Selesai'}`;
                }
            }
        } catch (err) {
            e.target.submit();
        }
    }
</script>
@endpush
@endonce

