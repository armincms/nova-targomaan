<?php

namespace Armincms\Fields;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;

class TargomaanServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Nova::script('nova-targomaan', __DIR__.'/../dist/js/field.js');

        $this->app->singleton('nova-targomaan.locales', function () {
            return [
                app()->getLocale() => strtoupper(app()->getLocale()),
            ];
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function when()
    {
        return [
            \Laravel\Nova\Events\ServingNova::class,
        ];
    }
}
