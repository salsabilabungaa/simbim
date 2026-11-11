<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

abstract class Controller
{
    /**
     * Paginasi koleksi atau array data tabel secara konsisten menggunakan ukuran halaman tetap.
     *
     * @param  array<int|string, mixed>|Collection<int|string, mixed>  $items
     */
    protected function paginateItems(
        array|Collection $items,
        int $perPage = 6,
        string $pageName = 'page'
    ): LengthAwarePaginator {
        $collection = $items instanceof Collection ? $items->values() : collect(array_values($items));
        $currentPage = Paginator::resolveCurrentPage($pageName);
        $total = $collection->count();
        $results = $collection->forPage($currentPage, $perPage)->values();

        return (new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        ))->withQueryString();
    }
}
