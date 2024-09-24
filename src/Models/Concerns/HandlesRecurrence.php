<?php

namespace PhpMonsters\Sasscription\Models\Concerns;

use Illuminate\Support\Carbon;
use PhpMonsters\Sasscription\Enums\PeriodicityType;

trait HandlesRecurrence
{
    public function calculateNextRecurrenceEnd(Carbon|string $start = null): Carbon
    {
        if (empty($start)) {
            $start = now();
        }

        if (is_string($start)) {
            $start = Carbon::parse($start);
        }

        $recurrences = PeriodicityType::getDateDifference(from: now(), to: $start, unit: $this->periodicity_type);
        $expirationDate = $start->copy()->add($this->periodicity_type, $this->periodicity + $recurrences);

        return $expirationDate;
    }
}
