<?php

namespace PhpMonsters\Sasscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpMonsters\Sasscription\Models\Concerns\Expires;

class FeatureConsumption extends Model
{
    use Expires;
    use HasFactory;

    protected $fillable = [
        'consumption',
        'expired_at',
    ];

    public function feature()
    {
        return $this->belongsTo(config('sasscription.models.feature'));
    }

    public function subscriber()
    {
        return $this->morphTo('subscriber');
    }
}
