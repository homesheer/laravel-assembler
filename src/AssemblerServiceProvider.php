<?php

namespace LaravelAssembler;

use Illuminate\Support\ServiceProvider;

class AssemblerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/assembler.php' => config_path('assembler.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/assembler.php';
        $this->mergeConfigFrom($configPath, 'assembler');
    }

}
