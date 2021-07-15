<?php

namespace Miniyus\Mapper\Provider;

use Illuminate\Support\ServiceProvider;
use Miniyus\Mapper\Mapper;
use Miniyus\Mapper\MapperInterface;

class MapperServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MapperInterface::class, Mapper::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../src/config/mapper.php' => config_path('mapper.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../src/config/make_class.php' => config_path('make_class.php'),
        ], 'config');

        if (!file_exists(base_path('app/Stubs'))) {
            mkdir(base_path('app/Stubs'));
        }

        $this->publishes([
            __DIR__ . '/../src/config/make_class.php' => base_path('app/Stubs')
        ], 'app');

        $this->commands(\Miniyus\Mapper\Commands\GenerateMap::class);
    }
}