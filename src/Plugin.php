<?php
declare(strict_types=1);

/**
 * Plugin Definitions
 */

namespace Sis\Stats;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Sis\Stats\Event\TestListener;

/**
 * Plugin Definitions
 */
class Plugin extends BasePlugin
{
    /**
     * Bootstraping for this specific plugin.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The app object.
     * @return void
     */
    public function bootstrap(\Cake\Core\PluginApplicationInterface $app): void
    {
        // Add constants, load configuration defaults.
        if (!Configure::read('Stats')) {
            Configure::write('Stats', [
                'test' => 'TEST',
            ]);
        }

        // used to test the src/Event/StatsListener via the src/Event/TestListener
        EventManager::instance()->on(new TestListener());

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
            'Sis/Stats',
            ['path' => '/stats'],
            function (RouteBuilder $routes) {
                $routes->setExtensions(['json', 'm', 'xml', 'txt', 'csv', 'pdf', 'xlsx']);
                $routes->prefix('Admin', function (RouteBuilder $routes) {
                    $routes->fallbacks(DashedRoute::class);
                });
                $routes->prefix('Sa', function (RouteBuilder $routes) {
                    $routes->fallbacks(DashedRoute::class);
                });
                $routes->prefix('Api', function (RouteBuilder $routes) {
                    $routes->prefix('V1', function (RouteBuilder $routes) {
                        $routes->fallbacks(DashedRoute::class);
                    });
                    $routes->fallbacks(DashedRoute::class);
                });
                $routes->fallbacks(DashedRoute::class);
            }
        );

        // By default will load `config/routes.php` in the plugin.
        parent::routes($routes);
    }
}
