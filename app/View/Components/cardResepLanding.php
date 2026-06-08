<?php

namespace App\View\Components;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class cardResepLanding extends Component
{
    /**
     * Create a new component instance.
     */
    public $resep;
    public function __construct($resep, $index)
    {
        $this->resep = $resep;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card-resep-landing');
    }
}
