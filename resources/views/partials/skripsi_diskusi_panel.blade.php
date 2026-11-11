@php
    $diskusi = $diskusi ?? [];
    $storeDiskusiUrl = $storeDiskusiUrl ?? '#';
    $roleMode = $roleMode ?? 'dosen';
    $panelTitle = $panelTitle ?? 'Kolom Diskusi Dua Arah';
    $panelSubtitle = $panelSubtitle ?? 'Tersinkronisasi langsung antara Dosen Pembimbing & Mahasiswa';
    $chatContainerId = $chatContainerId ?? 'chat_thread_container';
    $canPostDiskusi = ! empty($storeDiskusiUrl) && $storeDiskusiUrl !== '#';
@endphp

<div class="card card-custom bg-body border border-gray-200 shadow-sm" id="panel_diskusi_dua_arah">
    <div class="card-header border-0 pt-5 pb-3">
        <div class="card-title flex-column align-items-start">
            <h4 class="fw-bolder text-dark fs-5 mb-0">
                <i class="fas fa-comments text-success me-2"></i>
                {{ $panelTitle }}
                <span class="badge badge-light-success fw-bolder ms-2" id="diskusi_count_badge">{{ count($diskusi) }}</span>
            </h4>
            <span class="text-muted fs-8 mt-1">{{ $panelSubtitle }}</span>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="d-flex flex-column gap-3 scroll-y mh-350px pe-2 mb-4" id="{{ $chatContainerId }}">
            @forelse ($diskusi as $chat)
                @php
                    $isCurrentActor = ($roleMode === 'dosen' && ($chat['peran_pengirim'] ?? '') === 'dosen')
                        || ($roleMode === 'mahasiswa' && ($chat['peran_pengirim'] ?? '') === 'mahasiswa');
                @endphp
                <div class="p-4 rounded-3 border {{ $isCurrentActor ? 'bg-light-primary border-primary border-dashed ms-6' : 'bg-light border-gray-200 me-6' }}">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="fw-bolder fs-7 text-dark">{{ $chat['pengirim'] }}</span>
                            <span class="badge badge-light-{{ $chat['badge_role'] }} fw-bolder fs-9">{{ $chat['role'] }}</span>
                        </div>
                        <span class="text-muted fs-9">{{ $chat['waktu'] }}</span>
                    </div>
                    <p class="text-gray-800 fs-7 mb-0">
                        {{ $chat['pesan'] }}
                    </p>
                </div>
            @empty
                <div class="text-center text-muted py-6 fs-7" id="empty_diskusi_placeholder">
                    Belum ada pesan diskusi. Mulai diskusi revisi dua arah di bawah ini.
                </div>
            @endforelse
        </div>

        <form id="form_send_chat" method="POST" action="{{ $storeDiskusiUrl }}" onsubmit="sendDiskusiPesan(event)">
            @csrf
            <div class="input-group input-group-solid">
                <input type="text"
                       name="pesan"
                       id="input_pesan_diskusi"
                       class="form-control form-control-solid fs-7"
                       placeholder="Tulis pesan diskusi revisi skripsi..."
                       required />
                <button type="submit" class="btn btn-primary fw-bolder px-5" id="btn_submit_diskusi">
                    <i class="fas fa-paper-plane me-1"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</div>

@once
@push('scripts')
<script>
    window.__diskusiConfig = {
        storeUrl: @json($storeDiskusiUrl),
        canPost: @json($canPostDiskusi),
        containerId: @json($chatContainerId),
        fallbackNama: @json(auth()->user()?->name ?? ($roleMode === 'dosen' ? 'Dosen Pembimbing' : 'Andi Pratama')),
        fallbackRoleLabel: @json($roleMode === 'dosen' ? 'Dosen Pembimbing' : 'Mahasiswa'),
        fallbackBadge: @json($roleMode === 'dosen' ? 'primary' : 'info'),
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    };

    function escapeDiskusiHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function appendDiskusiBubble(chat) {
        document.getElementById('empty_diskusi_placeholder')?.remove();
        const container = document.getElementById(window.__diskusiConfig.containerId);
        if (!container) return;

        const html = `
            <div class="p-4 rounded-3 bg-light-primary border border-primary border-dashed ms-6">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="fw-bolder fs-7 text-dark">${escapeDiskusiHtml(chat.pengirim)}</span>
                        <span class="badge badge-light-${escapeDiskusiHtml(chat.badge_role)} fw-bolder fs-9">${escapeDiskusiHtml(chat.role)}</span>
                    </div>
                    <span class="text-muted fs-9">${escapeDiskusiHtml(chat.waktu)}</span>
                </div>
                <p class="text-gray-800 fs-7 mb-0">${escapeDiskusiHtml(chat.pesan)}</p>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        container.scrollTop = container.scrollHeight;

        const badge = document.getElementById('diskusi_count_badge');
        if (badge) {
            badge.textContent = String((parseInt(badge.textContent || '0', 10) || 0) + 1);
        }
    }

    async function sendDiskusiPesan(e) {
        e.preventDefault();
        const input = document.getElementById('input_pesan_diskusi');
        const btn = document.getElementById('btn_submit_diskusi');
        if (!input || !input.value.trim()) return;

        const pesanText = input.value.trim();

        if (!window.__diskusiConfig.canPost) {
            appendDiskusiBubble({
                pengirim: window.__diskusiConfig.fallbackNama,
                role: window.__diskusiConfig.fallbackRoleLabel,
                badge_role: window.__diskusiConfig.fallbackBadge,
                waktu: 'Baru saja',
                pesan: pesanText,
            });
            input.value = '';
            return;
        }

        if (btn) btn.disabled = true;
        try {
            const res = await fetch(window.__diskusiConfig.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.__diskusiConfig.csrfToken,
                },
                body: JSON.stringify({ pesan: pesanText }),
            });
            if (!res.ok) throw new Error('Gagal mengirim pesan diskusi.');
            const data = await res.json();
            appendDiskusiBubble(data.pesan);
            input.value = '';
        } catch (err) {
            e.target.submit();
        } finally {
            if (btn) btn.disabled = false;
        }
    }
</script>
@endpush
@endonce
