<?php

namespace PhpMonsters\Sasscription\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use PhpMonsters\Sasscription\Models\Feature;
use PhpMonsters\Sasscription\Models\FeatureConsumption;

class FeatureConsumed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public $subscriber,
        public Feature $feature,
        public FeatureConsumption $featureConsumption,
    ) {
        //
    }
}
