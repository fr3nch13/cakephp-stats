<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Test\TestCase\Model\Table;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\TestSuite\TestCase;
use Fr3nch13\Stats\Model\Entity\StatsObject;
use Fr3nch13\Stats\Model\Table\StatsCountsTable;
use Fr3nch13\Stats\Model\Table\StatsObjectsTable;

/**
 * Fr3nch13\Stats\Model\Table\StatsObjectsTable Test Case
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
            'plugin.Fr3nch13/Stats.Tests',
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
            'name' => [
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
            'key' => '',
        ]);

        $expected = [
            'okey' => [
                '_empty' => 'The Key is required, and can not be empty.',
            ],
            'name' => [
                '_empty' => 'The Name is required, and can not be empty.',
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

        $this->assertSame(10, $count);
    }

    /**
     * Testing finder method.
     *
     * @return void
     */
    public function testFindByKey(): void
    {
        $query = $this->StatsObjects->find('key', key: 'Stats.Tests.open');
        $count = $query->count();
        $this->assertSame(1, $count);

        $query = $this->StatsObjects->find('key', key: 'Stats.Tests.dontexist');
        $count = $query->count();
        $this->assertSame(0, $count);

        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Missing key in options.');

        $query = $this->StatsObjects->find('byKey');
        $query->count();
    }
}
