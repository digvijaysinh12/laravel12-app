<?php

namespace App\View\Components\User;

use Illuminate\View\Component;
use Illuminate\View\View;

class Navbar extends Component
{
    public function render(): View
    {
        return view('user.components.navbar');
    }
}
