<?php

namespace App\Livewire\Public;

use App\Models\Species;
use Livewire\Component;

class LifeCycleCalendar extends Component
{
    public Species $species;

    public function mount(Species $species)
    {
        $this->species = $species;
    }

    public function render()
    {
        // Prepare calendar data from generations
        $months = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
        $calendarData = [];

        if ($this->species->generations && count($this->species->generations) > 0) {
            foreach ($this->species->generations as $generation) {
                $generationData = [
                    'number' => $generation->generation_number,
                    'months' => []
                ];

                for ($month = 1; $month <= 12; $month++) {
                    $monthData = [
                        'month' => $month,
                        'label' => $months[$month - 1],
                        'types' => []
                    ];

                    // Check flight months
                    if ($generation->flight_start_month && $generation->flight_end_month) {
                        if ($generation->flight_start_month <= $generation->flight_end_month) {
                            // Normal range (e.g., April to July)
                            if ($month >= $generation->flight_start_month && $month <= $generation->flight_end_month) {
                                $monthData['types'][] = 'flight';
                            }
                        } else {
                            // Wrapping range (e.g., November to March)
                            if ($month >= $generation->flight_start_month || $month <= $generation->flight_end_month) {
                                $monthData['types'][] = 'flight';
                            }
                        }
                    }

                    // Check pupation months
                    if ($generation->pupation_start_month && $generation->pupation_end_month) {
                        if ($generation->pupation_start_month <= $generation->pupation_end_month) {
                            // Normal range
                            if ($month >= $generation->pupation_start_month && $month <= $generation->pupation_end_month) {
                                $monthData['types'][] = 'pupation';
                            }
                        } else {
                            // Wrapping range
                            if ($month >= $generation->pupation_start_month || $month <= $generation->pupation_end_month) {
                                $monthData['types'][] = 'pupation';
                            }
                        }
                    }

                    $generationData['months'][] = $monthData;
                }

                $calendarData[] = $generationData;
            }
        }

        return view('livewire.public.life-cycle-calendar', [
            'species' => $this->species,
            'calendarData' => $calendarData,
            'months' => $months,
        ]);
    }
}
