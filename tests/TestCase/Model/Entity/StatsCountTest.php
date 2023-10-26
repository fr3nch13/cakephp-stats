<?php
declare(strict_types=1);

/**
 *  StatsCountTest
 */

namespace Fr3nch13\Stats\Test\TestCase\Model\Entity;

use Cake\I18n\DateTime;
use Cake\TestSuite\TestCase;
use Fr3nch13\Stats\Model\Entity\StatsCount;

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
     * @var \Fr3nch13\Stats\Model\Table\StatsCountsTable
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
            'plugin.Fr3nch13/Stats.StatsObjects',
            'plugin.Fr3nch13/Stats.StatsCounts',
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

        /** @var \Fr3nch13\Stats\Model\Table\StatsCountsTable $StatsCounts */
        $StatsCounts = $Locator->get('Fr3nch13/Stats.StatsCounts');
        $this->StatsCounts = $StatsCounts;
    }

    /**
     * Testing the _getBody() method.
     */
    public function testGetTimestamp(): void
    {
        // Hour
        $now = new DateTime();
        $stats_count = $this->StatsCounts->get(1);
        $this->assertInstanceOf(StatsCount::class, $stats_count);
        $this->assertSame('hour', $stats_count->time_period);
        $this->assertSame((int)$now->format('YmdH'), $stats_count->time_stamp);
        $this->assertSame($now->year, $stats_count->timestamp->year);
        $this->assertSame($now->month, $stats_count->timestamp->month);
        $this->assertSame($now->weekOfYear, $stats_count->timestamp->weekOfYear);
        $this->assertSame($now->day, $stats_count->timestamp->day);
        $this->assertSame($now->hour, $stats_count->timestamp->hour);
        $this->assertSame($now->format('m/d/Y'), $stats_count->timestamp->i18nFormat('MM/dd/yyyy'));

        // Day
        $now = new DateTime();
        $stats_count = $this->StatsCounts->get(2);
        $this->assertInstanceOf(StatsCount::class, $stats_count);
        $this->assertSame('day', $stats_count->time_period);
        $this->assertSame((int)$now->format('Ymd'), $stats_count->time_stamp);
        $this->assertSame($now->year, $stats_count->timestamp->year);
        $this->assertSame($now->month, $stats_count->timestamp->month);
        $this->assertSame($now->weekOfYear, $stats_count->timestamp->weekOfYear);
        $this->assertSame($now->day, $stats_count->timestamp->day);
        $this->assertSame(0, $stats_count->timestamp->hour);
        $this->assertSame($now->format('m/d/Y'), $stats_count->timestamp->i18nFormat('MM/dd/yyyy'));

        // Week
        $now = new DateTime();
        $stats_count = $this->StatsCounts->get(3);
        $this->assertInstanceOf(StatsCount::class, $stats_count);
        $this->assertSame('week', $stats_count->time_period);
        $this->assertSame((int)$now->format('YW'), $stats_count->time_stamp);
        $this->assertSame($now->year, $stats_count->timestamp->year);
        $this->assertSame($now->month, $stats_count->timestamp->month);
        $this->assertSame($now->weekOfYear, $stats_count->timestamp->weekOfYear);
        $this->assertSame(1, $stats_count->timestamp->dayOfWeek);
        $this->assertSame(0, $stats_count->timestamp->hour);

        // Month
        $now = new DateTime();
        $stats_count = $this->StatsCounts->get(4);
        $this->assertInstanceOf(StatsCount::class, $stats_count);
        $this->assertSame('month', $stats_count->time_period);
        $this->assertSame((int)$now->format('Ym'), $stats_count->time_stamp);
        $this->assertSame($now->year, $stats_count->timestamp->year);
        $this->assertSame($now->month, $stats_count->timestamp->month);
        $this->assertSame(1, $stats_count->timestamp->day);
        $this->assertSame(0, $stats_count->timestamp->hour);
        $this->assertSame($now->format('m/01/Y'), $stats_count->timestamp->i18nFormat('MM/dd/yyyy'));

        // Year
        $now = new DateTime();
        $stats_count = $this->StatsCounts->get(5);
        $this->assertInstanceOf(StatsCount::class, $stats_count);
        $this->assertSame('year', $stats_count->time_period);
        $this->assertSame((int)$now->format('Y'), $stats_count->time_stamp);
        $this->assertSame($now->year, $stats_count->timestamp->year);
        $this->assertSame(1, $stats_count->timestamp->month);
        $this->assertSame(1, $stats_count->timestamp->day);
        $this->assertSame(0, $stats_count->timestamp->hour);
        $this->assertSame($now->format('01/01/Y'), $stats_count->timestamp->i18nFormat('MM/dd/yyyy'));
    }
}
