<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapV1FrontendRoutes();
        $this->mapV1BackendRoutes();
        $this->mapV1CommonRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * 定义V1版本的前台API接口路由
     *
     * @return void
     */
    protected function mapV1FrontendRoutes()
    {
        Route::namespace($this->namespace)
            ->group(base_path('routes/v1/frontend.php'));
    }

    /**
     * 定义V1版本的后台API接口路由
     *
     * @return void
     */
    protected function mapV1BackendRoutes()
    {
        Route::namespace($this->namespace)
            ->group(base_path('routes/v1/backend.php'));
    }

    /**
     * 定义V1版本的公用API接口路由
     *
     * @return void
     */
    protected function mapV1CommonRoutes()
    {
        Route::namespace($this->namespace)
            ->group(base_path('routes/v1/commend.php'));
    }
}
