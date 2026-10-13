<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        // Langsung arahkan ke view pages.index
        if (view()->exists('pages.index')) {
            return view('pages.index');
        }

        // Jika halaman utama tidak ada, coba panggil berdasarkan theme option
        $view = theme()->getOption('page', 'view');
        if (view()->exists('pages.'.$view)) {
            return view('pages.'.$view);
        }

        return redirect('/');
    }

    /**
     * Temporary function to replace icon duotone
     */
    public function replaceIcons()
    {
        $fileContent = file_get_contents(public_path('icon_replacement.txt'));
        $lines = explode("\n", $fileContent);

        $patterns = [];
        $replacements = [];
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            $el = explode(' ', $line);
            if (isset($el[0], $el[1])) {
                $patterns[] = trim($el[0]);
                $replacements[] = trim($el[1]);
            }
        }

        $files = File::allFiles(resource_path());
        $filtered = array_filter($files, function ($str) {
            return strpos($str, '.php') !== false;
        });

        foreach ($filtered as $file) {
            $bladeFileContent = file_get_contents($file->getPathname());
            $bladeFileContent = str_replace($patterns, $replacements, $bladeFileContent);
            file_put_contents($file->getPathname(), $bladeFileContent);
        }
    }
}
