<?php namespace Ravoiu\Tassets;

use Illuminate\Support\ServiceProvider;
use Ravoiu\Tassets\Console\Command\ClearAssetsCommand;
use Ravoiu\Tassets\Console\Command\MakeAssetsCommand;

class TassetsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * The laravel version number. This is used for the install commands
	 *
	 * @var int
	 */
	protected $laravelVersion = 5;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// -- Laravel 5
		$this->laravelVersion = 5;

		// -- Package separator
		$this->packageSeparator = '::';

		// -- Handle config file
		// -- -- Get path
		$config_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
		// -- -- Merge from config
		$this->mergeConfigFrom($config_file, 'tassets');
		// -- -- Tell laravel that we publish this file
		$this->publishes([
			                 $config_file => config_path('tassets.php')
		                 ], 'config');

		// Handle blade extensions
		$this->bladeExtensions();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Make assets command
		$this->app->bind('tassets.make-assets', function() {
			return new MakeAssetsCommand($this->getConfig());
		});

		// Clear assets command
		$this->app->singleton('tassets.clear-assets', function() {
			return new ClearAssetsCommand($this->getConfig());
		});

		// Register commands
		$this->commands([
			                'tassets.make-assets',
			                'tassets.clear-assets'
		                ]);
	}

	/**
	 * Get config
	 * @return array
	 */
	public function getConfig()
	{
		return \Config::get("tassets");
	}

	/**
	 * Handle blade extensions
	 */
	protected function bladeExtensions()
	{
		// Potion asset url
		\Blade::extend(function($view, $compiler)
		{
			$pattern = $compiler->createMatcher('tassets_asset_url');
			return preg_replace($pattern, '$1<?php echo(\Ravoiu\Tassets\BladeHelpers::assetUrl$2); ?>', $view);
		});

		// Potion Css
		\Blade::extend(function($view, $compiler)
		{
			$pattern = $compiler->createMatcher('tassets_asset_css');
			return preg_replace($pattern, '$1<?php echo(\Ravoiu\Tassets\BladeHelpers::assetCss$2); ?>', $view);
		});

		// Potion Js
		\Blade::extend(function($view, $compiler)
		{
			$pattern = $compiler->createMatcher('tassets_asset_js');
			return preg_replace($pattern, '$1<?php echo(\Ravoiu\Tassets\BladeHelpers::assetJs$2); ?>', $view);
		});

		// Potion Img
		\Blade::extend(function($view, $compiler)
		{
			$pattern = $compiler->createMatcher('tassets_asset_img');
			return preg_replace($pattern, '$1<?php echo(\Ravoiu\Tassets\BladeHelpers::assetImg$2); ?>', $view);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['tassets'];
	}
}
