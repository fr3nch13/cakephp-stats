<?php
declare(strict_types=1);

/**
 *  StatsCountTest
 */

namespace Sis\Stats\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;

/**
 *  StatsCount Test
 *
 * Created to specifically test the Entity and it's timestamp.
 */
class StatsCountTest extends TestCase
{
    /**
     * The table object.
     *
     * @var \Sis\Stats\Model\Table\StatsCountsTable
     */
    public $StatsCounts;

    /**
     * Defines which fixtures we'll be using.
     *
     * @return array<string>
     */
    public function getFixtures(): array
    {
        return [
            'plugin.Sis/Stats.StatsEntities',
            'plugin.Sis/Stats.StatsCounts',
        ];
    }

    /**
     * Connect the model.
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var \Cake\ORM\Locator\TableLocator $Locator */
        $Locator = $this->getTableLocator();
        $Locator->allowFallbackClass(false);

        /** @var \Sis\Stats\Model\Table\StatsCountsTable $StatsCounts */
        $StatsCounts = $Locator->get('Sis/Stats.StatsCounts');
        $this->StatsCounts = $StatsCounts;
    }

    /**
     * Testing the _getBody() method.
     */
    public function testGetTimestamp(): void
    {
        // currently right
        $stats_count = $this->StatsCounts->get(15);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $stats_count);
        $this->assertSame('day', $stats_count->get('time_period'));
        $this->assertSame(20191201, $stats_count->get('time_stamp'));
        $this->assertSame(2019, $stats_count->get('timestamp')->year);
        $this->assertSame(12, $stats_count->get('timestamp')->month);
        $this->assertSame(1, $stats_count->get('timestamp')->day);
        $this->assertSame('12/01/2019', $stats_count->get('timestamp')->i18nFormat('MM/dd/yyyy'));

        // currently right
        $stats_count = $this->StatsCounts->get(14);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $stats_count);
        $this->assertSame('day', $stats_count->get('time_period'));
        $this->assertSame(20191001, $stats_count->get('time_stamp'));
        $this->assertSame(2019, $stats_count->get('timestamp')->year);
        $this->assertSame(10, $stats_count->get('timestamp')->month);
        $this->assertSame(1, $stats_count->get('timestamp')->day);
        $this->assertSame('10/01/2019', $stats_count->get('timestamp')->i18nFormat('MM/dd/yyyy'));

        // currently wrong
        $stats_count = $this->StatsCounts->get(13);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $stats_count);
        $this->assertSame('day', $stats_count->get('time_period'));
        $this->assertSame(20190620, $stats_count->get('time_stamp'));
        $this->assertSame(2019, $stats_count->get('timestamp')->year);
        $this->assertSame(6, $stats_count->get('timestamp')->month);
        $this->assertSame(20, $stats_count->get('timestamp')->day);
        $this->assertSame('06/20/2019', $stats_count->get('timestamp')->i18nFormat('MM/dd/yyyy'));

        // wrong
        $stats_count = $this->StatsCounts->get(16);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $stats_count);
        $this->assertSame('day', $stats_count->get('time_period'));
        $this->assertSame(20190220, $stats_count->get('time_stamp'));
        $this->assertSame(2019, $stats_count->get('timestamp')->year);
        $this->assertSame(2, $stats_count->get('timestamp')->month);
        $this->assertSame(20, $stats_count->get('timestamp')->day);
        $this->assertSame('02/20/2019', $stats_count->get('timestamp')->i18nFormat('MM/dd/yyyy'));

        // wrong
        $stats_count = $this->StatsCounts->get(17);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $stats_count);
        $this->assertSame('day', $stats_count->get('time_period'));
        $this->assertSame(20190905, $stats_count->get('time_stamp'));
        $this->assertSame(2019, $stats_count->get('timestamp')->year);
        $this->assertSame(9, $stats_count->get('timestamp')->month);
        $this->assertSame(5, $stats_count->get('timestamp')->day);
        $this->assertSame('09/05/2019', $stats_count->get('timestamp')->i18nFormat('MM/dd/yyyy'));

        // week
        $stats_count = $this->StatsCounts->get(3);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $stats_count);
        $this->assertSame('week', $stats_count->get('time_period'));
        $this->assertSame(201925, $stats_count->get('time_stamp'));
        $this->assertSame(2019, $stats_count->get('timestamp')->year);
        $this->assertSame(6, $stats_count->get('timestamp')->month);
        $this->assertSame(17, $stats_count->get('timestamp')->day);
        $this->assertSame('06/17/2019', $stats_count->get('timestamp')->i18nFormat('MM/dd/yyyy'));
    }
}
