<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Test\TestCase\Controller;

use Cake\Core\Configure;
use Fr3nch13\Stats\Model\Table\StatsCountsTable;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Fr3nch13\TestApp\Application;

/**
 * Uses the TestsController to test the DbLineTrait
 *
 * @property \Cake\Http\Response $_response
 */
class TestsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Defines which fixtures we'll be using.
     *
     * @return array<string>
     */
    public function getFixtures(): array
    {
        return [
            'plugin.Fr3nch13/Stats.StatsCounts',
            'plugin.Fr3nch13/Stats.StatsObjects',
        ];
    }
    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Configure::write('debug', true);
        $this->loadPlugins(['Fr3nch13/Stats']);
    }

    /**
     * Test qr-code method
     *
     * @uses \Fr3nch13\Stats\Controller\TestsController::dbLineTrait()
     * @return void
     */
    public function testDbLineTrait(): void
    {
        // get auto redirect
        $this->get('/stats/tests/db-line-trait');
        $this->assertRedirect('/stats/tests/db-line-trait/7/day');

        //
        Configure::write('debug', true);
        $this->get('/stats/tests/db-line-trait/7/day');
        debug((string)$this->_response->getBody());
        $this->assertResponseOk();
    }
}
