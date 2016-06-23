<?php

namespace WI\Media;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class MediaServiceProvider extends ServiceProvider
{
    
	
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    #protected $defer = true;
	
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

		if (!$this->app->routesAreCached()) {
			$this->setupRoutes($this->app->router);
		}

	    if (is_dir(base_path() . '/resources/views/admin/media')) {
		    //load from resource
		    $this->loadViewsFrom(base_path() . '/resources/views/admin/media', 'media');
	    } else {
		    //load from package
		    $this->loadViewsFrom(__DIR__.'/views', 'media');
	    }

	    $this->publishes([
		    __DIR__.'/views' => base_path('resources/views/admin/media')
	    ],'media');
    }
	
	/**
		 * Define the routes for the application.
		 *
		 * @param  \Illuminate\Routing\Router  $router
		 * @return void
		 */
		public function setupRoutes(Router $router)
		{
			/*$router->group([
				'namespace' => 'WI\User'

			],function($router){
					require __DIR__.'/routes.php';
			});
			*/

			$router->group([
				//'namespace' => 'WI\Dashboard',
				'namespace' => 'WI\Media',	// Controllers Within The "WI\Dashboard" Namespace
				'as' => 'admin::',		// Route named "admin::
				//'prefix' => 'backStage',	// Matches The "/admin" URL
				'prefix' => config('wi.dashboard.admin_prefix'),
				'middleware' => ['web','auth']	// Use Auth Middleware
			],
				function($router)
				{
					require __DIR__.'/routes.php';
				}
			);


		}

    /**
     * Register the application services.
     * https://laracasts.com/discuss/channels/general-discussion/how-to-move-my-controllers-into-a-seperate-package-folder
     * @return void
     */
    public function register()
    {
		$this->app->bind(
			'WI\Media\Repositories\Media\MediaRepositoryInterface',
			'WI\Media\Repositories\Media\DbMediaRepository'
		);    }
}
