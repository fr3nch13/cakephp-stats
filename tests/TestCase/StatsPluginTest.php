<?php
declare(strict_types=1);

/**
 * PluginTest
 */

namespace Fr3nch13\Stats\Test\TestCase;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Fr3nch13\TestApp\Application;

/**
 * PluginTest class
 */
class StatsPluginTest extends TestCase
{
    /**
     * Apparently this is the new Cake way to do integration.
     */
    use IntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * testBootstrap
     *
     * @return void
     */
    public function testBootstrap(): void
    {
        $app = new Application(CONFIG);
        $app->bootstrap();
        $app->pluginBootstrap();
        $plugins = $app->getPlugins();

        // test to make sure the plugin was loaded.
        $this->assertSame('Fr3nch13/Stats', $plugins->get('Fr3nch13/Stats')->getName());

        // make sure it was able to read and store the config.
        $this->assertSame(Configure::read('Stats.test'), 'TEST');

        // test to make sure the listeners are registered.
        $listeners = $app->getEventManager()->listeners('Fr3nch13.Stats.count');
        $this->assertCount(1, $listeners);
    }

    /**
     * testRoutes
     *
     * @return void
     */
    public function testRoutes(): void
    {
        $this->loadPlugins(['Fr3nch13/Stats' => []]);
        $url = Router::url(['plugin' => 'Fr3nch13/Stats', 'controller' => 'Tests']);
        $this->assertSame($url, '/stats/tests');
    }
}
