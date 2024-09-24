<?php

namespace PhpMonsters\Sasscription\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use PhpMonsters\Sasscription\Events\SubscriptionCanceled;
use PhpMonsters\Sasscription\Events\SubscriptionRenewed;
use PhpMonsters\Sasscription\Events\SubscriptionScheduled;
use PhpMonsters\Sasscription\Events\SubscriptionStarted;
use PhpMonsters\Sasscription\Events\SubscriptionSuppressed;
use PhpMonsters\Sasscription\Models\Concerns\ExpiresAndHasGraceDays;
use PhpMonsters\Sasscription\Models\Concerns\Starts;
use PhpMonsters\Sasscription\Models\Concerns\Suppresses;
use PhpMonsters\Sasscription\Models\Scopes\ExpiringWithGraceDaysScope;
use PhpMonsters\Sasscription\Models\Scopes\StartingScope;
use PhpMonsters\Sasscription\Models\Scopes\SuppressingScope;

class Subscription extends Model
{
    use ExpiresAndHasGraceDays;
    use HasFactory;
    use SoftDeletes;
    use Starts;
    use Suppresses;

    protected $dates = [
        'canceled_at',
    ];

    protected $fillable = [
        'canceled_at',
        'expired_at',
        'grace_days_ended_at',
        'started_at',
        'suppressed_at',
        'was_switched',
    ];

    public function plan()
    {
        return $this->belongsTo(config('sasscription.models.plan'));
    }

    public function renewals()
    {
        return $this->hasMany(config('sasscription.models.subscription_renewal'));
    }

    public function subscriber()
    {
        return $this->morphTo('subscriber');
    }

    public function scopeNotActive(Builder $query)
    {
        return $query->withoutGlobalScopes([
                ExpiringWithGraceDaysScope::class,
                StartingScope::class,
                SuppressingScope::class,
            ])
            ->where(function (Builder $query) {
                $query->where(fn (Builder $query) => $query->onlyExpired())
                    ->orWhere(fn (Builder $query) => $query->onlyNotStarted())
                    ->orWhere(fn (Builder $query) => $query->onlySuppressed());
            });
    }

    public function scopeCanceled(Builder $query)
    {
        return $query->whereNotNull('canceled_at');
    }

    public function scopeNotCanceled(Builder $query)
    {
        return $query->whereNull('canceled_at');
    }

    public function markAsSwitched(): self
    {
        return $this->fill([
            'was_switched' => true,
        ]);
    }

    public function start(?Carbon $startDate = null): self
    {
        $startDate = $startDate ?: today();

        $this->fill(['started_at' => $startDate])
            ->save();

        if ($startDate->isToday()) {
            event(new SubscriptionStarted($this));
        } elseif ($startDate->isFuture()) {
            event(new SubscriptionScheduled($this));
        }

        return $this;
    }

    public function renew(?Carbon $expirationDate = null): self
    {
        $this->renewals()->create([
            'renewal' => true,
            'overdue' => $this->isOverdue,
        ]);

        $expirationDate = $this->getRenewedExpiration($expirationDate);

        $this->update([
            'expired_at' => $expirationDate,
        ]);

        event(new SubscriptionRenewed($this));

        return $this;
    }

    public function cancel(?Carbon $cancelDate = null): self
    {
        $cancelDate = $cancelDate ?: now();

        $this->fill(['canceled_at' => $cancelDate])
            ->save();

        event(new SubscriptionCanceled($this));

        return $this;
    }

    public function suppress(?Carbon $suppressation = null)
    {
        $suppressationDate = $suppressation ?: now();

        $this->fill(['suppressed_at' => $suppressationDate])
            ->save();

        event(new SubscriptionSuppressed($this));

        return $this;
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->grace_days_ended_at) {
            return $this->expired_at->isPast()
                and $this->grace_days_ended_at->isPast();
        }

        return $this->expired_at->isPast();
    }

    private function getRenewedExpiration(?Carbon $expirationDate = null)
    {
        if (! empty($expirationDate)) {
            return $expirationDate;
        }

        if ($this->isOverdue) {
            return $this->plan->calculateNextRecurrenceEnd();
        }

        return $this->plan->calculateNextRecurrenceEnd($this->expired_at);
    }
}
