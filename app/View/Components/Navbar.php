<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Navbar extends Component
{
    /**
     * Create a new component instance.
     */
    public $user;
    public $backUrl;
    public $hamburger;

    public function __construct($backUrl = false, $hamburger = false)
    {
        $this->user      = Auth::user();
        $this->backUrl   = $backUrl;
        $this->hamburger = $hamburger;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navbar');
    }
}