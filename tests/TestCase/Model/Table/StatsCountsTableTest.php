<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Test\TestCase\Model\Table;

use Cake\I18n\DateTime;
use Cake\ORM\Association\BelongsTo;
use Cake\TestSuite\TestCase;
use Fr3nch13\Stats\Exception\CountsException;
use Fr3nch13\Stats\Model\Entity\StatsCount;
use Fr3nch13\Stats\Model\Entity\StatsObject;
use Fr3nch13\Stats\Model\Table\StatsCountsTable;
use Fr3nch13\Stats\Model\Table\StatsObjectsTable;

/**
 * Fr3nch13\Stats\Model\Table\StatsCountsTable Test Case
 *
 * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable
 */
class StatsCountsTableTest extends TestCase
{
    /**
     * Test subject
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
    }

    /**
     * Tests the class name of the Table
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::initialize()
     * @return void
     */
    public function testClassInstance(): void
    {
        $this->assertInstanceOf(StatsCountsTable::class, $this->StatsCounts);
    }

    /**
     * Testing a method.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::initialize()
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertSame('stats_counts', $this->StatsCounts->getTable());
        $this->assertSame('id', $this->StatsCounts->getDisplayField());
        $this->assertSame('id', $this->StatsCounts->getPrimaryKey());
    }

    /**
     * Test Associations method
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::initialize()
     * @return void
     */
    public function testAssociations(): void
    {
        // get all of the associations
        $Associations = $this->StatsCounts->associations();

        ////// foreach association.
        // make sure the association exists
        $this->assertNotNull($Associations->get('StatsObjects'));
        $this->assertInstanceOf(BelongsTo::class, $Associations->get('StatsObjects'));
        $this->assertInstanceOf(StatsObjectsTable::class, $Associations->get('StatsObjects')->getTarget());
        $Association = $this->StatsCounts->StatsObjects;
        $this->assertSame('StatsObjects', $Association->getName());
        $this->assertSame('Fr3nch13/Stats.StatsObjects', $Association->getClassName());
        $this->assertSame('stats_object_id', $Association->getForeignKey());
    }

    /**
     * Testing a method.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::validationDefault()
     * @return void
     */
    public function testValidationDefault(): void
    {
        // test no set fields
        $entity = $this->StatsCounts->newEntity([]);
        $this->assertInstanceOf(StatsCount::class, $entity);
        $expected = [
            'stats_object_id' => [
                '_required' => 'This field is required',
            ],
            'time_period' => [
                '_required' => 'This field is required',
            ],
            'time_stamp' => [
                '_required' => 'This field is required',
            ],
            'time_count' => [
                '_required' => 'This field is required',
            ],
        ];
        $this->assertSame($expected, $entity->getErrors());

        $entity->set('stats_object_id', 1);
        $entity->set('time_period', 'hour');
        $expected = [
            'time_stamp' => [
                '_required' => 'This field is required',
            ],
            'time_count' => [
                '_required' => 'This field is required',
            ],
        ];
        $this->assertSame($expected, $entity->getErrors());

        $entity->set('time_stamp', 2019062014);
        $expected = [
            'time_count' => [
                '_required' => 'This field is required',
            ],
        ];
        $this->assertSame($expected, $entity->getErrors());

        $entity->set('time_count', 1);
        $expected = [];
        $this->assertSame($expected, $entity->getErrors());

        // test empty fields
        $entity = $this->StatsCounts->newEntity([
            'time_period' => '',
        ]);
        $expected = [
            'stats_object_id' => [
                '_required' => 'This field is required',
            ],
            'time_period' => [
                '_empty' => 'This field cannot be left empty',
            ],
            'time_stamp' => [
                '_required' => 'This field is required',
            ],
            'time_count' => [
                '_required' => 'This field is required',
            ],
        ];
        $this->assertSame($expected, $entity->getErrors());

        // test valid entity
        $entity = $this->StatsCounts->newEntity([
            'stats_object_id' => 1,
            'time_period' => 'day',
            'time_stamp' => 20190620,
            'time_count' => 3,
        ]);

        $expected = [];

        $this->assertSame($expected, $entity->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::buildRules()
     * @return void
     */
    public function testBuildRules(): void
    {
        // bad Object ID
        $entity = $this->StatsCounts->newEntity([
            'stats_object_id' => 999,
            'time_period' => 'day',
            'time_stamp' => (new DateTime())->format('Ymd'),
            'time_count' => 3,
        ]);
        $result = $this->StatsCounts->checkRules($entity);
        $this->assertFalse($result);
        $expected = [
            'stats_object_id' => [
                '_existsIn' => 'Unknown Stats Object',
            ],
        ];
        $this->assertSame($expected, $entity->getErrors());

        // check that we are passing the rules.
        $entity = $this->StatsCounts->newEntity([
            'stats_object_id' => 1,
            'time_period' => 'day',
            'time_stamp' => (new DateTime())->format('Ymd'),
            'time_count' => 3,
        ]);
        $result = $this->StatsCounts->checkRules($entity);
        $this->assertTrue($result);
        $expected = [];
        $this->assertSame($expected, $entity->getErrors());
    }

    /**
     * Test the entity itself
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::get()
     * @return void
     */
    public function testEntity(): void
    {
        $entity = $this->StatsCounts->get(1, [
            'contain' => [
                'StatsObjects',
            ],
        ]);
        $this->assertInstanceOf(StatsCount::class, $entity);

        $this->assertSame(1, $entity->id);
        $this->assertSame('hour', $entity->time_period);
        $this->assertSame(1, $entity->stats_object_id);
        $this->assertTrue($entity->hasValue('stats_object'));
        $this->assertSame('Stats.Tests.open', $entity->stats_object->okey);
    }

    /**
     * Test updating the counts
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::addUpdateCount()
     * @return void
     */
    public function testAddUpdateCount(): void
    {
        $entity = $this->StatsCounts->StatsObjects->get(1);
        $this->assertInstanceOf(StatsObject::class, $entity);

        // test defaults
        $results = $this->StatsCounts->addUpdateCount($entity);
        $this->assertIsArray($results);
        $this->assertCount(5, $results);
        // make sure they're all there'
        $this->assertTrue(isset($results['year']));
        $this->assertTrue(isset($results['month']));
        $this->assertTrue(isset($results['week']));
        $this->assertTrue(isset($results['day']));
        $this->assertTrue(isset($results['hour']));

        // make sure they got incremented by 1.
        $this->assertSame(12001, $results['year']->time_count);
        $this->assertSame(3001, $results['month']->time_count);
        $this->assertSame(701, $results['week']->time_count);
        $this->assertSame(101, $results['day']->time_count);
        $this->assertSame(11, $results['hour']->time_count);

        // make sure they got incremented by 5.
        $results = $this->StatsCounts->addUpdateCount($entity, 5);

        $this->assertSame(12006, $results['year']->time_count);
        $this->assertSame(3006, $results['month']->time_count);
        $this->assertSame(706, $results['week']->time_count);
        $this->assertSame(106, $results['day']->time_count);
        $this->assertSame(16, $results['hour']->time_count);

        // test a different time
        $results = $this->StatsCounts->addUpdateCount($entity, 5, new DateTime('+1 hour'));

        $this->assertSame(12011, $results['year']->time_count);
        $this->assertSame(3011, $results['month']->time_count);
        $this->assertSame(711, $results['week']->time_count);
        $this->assertSame(111, $results['day']->time_count);
        $this->assertSame(5, $results['hour']->time_count);

        // test only day timeperiods in array
        $results = $this->StatsCounts->addUpdateCount($entity, 5, new DateTime('+1 hour'), ['day']);
        $this->assertIsArray($results);
        $this->assertSame(1, count($results));

        $this->assertSame(116, $results['day']->time_count);

        // test bad time period
        $this->expectException(CountsException::class);
        $this->expectExceptionMessage('Invalid timeperiod: badtimeperiod');
        $results = $this->StatsCounts->addUpdateCount($entity, 5, new DateTime('+1 hour'), ['badtimeperiod']);
    }

    /**
     * Tests getting timestamps.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::getTimeStamps()
     * @return void
     */
    public function testGetTimeStamps(): void
    {
        // test defaults
        $results = $this->StatsCounts->getTimeStamps();

        $this->assertIsArray($results);
        $this->assertCount(5, $results);
        // make sure they're all there'
        $this->assertTrue(isset($results['year']));
        $this->assertTrue(isset($results['month']));
        $this->assertTrue(isset($results['week']));
        $this->assertTrue(isset($results['day']));
        $this->assertTrue(isset($results['hour']));

        $now = new DateTime();
        $this->assertSame(intval($now->format('Y')), $results['year']);
        $this->assertSame(intval($now->format('Ym')), $results['month']);
        $this->assertSame(intval($now->format('YW')), $results['week']);
        $this->assertSame(intval($now->format('Ymd')), $results['day']);
        $this->assertSame(intval($now->format('YmdH')), $results['hour']);

        $hour1 = new DateTime('+1 hour');
        $results = $this->StatsCounts->getTimeStamps($hour1);

        $this->assertSame(intval($hour1->format('Y')), $results['year']);
        $this->assertSame(intval($hour1->format('Ym')), $results['month']);
        $this->assertSame(intval($hour1->format('YW')), $results['week']);
        $this->assertSame(intval($hour1->format('Ymd')), $results['day']);
        $this->assertSame(intval($hour1->format('YmdH')), $results['hour']);
    }

    /**
     * Tests getting timestamp range.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::getTimestampRange()
     * @return void
     */
    public function testGetTimestampRange(): void
    {
        $now = new DateTime();
        // test 1
        $results = $this->StatsCounts->getTimestampRange($now, 1, 'hour');
        $this->assertIsArray($results);
        $this->assertCount(2, $results);
        $this->assertSame(intval($now->format('YmdH')), $results[0]);
        $this->assertSame(intval($now->modify('-1 hour')->format('YmdH')), $results[1]);

        // test 5
        $results = $this->StatsCounts->getTimestampRange($now, 5, 'hour');
        debug($results);
        $this->assertIsArray($results);
        $this->assertCount(6, $results);
        $this->assertSame(intval($now->format('YmdH')), $results[0]);
        $this->assertSame(intval($now->modify('-1 hour')->format('YmdH')), $results[1]);
        $this->assertSame(intval($now->modify('-2 hour')->format('YmdH')), $results[2]);
        $this->assertSame(intval($now->modify('-3 hour')->format('YmdH')), $results[3]);
        $this->assertSame(intval($now->modify('-4 hour')->format('YmdH')), $results[4]);
        $this->assertSame(intval($now->modify('-5 hour')->format('YmdH')), $results[5]);
    }

    /**
     * Tests getting timestamp range.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::getObjectCounts()
     * @return void
     */
    public function testGetObjectCounts(): void
    {
        $now = new DateTime();

        // new entity
        $results = $this->StatsCounts->getObjectCounts('Stats.Tests.newkey', $now, 5, 'day');
        $this->assertIsArray($results);
        $this->assertTrue(isset($results['object']));
        $this->assertTrue(isset($results['counts']));
        $this->assertCount(6, $results['counts']);

        $i = 5;
        foreach($results['counts'] as $key => $count) {
            $stamp = intval($now->modify('-' . $i .' day')->format('Ymd'));
            $this->assertSame($stamp, $key);
            $this->assertSame(0, $count->stats_object_id);
            $this->assertSame('day', $count->time_period);
            $this->assertSame($stamp, $count->time_stamp);
            $this->assertSame(0, $count->time_count);
            $this->assertTrue($count->isNew());
            $i--;
        }

        // existing entity
        $results = $this->StatsCounts->getObjectCounts('Stats.Tests.open', $now, 5, 'day');
        debug($results);
        $this->assertIsArray($results);
        $this->assertTrue(isset($results['object']));
        $this->assertTrue(isset($results['counts']));
        $this->assertCount(6, $results['counts']);

        $i = 5;
        foreach($results['counts'] as $key => $count) {
            $stamp = intval($now->modify('-' . $i .' day')->format('Ymd'));
            $this->assertSame($stamp, $key);
            $this->assertSame(1, $count->stats_object_id);
            $this->assertSame('day', $count->time_period);
            $this->assertSame($stamp, $count->time_stamp);
            $count_count = 0;
            if ($i === 0) {
                $count_count = 100;
                $this->assertFalse($count->isNew());
            } else {
                $this->assertTrue($count->isNew());
            }
            $this->assertSame($count_count, $count->time_count);
            $i--;
        }

        // bad timeperiod
        $this->expectException(CountsException::class);
        $this->expectExceptionMessage('Invalid timeperiod: badtimeperiod');
        $results = $this->StatsCounts->getObjectCounts('Stats.Tests.open', $now, 5, 'badtimeperiod');
    }

    /**
     * Tests getting timestamp range.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsCountsTable::getObjectsCounts()
     * @return void
     */
    public function testGetObjectsCounts(): void
    {
        $now = new DateTime();

        $results = $this->StatsCounts->getObjectsCounts([
            'Stats.Tests.newkey', // new
            'Stats.Tests.open', // existing
        ], $now, 5, 'day');
        $this->assertIsArray($results);
        $this->assertCount(2, $results);

        // don't need to test the details of each one, that's done above.

        // bad timeperiod
        $this->expectException(CountsException::class);
        $this->expectExceptionMessage('Invalid timeperiod: badtimeperiod');
        $results = $this->StatsCounts->getObjectsCounts([
            'Stats.Tests.newkey', // new
            'Stats.Tests.open', // existing
        ], $now, 5, 'badtimeperiod');
    }
}
