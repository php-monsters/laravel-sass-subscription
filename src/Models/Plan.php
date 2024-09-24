<?php

namespace PhpMonsters\Sasscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use PhpMonsters\Sasscription\Models\Concerns\HandlesRecurrence;

class Plan extends Model
{
    use HandlesRecurrence;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'grace_days',
        'name',
        'periodicity_type',
        'periodicity',
    ];

    public function features()
    {
        return $this->belongsToMany(config('sasscription.models.feature'))
            ->using(config('sasscription.models.feature_plan'))
            ->withPivot(['charges']);
    }

    public function subscriptions()
    {
        return $this->hasMany(config('sasscription.models.subscription'));
    }

    public function calculateGraceDaysEnd(Carbon $recurrenceEnd)
    {
        return $recurrenceEnd->copy()->addDays($this->grace_days);
    }

    public function getHasGraceDaysAttribute()
    {
        return ! empty($this->grace_days);
    }
}
