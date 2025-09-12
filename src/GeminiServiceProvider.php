<?php

namespace HosseinHezami\LaravelGemini;

use Illuminate\Support\ServiceProvider;
use HosseinHezami\LaravelGemini\Console\ModelsCommand;
use HosseinHezami\LaravelGemini\Factory\ProviderFactory;
use HosseinHezami\LaravelGemini\Gemini;

class GeminiServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/gemini.php', 'gemini');
		
		$this->app->singleton(ProviderFactory::class, function ($app) {
            return new ProviderFactory();
        });

        $this->app->singleton('gemini', function ($app) {
            return new Gemini($app->make(ProviderFactory::class));
        });
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/Config/gemini.php' => config_path('gemini.php'),
        ], ['gemini-config']);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ModelsCommand::class,
            ]);
        }
    }
}