<?php

namespace PhpMonsters\Sasscription\Models;

use Illuminate\Database\Eloquent\Model;
use PhpMonsters\Sasscription\Models\Concerns\Expires;

class FeatureTicket extends Model
{
    use Expires;

    protected $fillable = [
        'charges',
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
