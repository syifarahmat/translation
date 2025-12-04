<?php namespace Barryvdh\TranslationManager;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ManagerServiceProvider extends ServiceProvider {
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        // Register the config publish path
        $configPath = __DIR__ . '/../config/translation.php';
        $this->mergeConfigFrom($configPath, 'translation');
        $this->publishes([$configPath => config_path('translation.php')], 'config');

        $this->app->singleton('translation', function ($app) {
            $manager = $app->make('Barryvdh\TranslationManager\Manager');
            return $manager;
        });

        $this->app->singleton('command.translation.reset', function ($app) {
            return new Console\ResetCommand($app['translation']);
        });
        $this->commands('command.translation.reset');

        $this->app->singleton('command.translation.import', function ($app) {
            return new Console\ImportCommand($app['translation']);
        });
        $this->commands('command.translation.import');

        $this->app->singleton('command.translation.find', function ($app) {
            return new Console\FindCommand($app['translation']);
        });
        $this->commands('command.translation.find');

        $this->app->singleton('command.translation.export', function ($app) {
            return new Console\ExportCommand($app['translation']);
        });
        $this->commands('command.translation.export');

        $this->app->singleton('command.translation.clean', function ($app) {
            return new Console\CleanCommand($app['translation']);
        });
        $this->commands('command.translation.clean');
	}

    /**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
        $viewPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($viewPath, 'translation');
        $this->publishes([
            $viewPath => base_path('resources/views/vendor/translation'),
        ], 'views');

        $migrationPath = __DIR__.'/../database/migrations';
        $this->publishes([
            $migrationPath => base_path('database/migrations'),
        ], 'migrations');

        $this->loadRoutesFrom(__DIR__.'/routes.php');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('translation',
            'command.translation.reset',
            'command.translation.import',
            'command.translation.find',
            'command.translation.export',
            'command.translation.clean'
        );
	}

}
