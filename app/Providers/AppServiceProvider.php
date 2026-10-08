<?php

namespace App\Providers;

use App\Events\OrderCompleted;
use App\Listeners\SendOrderCompletedEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        Event::listen(
            OrderCompleted::class,
            SendOrderCompletedEmail::class,
        );
    }
}
