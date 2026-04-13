<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // FIXED: register the broadcast auth route.
        Broadcast::routes([
            'middleware' => ['web', 'auth'],
        ]);

        // FIXED: load channel authorization rules.
        require base_path('routes/channels.php');
    }
}
