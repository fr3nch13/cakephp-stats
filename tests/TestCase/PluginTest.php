<?php
declare(strict_types=1);

/**
 * PluginTest
 */

namespace Sis\Stats\Test\TestCase;

use App\Application;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * PluginTest class
 */
class PluginTest extends TestCase
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

        $this->assertSame('Sis/Stats', $plugins->get('Sis/Stats')->getName());

        // make sure it was able to read and store the config.
        $this->assertSame(Configure::read('Stats.test'), 'TEST');
    }

    /**
     * testRoutes
     *
     * @return void
     */
    public function testRoutes(): void
    {
        $this->loadPlugins(['Sis/Stats' => []]);
        $url = Router::url(['plugin' => 'Sis/Stats', 'controller' => 'App']);
        $this->assertSame($url, '/stats/app');
    }
}
