<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Test\TestCase\Controller;

use Cake\Core\Configure;
use Fr3nch13\Stats\Exception\CountsException;
use Fr3nch13\Stats\Model\Table\StatsCountsTable;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Fr3nch13\TestApp\Application;

/**
 * Uses the TestsController to test the ChartJsTrait
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
     * Test line method
     *
     * @uses \Fr3nch13\Stats\Controller\TestsController::line()
     * @return void
     */
    public function testStatsTrait(): void
    {
        // get auto redirect
        $this->get('/stats/tests/line');
        $this->assertRedirect('/stats/tests/line/7/day');

        // test the Trait
        $this->get('/stats/tests/line/7/day');
        $this->assertResponseOk();
        $this->assertResponseContains('<!-- START: Fr3nch13/Stats.element/chartjs/block-line -->');
        $this->assertResponseContains('<!-- END: Fr3nch13/Stats.element/chartjs/block-line -->');


        $this->assertResponseContains('const ctx = document.getElementById(');
        $this->assertResponseContains('        "data": [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            101
        ]');
        $this->assertResponseContains('        "data": [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            102
        ]');
        $this->assertResponseContains('        "data": [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            103
        ]');
        $this->assertResponseContains('        "data": [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0
        ]');

        $this->get('/stats/tests/line/7/badtimeperiod');
        $this->assertResponseFailure('Invalid timeperiod: badtimeperiod');
    }
}
