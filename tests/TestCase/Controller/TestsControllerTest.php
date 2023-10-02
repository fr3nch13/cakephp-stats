<?php
declare(strict_types=1);

/**
 * TestsControllerTest
 */
namespace Sis\Stats\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * TestsControllerTest class
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
        $fixtures = [
            'plugin.Sis/Stats.Tests',
            'plugin.Sis/Stats.StatsEntities',
            'plugin.Sis/Stats.StatsCounts',
        ];

        return $fixtures;
    }

    /**
     * test dbLineTrait method with no args
     *
     * @return void
     */
    public function testDbLineTraitNoArgs(): void
    {
        Configure::write('debug', true);
        $this->get('/stats/tests/db-line-trait');

        $this->assertResponseCode(302);
        $this->assertRedirect('/stats/tests/db-line-trait/7/day');
    }

    /**
     * test dbLineTrait method with args
     *
     * @return void
     */
    public function testDbLineTraitWithArgs(): void
    {
        Configure::write('debug', true);
        $this->get('/stats/tests/db-line-trait/7/day');

        $content = (string)$this->_response->getBody();

        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        // title is properly set
        $this->assertStringContainsString('Test - Testing the trait 7 days', $content);
        // canvas where the chart is created
        $this->assertStringContainsString('<div class="chart-responsive">', $content);
        // option for different range
        $this->assertStringContainsString('<option value="/stats/tests/db-line-trait/3/month">Past 3 Months</option>', $content);
        // data is in a javascript block
        $this->assertStringContainsString('var config = {', $content);
        $this->assertStringContainsString('"label": "Open",', $content);
        $this->assertStringContainsString('"label": "Past Due",', $content);
        $this->assertStringContainsString('"label": "Needs Review",', $content);
        $this->assertStringContainsString("text: 'Test - Testing the trait 7 days'", $content);
        // javascript is there to write the chart
        $this->assertStringContainsString('var myLineChart = new Chart(ctx, config);', $content);
        // Listener watching the refresh button to be clicked.
        $this->assertStringContainsString(".block-refresh').on('click', function(event) {", $content);
        $this->assertStringContainsString('updateDashBoardBlock(updateEntity, url, true);', $content);
        // Listener watching the dropdown to update the chart.
        $this->assertStringContainsString("options').on('change', function() {", $content);
        $this->assertStringContainsString('updateDashBoardBlock(updateEntity, $(this).val(), true);', $content);
    }

    /**
     * test dbLineTrait method with args
     *
     * @return void
     */
    public function testDbLineTraitBadTimeperiod(): void
    {
        Configure::write('debug', true);
        $this->get('/stats/tests/db-line-trait/7/decade');

        $content = (string)$this->_response->getBody();

        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        // title is properly set
        $this->assertStringContainsString('Test - Testing the trait 7 days', $content);
        // canvas where the chart is created
        $this->assertStringContainsString('<div class="chart-responsive">', $content);
        // option for different range
        $this->assertStringContainsString('<option value="/stats/tests/db-line-trait/3/month">Past 3 Months</option>', $content);
        // data is in a javascript block
        $this->assertStringContainsString('var config = {', $content);
        $this->assertStringContainsString('"label": "Open",', $content);
        $this->assertStringContainsString('"label": "Past Due",', $content);
        $this->assertStringContainsString('"label": "Needs Review",', $content);
        $this->assertStringContainsString("text: 'Test - Testing the trait 7 days'", $content);
        // javascript is there to write the chart
        $this->assertStringContainsString('var myLineChart = new Chart(ctx, config);', $content);
        // Listener watching the refresh button to be clicked.
        $this->assertStringContainsString(".block-refresh').on('click', function(event) {", $content);
        $this->assertStringContainsString('updateDashBoardBlock(updateEntity, url, true);', $content);
        // Listener watching the dropdown to update the chart.
        $this->assertStringContainsString("options').on('change', function() {", $content);
        $this->assertStringContainsString('updateDashBoardBlock(updateEntity, $(this).val(), true);', $content);
    }

    /**
     * test dbLineTrait method with args
     *
     * @return void
     */
    public function testDbLineTraitWithIds(): void
    {
        Configure::write('debug', true);
        $this->get('/stats/tests/db-line-trait-ids/7/day');

        $content = (string)$this->_response->getBody();

        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        // title is properly set
        $this->assertStringContainsString('Test - Testing with IDS 7 days', $content);
        // canvas where the chart is created
        $this->assertStringContainsString('<div class="chart-responsive">', $content);
        // option for different range
        $this->assertStringContainsString('<option value="/stats/tests/db-line-trait-ids/3/month">Past 3 Months</option>', $content);
        // data is in a javascript block
        $this->assertStringContainsString('var config = {', $content);
        $this->assertStringContainsString('"label": "Open",', $content);
        $this->assertStringContainsString("text: 'Test - Testing with IDS 7 days'", $content);
        // make sure the counts are there and accurate.
        $this->assertStringContainsString('"data": [ 0, 0, 0, 0, 0, 0, 10, 0 ]', preg_replace("/\s+/", ' ', str_replace("\n", '', $content)));
        $this->assertStringContainsString('labels: [ "06\/14\/2019", "06\/15\/2019", "06\/16\/2019", "06\/17\/2019", "06\/18\/2019", "06\/19\/2019", "06\/20\/2019", "06\/21\/2019"]', preg_replace("/\s+/", ' ', str_replace("\n", '', $content)));
        // javascript is there to write the chart
        $this->assertStringContainsString('var myLineChart = new Chart(ctx, config);', $content);
        // Listener watching the refresh button to be clicked.
        $this->assertStringContainsString(".block-refresh').on('click', function(event) {", $content);
        $this->assertStringContainsString('updateDashBoardBlock(updateEntity, url, true);', $content);
        // Listener watching the dropdown to update the chart.
        $this->assertStringContainsString("options').on('change', function() {", $content);
        $this->assertStringContainsString('updateDashBoardBlock(updateEntity, $(this).val(), true);', $content);
    }
}
