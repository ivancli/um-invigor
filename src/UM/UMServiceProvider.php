<?php namespace Invigor\UM;

/**
 * This file is part of UM,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Invigor\UM
 */

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class UMServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

//    protected $repoBindings = array(
//        'UM\UserRepositoryContract' => 'UM\UserRepository',
//        'UM\GroupRepositoryContract' => 'UM\GroupRepository',
//        'UM\RoleRepositoryContract' => 'UM\RoleRepository',
//        'UM\PermissionRepositoryContract' => 'UM\PermissionRepository'
//    );

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
//        $this->publishes([
//            __DIR__ . '/../config/config.php' => config_path('um.php'),
//        ]);
        $this->publishes([
            __DIR__.'/../views/user' => resource_path('views/um/user'),
            __DIR__.'/../views/group' => resource_path('views/um/group'),
            __DIR__.'/../views/role' => resource_path('views/um/role'),
            __DIR__.'/../views/permission' => resource_path('views/um/permission'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../views/', 'um');

        // Register commands
        $this->commands('command.um.migration');
        $this->commands('command.um.controller');
        $this->commands('command.um.seeder');

        // Register blade directives
        $this->bladeDirectives();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRoute();

        $this->registerLaravelCollective();

        $this->registerUM();

        $this->registerCommands();

        $this->mergeConfig();

//        foreach($this->repoBindings as $contract=>$repo){
//            $this->app->bind()
//        }
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    private function bladeDirectives()
    {
        // Call to UM::hasRole
        \Blade::directive('role', function ($expression) {
            return "<?php if (\\UM::hasRole{$expression}) : ?>";
        });

        \Blade::directive('endrole', function ($expression) {
            return "<?php endif; // UM::hasRole ?>";
        });

        // Call to UM::can
        \Blade::directive('permission', function ($expression) {
            return "<?php if (\\UM::can{$expression}) : ?>";
        });

        \Blade::directive('endpermission', function ($expression) {
            return "<?php endif; // UM::can ?>";
        });

        // Call to UM::ability
        \Blade::directive('ability', function ($expression) {
            return "<?php if (\\UM::ability{$expression}) : ?>";
        });

        \Blade::directive('endability', function ($expression) {
            return "<?php endif; // UM::ability ?>";
        });
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerUM()
    {
        $this->app->bind('um', function ($app) {
            return new UM($app);
        });

        $this->app->alias('um', 'Invigor\UM\UM');
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->singleton('command.um.migration', function ($app) {
            return new MigrationCommand();
        });
        $this->app->singleton('command.um.controller', function ($app) {
            return new ControllersCommand();
        });
        $this->app->singleton('command.um.seeder', function ($app) {
            return new SeederCommand();
        });
    }

    public function registerLaravelCollective()
    {
        $this->app->register('Collective\Html\HtmlServiceProvider');
        $loader = AliasLoader::getInstance();
        $loader->alias('Form', 'Collective\Html\FormFacade');
        $loader->alias('Html', 'Collective\Html\HtmlFacade');
    }

    public function registerRoute()
    {
        include __DIR__.'/routes.php';
    }

    /**
     * Merges user's and um's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php', 'um'
        );
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.um.migration',
            'command.um.controller',
            'command.um.seeder',
        ];
    }
}
