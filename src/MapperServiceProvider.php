<?php

namespace Miniyus\Mapper;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;


class MapperServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MapperInterface::class, function (Application $app) {
            $configRepository = $app->get('config');

            $mapperConfig = $configRepository->get('mapper');

            return Mapper::newInstance($mapperConfig);
        });

        $this->app->alias(MapperInterface::class, 'mapper');
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
            __DIR__ . '/stubs/Map.stub' => base_path('stubs/Map.stub')
        ]);

        $this->commands(\Miniyus\Mapper\Console\GenerateMap::class);
    }
}