<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AuthLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return View
     */
    public function render()
    {
        return view('auth.layout', ['comment' => 'Sign-in', 'wrapperClass' => 'w-lg-500px']);
    }
}
