<?php
declare(strict_types=1);

/**
 * Plugin Definitions
 */

namespace Fr3nch13\Stats;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Fr3nch13\Stats\Event\StatsListener;

/**
 * Plugin Definitions
 */
class StatsPlugin extends BasePlugin
{
    /**
     * Bootstraping for this specific plugin.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The app object.
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        // Add constants, load configuration defaults.
        if (!Configure::read('Stats')) {
            Configure::write('Stats', [
                'test' => 'TEST',
            ]);
        }

        // register out stats listener directly,
        // in case it needs to be directly dispatched.
        EventManager::instance()->on(new StatsListener());

        // By default will load `config/bootstrap.php` in the plugin.
        parent::bootstrap($app);
    }

    /**
     * Add plugin specific routes here.
     *
     * @param \Cake\Routing\RouteBuilder $routes The passed routes object.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        // Add routes.
        $routes->plugin(
            'Fr3nch13/Stats',
            ['path' => '/stats'],
            function (RouteBuilder $routes): void {
                $routes->setExtensions(['json']);
                $routes->prefix('Admin', function (RouteBuilder $routes): void {
                    $routes->fallbacks(DashedRoute::class);
                });
                $routes->fallbacks(DashedRoute::class);
            }
        );

        // By default will load `config/routes.php` in the plugin.
        parent::routes($routes);
    }
}
