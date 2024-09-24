<?php

namespace PhpMonsters\Sasscription\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use PhpMonsters\Sasscription\Models\Subscription;

class SubscriptionRenewed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Subscription $subscription,
    ) {
        //
    }
}
