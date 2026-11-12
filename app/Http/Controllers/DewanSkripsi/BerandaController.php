<?php

namespace App\Http\Controllers\DewanSkripsi;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BerandaController extends DewanSkripsiController
{
    public function index(Request $request): View
    {
        return $this->dashboard($request);
    }
}
