<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ResepCard extends Component
{
    /**
     * Create a new component instance.
     */
    public $resep;
    public function __construct($resep)
    {
        $this->resep = $resep;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.resep-card');
    }
}
