<?php
/**
 * This file is part of the NextDeveloper Generator library.
 *
 * (c) Harun Baris Bulut <baris.bulut@plusclouds.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace NextDeveloper\Generator;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class CoreServiceProvider.
 *
 * @package PlusClouds\Core
 */
class GeneratorServiceProvider extends AbstractServiceProvider {
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function boot() {
        $this->publishes([
            __DIR__.'/../config/generator.php' => config_path('generator.php'),
        ], 'config');

        $this->bootChannelRoutes();

        $this->loadViewsFrom($this->dir.'/../resources/views', 'Generator');

//        $this->bootErrorHandler();
        $this->bootModelBindings();

        $this->bootEvents();
    }

    /**
     * @return void
     */
    public function register() {
        $this->registerHelpers();
        $this->registerMiddlewares('generator');
        $this->registerRoutes();
        $this->registerCommands();

        $this->mergeConfigFrom(__DIR__.'/../config/generator.php', 'generator');
        $this->customMergeConfigFrom(__DIR__.'/../config/relation.php', 'relation');
    }

    /**
     * @return array
     */
    public function provides() {
        return ['generator'];
    }

    /**
     * Hata işleyiciyi değiştiriyoruz.
     *
     * @return void
     */
//    public function bootErrorHandler() {
//        $this->app->singleton(
//            ExceptionHandler::class,
//            Handler::class
//        );
//    }

    /**
     * @return void
     */
    private function bootChannelRoutes() {
        if (file_exists(($file = $this->dir.'/../config/channel.routes.php'))) {
            require_once $file;
        }
    }

    /**
     * @return void
     */
    protected function bootEvents() {
        $configs = config()->all();

        foreach ($configs as $key => $value) {
            if (config()->has($key.'.events')) {
                foreach (config($key.'.events') as $event => $handlers) {
                    foreach ($handlers as $handler) {
                        $this->app['events']->listen($event, $handler);
                    }
                }
            }
        }
    }

    /**
     * Rota'ları kaydeder.
     *
     * @return void
     */
    protected function registerRoutes() {
        if ( ! $this->app->routesAreCached()) {
            $this->app['router']->prefix('v2')
                ->middleware(['api', 'team-finder'])
                ->namespace('NextDeveloper\Generator\Http\Controllers')
                ->group(__DIR__.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'api.routes.php');
        }
    }

    /**
     * @return void
     */
    protected function registerCommands() {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'NextDeveloper\Generator\Console\Commands\GenerateStructureCommand',
                'NextDeveloper\Generator\Console\Commands\GenerateModelCommand'
            /*
                'PlusClouds\Core\Console\Commands\FetchDisposableEmailDomainsCommand',
                'PlusClouds\Core\Console\Commands\MigrateExchangeRatesCommand',
                'PlusClouds\Core\Console\Commands\FetchExchangeRatesCommand',
                'PlusClouds\Core\Console\Commands\ObfuscateAccountDataCommand',
                // 'PlusClouds\Core\Common\Cache\ResponseCache\Commands\ClearCommand',
                */
            ]);
        }
    }

    /**
     * @return void
     */
    private function checkDatabaseConnection() {
        $isSuccessfull = false;

        try {
            \DB::connection()->getPdo();

            $isSuccessfull = true;
        } catch (\Exception $e) {
            die('Could not connect to the database. Please check your configuration. error:'.$e);
        }

        return $isSuccessfull;
    }
}
