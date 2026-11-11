@if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator && $paginator->total() > 0)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-4">
        <span class="text-muted fs-7">
            Menampilkan {{ $paginator->firstItem() ?? 0 }} sampai {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} {{ $label ?? 'data' }}
        </span>
        <div>
            {{ $paginator->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif

