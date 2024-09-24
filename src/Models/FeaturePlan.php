<?php

namespace PhpMonsters\Sasscription\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FeaturePlan extends Pivot
{
    protected $fillable = [
        'charges',
    ];

    public function feature()
    {
        return $this->belongsTo(config('sasscription.models.feature'));
    }

    public function plan()
    {
        return $this->belongsTo(config('sasscription.models.plan'));
    }
}
