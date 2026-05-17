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
        $this->resep->cook_duration = $this->formatDuration($resep->cook_duration);
    }

    public function formatDuration($duration)
    {
        // Jika durasi kosong atau null, kembalikan teks default
        if (empty($duration)) {
            return '-';
        }

        // Parse format 'H:i:s' dari MySQL (contoh: 01:30:00)
        $parsedTime = Carbon::createFromFormat('H:i:s', $duration);

        $hours = $parsedTime->hour;
        $minutes = $parsedTime->minute;

        $result = [];

        if ($hours > 0) {
            $result[] = $hours . 'h';
        }

        if ($minutes > 0) {
            $result[] = $minutes . 'm';
        }

        if (empty($result)) {
            return '<1m';
        }

        // Menggabungkan jam dan menit dengan spasi (contoh: "1 jam 30 menit")
        return implode(' ', $result);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card-resep-landing');
    }
}
