<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Test\TestCase\Model\Table;

use ArgumentCountError;
use Cake\I18n\DateTime;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\TestSuite\TestCase;
use Fr3nch13\Stats\Exception\CountsException;
use Fr3nch13\Stats\Model\Entity\StatsObject;
use Fr3nch13\Stats\Model\Table\StatsCountsTable;
use Fr3nch13\Stats\Model\Table\StatsObjectsTable;

/**
 * Fr3nch13\Stats\Model\Table\StatsObjectsTable Test Case
 *
 * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable
 */
class StatsObjectsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Fr3nch13\Stats\Model\Table\StatsObjectsTable
     */
    public $StatsObjects;

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

        $config = $this->getTableLocator()->exists('StatsObjects') ? [] : ['className' => StatsObjectsTable::class];
        /** @var \Fr3nch13\Stats\Model\Table\StatsObjectsTable $StatsObjects */
        $StatsObjects = $this->getTableLocator()->get('Fr3nch13/Stats.StatsObjects', $config);
        $this->StatsObjects = $StatsObjects;
    }

    /**
     * Tests the class name of the Table
     *
     * @return void
     */
    public function testClassInstance(): void
    {
        $this->assertInstanceOf(StatsObjectsTable::class, $this->StatsObjects);
    }

    /**
     * Testing a method.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::initialize()
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertSame('stats_objects', $this->StatsObjects->getTable());
        $this->assertSame('name', $this->StatsObjects->getDisplayField());
        $this->assertSame('id', $this->StatsObjects->getPrimaryKey());
    }

    /**
     * Test the behaviors
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::initialize()
     * @return void
     */
    public function testBehaviors(): void
    {
        $behaviors = [
            'Timestamp' => TimestampBehavior::class,
        ];

        foreach ($behaviors as $name => $class) {
            $behavior = $this->StatsObjects->behaviors()->get($name);
            $this->assertNotNull($behavior, __('Behavior `{0}` is null.', [$name]));
            $this->assertInstanceOf($class, $behavior, __('Behavior `{0}` isn\'t an instance of {1}.', [
                $name,
                $class,
            ]));
        }
    }

    /**
     * Test Associations method
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::initialize()
     * @return void
     */
    public function testAssociations(): void
    {
        // get all of the associations
        $Associations = $this->StatsObjects->associations();

        ////// foreach association.
        // make sure the association exists
        $this->assertNotNull($Associations->get('StatsCounts'));
        $this->assertInstanceOf(HasMany::class, $Associations->get('StatsCounts'));
        $this->assertInstanceOf(StatsCountsTable::class, $Associations->get('StatsCounts')->getTarget());
        $Association = $this->StatsObjects->StatsCounts;
        $this->assertSame('StatsCounts', $Association->getName());
        $this->assertSame('Fr3nch13/Stats.StatsCounts', $Association->getClassName());
        $this->assertSame('stats_object_id', $Association->getForeignKey());
    }

    /**
     * Testing a method.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::validationDefault()
     * @return void
     */
    public function testValidationDefault(): void
    {
        // test no set fields
        $entity = $this->StatsObjects->newEntity([]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $expected = [
            'okey' => [
                '_required' => 'This field is required',
            ],
        ];
        $this->assertSame($expected, $entity->getErrors());

        // test setting the fields after an empty entity.
        $entity->set('name', 'Test Stats Entity');
        $expected = [
            'okey' => [
                '_required' => 'This field is required',
            ],
        ];

        $this->assertSame($expected, $entity->getErrors());

        // test empty fields
        $entity = $this->StatsObjects->newEntity([
            'name' => '',
            'okey' => '',
        ]);

        $expected = [
            'okey' => [
                '_empty' => 'This field cannot be left empty',
            ],
        ];

        $this->assertSame($expected, $entity->getErrors());

        // test valid entity
        $entity = $this->StatsObjects->newEntity([
            'name' => 'Entity Name',
            'okey' => 'Entity.name',
        ]);

        $expected = [];

        $this->assertSame($expected, $entity->getErrors());
    }

    /**
     * Test the entity itself
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::get()
     * @return void
     */
    public function testEntity(): void
    {
        $entity = $this->StatsObjects->get(1);

        $this->assertInstanceOf(StatsObject::class, $entity);

        $this->assertSame('Open', $entity->get('name'));
        $this->assertSame('Stats.Tests.open', $entity->okey);
        $this->assertTrue($entity->get('active'));
    }

    /**
     * Testing finder method.
     *
     * @return void
     */
    public function testFindAll(): void
    {
        $query = $this->StatsObjects->find('all');
        $count = $query->count();

        $this->assertSame(5, $count);
    }

    /**
     * Testing finder method.
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::findByKey()
     * @return void
     */
    public function testFindByKey(): void
    {
        $query = $this->StatsObjects->find('byKey', key: 'Stats.Tests.open');
        $count = $query->count();
        $this->assertSame(1, $count);

        $query = $this->StatsObjects->find('byKey', key: 'Stats.Tests.dontexist');
        $count = $query->count();
        $this->assertSame(0, $count);

        $this->expectException(ArgumentCountError::class);

        $query = $this->StatsObjects->find('byKey');
        $query->count();
    }

    /**
     * Testing registering an object, no count
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::register()
     * @return void
     */
    public function testRegisterJustKey(): void
    {
        // test with an existing key, no count
        $entity = $this->StatsObjects->register('Stats.Tests.open');
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(1, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(0, $entity->stats_counts);

        // test with a new key, no count
        $entity = $this->StatsObjects->register('Stats.Tests.newkey');
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(0, $entity->stats_counts);
    }

    /**
     * Testing registering an object with just the count field
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::register()
     * @return void
     */
    public function testRegisterFieldCount(): void
    {
        // test with an existing key, and count field
        $now = new DateTime();
        $entity = $this->StatsObjects->register('Stats.Tests.open', [
            'count' => 1,
        ]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(1, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(5, $entity->stats_counts);
        // make sure they're all there'
        $this->assertTrue(isset($entity->stats_counts['year']));
        $this->assertTrue(isset($entity->stats_counts['month']));
        $this->assertTrue(isset($entity->stats_counts['week']));
        $this->assertTrue(isset($entity->stats_counts['day']));
        $this->assertTrue(isset($entity->stats_counts['hour']));

        $this->assertSame(intval($now->format('Y')), $entity->stats_counts['year']->time_stamp);
        $this->assertSame(intval($now->format('Ym')), $entity->stats_counts['month']->time_stamp);
        $this->assertSame(intval($now->format('YW')), $entity->stats_counts['week']->time_stamp);
        $this->assertSame(intval($now->format('Ymd')), $entity->stats_counts['day']->time_stamp);
        $this->assertSame(intval($now->format('YmdH')), $entity->stats_counts['hour']->time_stamp);
        $this->assertSame(12002, $entity->stats_counts['year']->time_count);
        $this->assertSame(3002, $entity->stats_counts['month']->time_count);
        $this->assertSame(702, $entity->stats_counts['week']->time_count);
        $this->assertSame(102, $entity->stats_counts['day']->time_count);
        $this->assertSame(12, $entity->stats_counts['hour']->time_count);

        // test with a new key, and count field
        $now = new DateTime();
        $entity = $this->StatsObjects->register('Stats.Tests.newkey', [
            'count' => 1,
        ]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(6, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(5, $entity->stats_counts);
        // make sure they're all there'
        $this->assertTrue(isset($entity->stats_counts['year']));
        $this->assertTrue(isset($entity->stats_counts['month']));
        $this->assertTrue(isset($entity->stats_counts['week']));
        $this->assertTrue(isset($entity->stats_counts['day']));
        $this->assertTrue(isset($entity->stats_counts['hour']));

        $this->assertSame(intval($now->format('Y')), $entity->stats_counts['year']->time_stamp);
        $this->assertSame(intval($now->format('Ym')), $entity->stats_counts['month']->time_stamp);
        $this->assertSame(intval($now->format('YW')), $entity->stats_counts['week']->time_stamp);
        $this->assertSame(intval($now->format('Ymd')), $entity->stats_counts['day']->time_stamp);
        $this->assertSame(intval($now->format('YmdH')), $entity->stats_counts['hour']->time_stamp);
        $this->assertSame(1, $entity->stats_counts['year']->time_count);
        $this->assertSame(1, $entity->stats_counts['month']->time_count);
        $this->assertSame(1, $entity->stats_counts['week']->time_count);
        $this->assertSame(1, $entity->stats_counts['day']->time_count);
        $this->assertSame(1, $entity->stats_counts['hour']->time_count);
    }

    /**
     * Testing registering an object with just the count field
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::register()
     * @return void
     */
    public function testRegisterFieldTimestamp(): void
    {
        ///// test with an existing key, and just timestamp field
        // no counts, as count wasn't also given
        $now = new DateTime();
        $entity = $this->StatsObjects->register('Stats.Tests.open', [
            'timestamp' => $now,
        ]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(1, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(0, $entity->stats_counts);

        // valid count and current timestamp
        $now = new DateTime();
        $entity = $this->StatsObjects->register('Stats.Tests.open', [
            'count' => 1,
            'timestamp' => $now,
        ]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(1, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(5, $entity->stats_counts);

        // make sure they're all there'
        $this->assertTrue(isset($entity->stats_counts['year']));
        $this->assertTrue(isset($entity->stats_counts['month']));
        $this->assertTrue(isset($entity->stats_counts['week']));
        $this->assertTrue(isset($entity->stats_counts['day']));
        $this->assertTrue(isset($entity->stats_counts['hour']));

        $this->assertSame(intval($now->format('Y')), $entity->stats_counts['year']->time_stamp);
        $this->assertSame(intval($now->format('Ym')), $entity->stats_counts['month']->time_stamp);
        $this->assertSame(intval($now->format('YW')), $entity->stats_counts['week']->time_stamp);
        $this->assertSame(intval($now->format('Ymd')), $entity->stats_counts['day']->time_stamp);
        $this->assertSame(intval($now->format('YmdH')), $entity->stats_counts['hour']->time_stamp);
        $this->assertSame(12002, $entity->stats_counts['year']->time_count);
        $this->assertSame(3002, $entity->stats_counts['month']->time_count);
        $this->assertSame(702, $entity->stats_counts['week']->time_count);
        $this->assertSame(102, $entity->stats_counts['day']->time_count);
        $this->assertSame(12, $entity->stats_counts['hour']->time_count);

        // valid count and future timestamp
        $now = new DateTime('+1 year');
        $entity = $this->StatsObjects->register('Stats.Tests.open', [
            'count' => 1,
            'timestamp' => $now,
        ]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(1, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(5, $entity->stats_counts);

        // make sure they're all there'
        $this->assertTrue(isset($entity->stats_counts['year']));
        $this->assertTrue(isset($entity->stats_counts['month']));
        $this->assertTrue(isset($entity->stats_counts['week']));
        $this->assertTrue(isset($entity->stats_counts['day']));
        $this->assertTrue(isset($entity->stats_counts['hour']));

        $this->assertSame(intval($now->format('Y')), $entity->stats_counts['year']->time_stamp);
        $this->assertSame(intval($now->format('Ym')), $entity->stats_counts['month']->time_stamp);
        $this->assertSame(intval($now->format('YW')), $entity->stats_counts['week']->time_stamp);
        $this->assertSame(intval($now->format('Ymd')), $entity->stats_counts['day']->time_stamp);
        $this->assertSame(intval($now->format('YmdH')), $entity->stats_counts['hour']->time_stamp);
        $this->assertSame(1, $entity->stats_counts['year']->time_count);
        $this->assertSame(1, $entity->stats_counts['month']->time_count);
        $this->assertSame(1, $entity->stats_counts['week']->time_count);
        $this->assertSame(1, $entity->stats_counts['day']->time_count);
        $this->assertSame(1, $entity->stats_counts['hour']->time_count);

        ///// test with a new key, and just timestamp field
        $now = new DateTime();
        $entity = $this->StatsObjects->register('Stats.Tests.newkey', [
            'timestamp' => $now,
        ]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(6, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(0, $entity->stats_counts);

        // valid count and future timestamp
        $now = new DateTime('+1 year');
        $entity = $this->StatsObjects->register('Stats.Tests.newkey2', [
            'count' => 1,
            'timestamp' => $now,
        ]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(7, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(5, $entity->stats_counts);
        // make sure they're all there'
        $this->assertTrue(isset($entity->stats_counts['year']));
        $this->assertTrue(isset($entity->stats_counts['month']));
        $this->assertTrue(isset($entity->stats_counts['week']));
        $this->assertTrue(isset($entity->stats_counts['day']));
        $this->assertTrue(isset($entity->stats_counts['hour']));

        $this->assertSame(intval($now->format('Y')), $entity->stats_counts['year']->time_stamp);
        $this->assertSame(intval($now->format('Ym')), $entity->stats_counts['month']->time_stamp);
        $this->assertSame(intval($now->format('YW')), $entity->stats_counts['week']->time_stamp);
        $this->assertSame(intval($now->format('Ymd')), $entity->stats_counts['day']->time_stamp);
        $this->assertSame(intval($now->format('YmdH')), $entity->stats_counts['hour']->time_stamp);
        $this->assertSame(1, $entity->stats_counts['year']->time_count);
        $this->assertSame(1, $entity->stats_counts['month']->time_count);
        $this->assertSame(1, $entity->stats_counts['week']->time_count);
        $this->assertSame(1, $entity->stats_counts['day']->time_count);
        $this->assertSame(1, $entity->stats_counts['hour']->time_count);

        // valid count and bad timestamp
        $this->expectException(CountsException::class);
        $this->StatsObjects->register('Stats.Tests.open', [
            'count' => 1,
            'timestamp' => 'not a datetime',
        ]);
    }

    /**
     * Testing registering an object with just the count field
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::register()
     * @return void
     */
    public function testRegisterFieldTimeperiods(): void
    {
        // 2 valid timeperiods
        $now = new DateTime();
        $entity = $this->StatsObjects->register('Stats.Tests.open', [
            'count' => 1,
            'timestamp' => $now,
            'timeperiods' => ['day', 'year'],
        ]);
        $this->assertInstanceOf(StatsObject::class, $entity);
        $this->assertFalse($entity->isNew());
        $this->assertSame(1, $entity->id);
        $this->assertIsArray($entity->stats_counts);
        $this->assertCount(2, $entity->stats_counts);
        // make sure they're all there'
        $this->assertTrue(isset($entity->stats_counts['year']));
        $this->assertFalse(isset($entity->stats_counts['month']));
        $this->assertFalse(isset($entity->stats_counts['week']));
        $this->assertTrue(isset($entity->stats_counts['day']));
        $this->assertFalse(isset($entity->stats_counts['hour']));
        $this->assertSame(intval($now->format('Y')), $entity->stats_counts['year']->time_stamp);
        $this->assertSame(intval($now->format('Ymd')), $entity->stats_counts['day']->time_stamp);
        $this->assertSame(12002, $entity->stats_counts['year']->time_count);
        $this->assertSame(102, $entity->stats_counts['day']->time_count);

        // valid count and bad timeperiod
        $this->expectException(CountsException::class);
        $this->StatsObjects->register('Stats.Tests.open', [
            'count' => 1,
            'timestamp' => $now,
            'timeperiods' => ['day', 'year', 'notaperiod'],
        ]);
    }

    /**
     * Testing registering an object with just the count field
     *
     * @uses \Fr3nch13\Stats\Model\Table\StatsObjectsTable::registerMany()
     * @return void
     */
    public function testRegisterManyValid(): void
    {
        $now = new DateTime();
        $stats = [
            // existing
            'Stats.Tests.open' => [
                'count' => 1,
                'timestamp' => $now,
                'timeperiods' => ['day', 'year'],
            ],
            // existing
            'Stats.Tests.newkey' => [
                'count' => 1,
                'timestamp' => $now,
            ],
        ];

        $results = $this->StatsObjects->registerMany($stats);
        $this->assertCount(2, $results);
        $this->assertTrue(isset($results['Stats.Tests.open']));
        $this->assertTrue(isset($results['Stats.Tests.newkey']));

        $this->assertSame(1, $results['Stats.Tests.open']->id);
        $this->assertSame(6, $results['Stats.Tests.newkey']->id);

        $this->assertCount(2, $results['Stats.Tests.open']->stats_counts);
        $this->assertCount(5, $results['Stats.Tests.newkey']->stats_counts);
    }
}
