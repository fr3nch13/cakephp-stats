<?php
declare(strict_types=1);

namespace Sis\Stats\Test\TestCase\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\TestCase;

/**
 * Sis\Stats\Model\Table\StatsCountsTable Test Case
 */
class StatsCountsTableTest extends TestCase
{
    /**
     * Test subject
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
            'plugin.Sis/Stats.StatsCounts',
            'plugin.Sis/Stats.StatsEntities',
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

        /** @var \Cake\ORM\Locator\TableLocator $Locator */
        $Locator = $this->getTableLocator();
        $Locator->allowFallbackClass(false);

        /** @var \Sis\Stats\Model\Table\StatsCountsTable $StatsCounts */
        $StatsCounts = $Locator->get('Sis/Stats.StatsCounts');
        $this->StatsCounts = $StatsCounts;
    }

    /**
     * Tests the class name of the Table
     *
     * @return void
     */
    public function testClassInstance(): void
    {
        $this->assertInstanceOf(\Sis\Stats\Model\Table\StatsCountsTable::class, $this->StatsCounts);
    }

    /**
     * Testing a method.
     *
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
     * @return void
     */
    public function testAssociations(): void
    {
        // get all of the associations
        $Associations = $this->StatsCounts->associations();

        ////// foreach association.
        // make sure the association exists
        $this->assertNotNull($Associations->get('StatsEntities'));
        $this->assertInstanceOf(\Cake\ORM\Association\BelongsTo::class, $Associations->get('StatsEntities'));
        $this->assertInstanceOf(\Sis\Stats\Model\Table\StatsEntitiesTable::class, $Associations->get('StatsEntities')->getTarget());
        $Association = $this->StatsCounts->StatsEntities;
        $this->assertSame('StatsEntities', $Association->getName());
        $this->assertSame('Sis/Stats.StatsEntities', $Association->getClassName());
        $this->assertSame('stats_entity_id', $Association->getForeignKey());
    }

    /**
     * Testing a method.
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        // test no set fields
        $entity = $this->StatsCounts->newEntity([]);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $entity);
        $expected = [
            'stats_entity_id' => [
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

        $entity->set('stats_entity_id', 1);
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
            'stats_entity_id' => [
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
            'stats_entity_id' => 1,
            'time_period' => 'day',
            'time_stamp' => 20190620,
            'time_count' => 3,
        ]);

        $expected = [];

        $this->assertSame($expected, $entity->getErrors());
    }

    /**
     * Test the entity itself
     *
     * @return void
     */
    public function testEntity(): void
    {
        $entity = $this->StatsCounts->get(1, [
            'contain' => [
                'StatsEntities',
            ],
        ]);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $entity);

        $this->assertSame(1, $entity->get('id'));
        $this->assertSame('hour', $entity->get('time_period'));
        $this->assertSame(1, $entity->get('stats_entity_id'));
        $this->assertTrue($entity->has('stats_entity'));
        $this->assertSame('PenTest.Results.open', $entity->get('stats_entity')->get('key'));
    }

    /**
     * Test the entity itself
     *
     * @return void
     */
    public function testEntityGetTimestamp(): void
    {
        // hour
        $entity = $this->StatsCounts->get(1);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $entity);
        $this->assertSame(1, $entity->get('id'));
        $this->assertSame('hour', $entity->get('time_period'));
        $timestamp = $entity->get('timestamp');
        $this->assertInstanceOf(FrozenTime::class, $timestamp);
        $this->assertSame(14, $timestamp->hour);

        // day
        $entity = $this->StatsCounts->get(2);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $entity);
        $this->assertSame(2, $entity->get('id'));
        $this->assertSame('day', $entity->get('time_period'));
        $timestamp = $entity->get('timestamp');
        $this->assertInstanceOf(FrozenTime::class, $timestamp);
        $this->assertSame(20, $timestamp->day);

        // week
        $entity = $this->StatsCounts->get(3);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $entity);
        $this->assertSame(3, $entity->get('id'));
        $this->assertSame('week', $entity->get('time_period'));
        $timestamp = $entity->get('timestamp');
        $this->assertInstanceOf(FrozenTime::class, $timestamp);
        $this->assertSame(25, $timestamp->weekOfYear);

        // month
        $entity = $this->StatsCounts->get(4);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $entity);
        $this->assertSame(4, $entity->get('id'));
        $this->assertSame('month', $entity->get('time_period'));
        $timestamp = $entity->get('timestamp');
        $this->assertInstanceOf(FrozenTime::class, $timestamp);
        $this->assertSame(06, $timestamp->month);

        // year
        $entity = $this->StatsCounts->get(5);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $entity);
        $this->assertSame(5, $entity->get('id'));
        $this->assertSame('year', $entity->get('time_period'));
        $timestamp = $entity->get('timestamp');
        $this->assertInstanceOf(FrozenTime::class, $timestamp);
        $this->assertSame(2019, $timestamp->year);
    }

    /**
     * Test updating the counts
     *
     * @return void
     */
    public function testAddUpdateCount(): void
    {
        $entity = $this->StatsCounts->StatsEntities->get(1);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $entity);

        $results = $this->StatsCounts->addUpdateCount($entity, 5);
        $this->assertIsArray($results);
        $this->assertSame(1, count($results));

        $results = $this->StatsCounts->addUpdateCount($entity, 5, new FrozenTime('2019-06-06 14:01:30'));
        $this->assertIsArray($results);
        $this->assertSame(1, count($results));

        $results = $this->StatsCounts->addUpdateCount($entity, 5, new FrozenTime('2019-06-07 14:01:30'), 'day');
        $this->assertIsArray($results);
        $this->assertSame(1, count($results));

        $results = $this->StatsCounts->addUpdateCount($entity, 5, new FrozenTime('2019-06-08 14:01:30'), 'day', false);
        $this->assertIsArray($results);
        $this->assertSame(1, count($results));

        $results = $this->StatsCounts->addUpdateCount($entity, 5, new FrozenTime('2019-06-09 14:01:30'), 'day', 'diff');
        $this->assertIsArray($results);
        $this->assertSame(1, count($results));
    }
}
