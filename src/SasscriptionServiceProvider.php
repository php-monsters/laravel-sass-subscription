<?php

namespace PhpMonsters\Sasscription;

use Illuminate\Support\ServiceProvider;

class SasscriptionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sasscription.php', 'sasscription');

        if (! config('sasscription.database.cancel_migrations_autoloading')) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        $this->publishes([
            __DIR__ . '/../config/sasscription.php' => config_path('sasscription.php'),
        ], 'sasscription-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'sasscription-migrations');
    }
}
