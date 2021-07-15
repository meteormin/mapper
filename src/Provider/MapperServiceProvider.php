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
            __DIR__ . '/../config/mapper.php' => config_path('mapper.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../config/make_class.php' => config_path('make_class.php'),
        ], 'config');

        if (!file_exists(base_path('stubs'))) {
            mkdir(base_path('stubs'));
        }

        $this->publishes([
            __DIR__ . '/../Stubs/Map.stub' => base_path('stubs/Map.stub')
        ]);

        $this->commands(\Miniyus\Mapper\Commands\GenerateMap::class);
    }
}