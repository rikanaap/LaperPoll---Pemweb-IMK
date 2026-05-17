<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class cardBahanLanding extends Component
{
    /**
     * Create a new component instance.
     */
    public $bahan, $index;
    public function __construct($bahan, $index)
    {
        $this->bahan = $bahan;
        $this->index = $index;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card-bahan-landing');
    }
}
