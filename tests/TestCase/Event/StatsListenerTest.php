<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Test\TestCase\Event;

use Cake\Event\Event;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\I18n\DateTime;
use Cake\TestSuite\TestCase;
use Fr3nch13\Stats\Event\StatsListener;
use Fr3nch13\Stats\Model\Table\StatsCountsTable;

/**
 * Fr3nch13\Stats\Event\Stats\Event\StatsListener Test Case
 *
 * @uses \Fr3nch13\Stats\Event\StatsListener
 */
class StatsListenerTest extends TestCase
{
    use EventDispatcherTrait;

    /**
     * Test subject
     *
     * @var \Fr3nch13\Stats\Model\Table\StatsCountsTable
     */
    public $StatsCounts;

    /**
     * @var \Cake\Event\EventManager
     */
    public EventManager $eventManager;

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
    public function setUp(): void
    {
        parent::setUp();

        $config = $this->getTableLocator()->exists('StatsCounts') ? [] : ['className' => StatsCountsTable::class];
        /** @var \Fr3nch13\Stats\Model\Table\StatsCountsTable $StatsCounts */
        $StatsCounts = $this->getTableLocator()->get('Fr3nch13/Stats.StatsCounts', $config);
        $this->StatsCounts = $StatsCounts;

        /** @var \Cake\Event\EventManager $eventManager */
        $eventManager = $this->getEventManager();
        $this->eventManager = $eventManager;

        // track events
        $this->eventManager->setEventList(new EventList());
        // register the listener.
        $this->eventManager->on(new StatsListener());
    }

    /**
     * Test trigger on existing count.
     *
     * @uses \Fr3nch13\Stats\Event\StatsListener::recordCount()
     * @return void
     */
    public function testRecordCountObjectEmptyCountsNoCountDefined(): void
    {
        $now = new DateTime();
        // create object with counts
        $results = $this->StatsCounts->getObjectCounts('Stats.Tests.newkey', $now, 5, 'day');
        $this->assertIsArray($results);
        $this->assertTrue(isset($results['object']));
        $this->assertTrue(isset($results['counts']));
        $this->assertCount(6, $results['counts']);

        // no counts
        $i = 5;
        foreach ($results['counts'] as $key => $count) {
            $stamp = intval($now->modify('-' . $i . ' day')->format('Ymd'));
            $this->assertSame($stamp, $key);
            $this->assertSame(0, $count->stats_object_id);
            $this->assertSame('day', $count->time_period);
            $this->assertSame($stamp, $count->time_stamp);
            $this->assertSame(0, $count->time_count);
            $this->assertTrue($count->isNew());
            $i--;
        }

        $this->eventManager->dispatch(new Event('Fr3nch13.Stats.count', $this, [
            'key' => 'Stats.Tests.newkey',
        ]));

        $this->assertEventFired('Fr3nch13.Stats.count', $this->eventManager);

        // create object with counts
        $results = $this->StatsCounts->getObjectCounts('Stats.Tests.newkey', $now, 5, 'day');
        $this->assertIsArray($results);
        $this->assertTrue(isset($results['object']));
        $this->assertTrue(isset($results['counts']));
        $this->assertCount(6, $results['counts']);

        // 1 count will have been registered, and that was today.
        // the other ones are new/filler counts.
        $i = 5;
        foreach ($results['counts'] as $key => $count) {
            $stamp = intval($now->modify('-' . $i . ' day')->format('Ymd'));
            $this->assertSame($stamp, $key);
            $this->assertSame(6, $count->stats_object_id);
            $this->assertSame('day', $count->time_period);
            $this->assertSame($stamp, $count->time_stamp);
            $count_count = 0;
            if ($i === 0) {
                $count_count = 1;
                $this->assertFalse($count->isNew());
            } else {
                $this->assertTrue($count->isNew());
            }
            $this->assertSame($count_count, $count->time_count);
            $i--;
        }
    }

    /**
     * Test trigger on existing count.
     *
     * @uses \Fr3nch13\Stats\Event\StatsListener::recordCount()
     * @return void
     */
    public function testRecordCountObjectEmptyCountsCountDefined(): void
    {
        $now = new DateTime();
        // create object with counts
        $results = $this->StatsCounts->getObjectCounts('Stats.Tests.newkey', $now, 5, 'day');
        $this->assertIsArray($results);
        $this->assertTrue(isset($results['object']));
        $this->assertTrue(isset($results['counts']));
        $this->assertCount(6, $results['counts']);

        // no counts
        $i = 5;
        foreach ($results['counts'] as $key => $count) {
            $stamp = intval($now->modify('-' . $i . ' day')->format('Ymd'));
            $this->assertSame($stamp, $key);
            $this->assertSame(0, $count->stats_object_id);
            $this->assertSame('day', $count->time_period);
            $this->assertSame($stamp, $count->time_stamp);
            $this->assertSame(0, $count->time_count);
            $this->assertTrue($count->isNew());
            $i--;
        }

        $this->eventManager->dispatch(new Event('Fr3nch13.Stats.count', $this, [
            'key' => 'Stats.Tests.newkey',
            'count' => 3,
        ]));

        $this->assertEventFired('Fr3nch13.Stats.count', $this->eventManager);

        // create object with counts
        $results = $this->StatsCounts->getObjectCounts('Stats.Tests.newkey', $now, 5, 'day');
        $this->assertIsArray($results);
        $this->assertTrue(isset($results['object']));
        $this->assertTrue(isset($results['counts']));
        $this->assertCount(6, $results['counts']);

        // 1 count will have been registered, and that was today.
        // the other ones are new/filler counts.
        $i = 5;
        foreach ($results['counts'] as $key => $count) {
            $stamp = intval($now->modify('-' . $i . ' day')->format('Ymd'));
            $this->assertSame($stamp, $key);
            $this->assertSame(6, $count->stats_object_id);
            $this->assertSame('day', $count->time_period);
            $this->assertSame($stamp, $count->time_stamp);
            $count_count = 0;
            if ($i === 0) {
                $count_count = 3;
                $this->assertFalse($count->isNew());
            } else {
                $this->assertTrue($count->isNew());
            }
            $this->assertSame($count_count, $count->time_count);
            $i--;
        }
    }
}
