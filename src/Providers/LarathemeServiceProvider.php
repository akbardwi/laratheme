<?php

namespace Akbardwi\Laratheme\Providers;

use App;
use File;
use Illuminate\Support\ServiceProvider;
use Akbardwi\Laratheme\Console\ThemeListCommand;
use Akbardwi\Laratheme\Contracts\ThemeContract;
use Akbardwi\Laratheme\Managers\Theme;

class LarathemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $symlink = config('theme.symlink_path', public_path('themes'));
        if (config('theme.symlink') && File::exists(config('theme.theme_path')) && !is_link($symlink)) {
            App::make('files')->link(config('theme.theme_path'), $symlink);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishConfig();
        $this->registerTheme();
        $this->registerHelper();
        $this->consoleCommand();
        $this->registerMiddleware();
        $this->loadViewsFrom(__DIR__.'/../Views', 'laratheme');
    }

    /**
     * Add Theme Types Middleware.
     *
     * @return void
     */
    public function registerMiddleware()
    {
        if (config('theme.types.enable')) {
            $themeTypes = config('theme.types.middleware');
            foreach ($themeTypes as $middleware => $themeName) {
                $this->app['router']->aliasMiddleware($middleware, '\Akbardwi\Laratheme\Middleware\RouteMiddleware:'.$themeName);
            }
        }
    }

    /**
     * Register theme required components .
     *
     * @return void
     */
    public function registerTheme()
    {
        $this->app->singleton(ThemeContract::class, function ($app) {
            $theme = new Theme($app, $this->app['view']->getFinder(), $this->app['config'], $this->app['translator']);

            return $theme;
        });
    }

    /**
     * Register All Helpers.
     *
     * @return void
     */
    public function registerHelper()
    {
        foreach (glob(__DIR__.'/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Publish config file.
     *
     * @return void
     */
    public function publishConfig()
    {
        $configPath = realpath(__DIR__.'/../../config/theme.php');

        $this->publishes([
            $configPath => config_path('theme.php'),
        ]);

        $this->mergeConfigFrom($configPath, 'theme');
    }

    /**
     * Add Commands.
     *
     * @return void
     */
    public function consoleCommand()
    {
        $this->registerThemeGeneratorCommand();
        $this->registerThemeListCommand();
        $this->registerThemeRemoveCommand();
        // Assign commands.
        $this->commands(
            'theme.create',
            'theme.list',
            'theme.remove'
        );
    }

    /**
     * Register generator command.
     *
     * @return void
     */
    public function registerThemeGeneratorCommand()
    {
        $this->app->singleton('theme.create', function ($app) {
            return new \Akbardwi\Laratheme\Console\ThemeGeneratorCommand($app['config'], $app['files']);
        });
    }

    /**
     * Register theme list command.
     *
     * @return void
     */
    public function registerThemeListCommand()
    {
        $this->app->singleton('theme.list', ThemeListCommand::class);
    }

    /**
     * Register theme remove command.
     *
     * @return void
     */
    public function registerThemeRemoveCommand()
    {
        $this->app->singleton('theme.remove', function ($app) {
            return new \Akbardwi\Laratheme\Console\ThemeRemoveCommand($app['config'], $app['files']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
