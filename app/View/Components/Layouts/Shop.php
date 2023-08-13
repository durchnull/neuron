<?php

namespace App\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Shop extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.layouts.shop');
    }
}
