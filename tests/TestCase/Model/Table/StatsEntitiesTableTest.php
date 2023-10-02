<?php
declare(strict_types=1);

namespace Sis\Stats\Test\TestCase\Model\Table;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\TestSuite\TestCase;

/**
 * Sis\Stats\Model\Table\StatsEntitiesTable Test Case
 */
class StatsEntitiesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Sis\Stats\Model\Table\StatsEntitiesTable
     */
    public $StatsEntities;

    /**
     * Defines which fixtures we'll be using.
     *
     * @return array<string>
     */
    public function getFixtures(): array
    {
        return [
            'plugin.Sis/Orgs.Ics',
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

        /** @var \Sis\Stats\Model\Table\StatsEntitiesTable $StatsEntities */
        $StatsEntities = $Locator->get('Sis/Stats.StatsEntities');
        $this->StatsEntities = $StatsEntities;
    }

    /**
     * Tests the class name of the Table
     *
     * @return void
     */
    public function testClassInstance(): void
    {
        $this->assertInstanceOf(\Sis\Stats\Model\Table\StatsEntitiesTable::class, $this->StatsEntities);
    }

    /**
     * Testing a method.
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertSame('stats_entities', $this->StatsEntities->getTable());
        $this->assertSame('name', $this->StatsEntities->getDisplayField());
        $this->assertSame('id', $this->StatsEntities->getPrimaryKey());
    }

    /**
     * Test the behaviors
     *
     * @return void
     */
    public function testBehaviors(): void
    {
        $behaviors = [
            'Timestamp' => \Cake\ORM\Behavior\TimestampBehavior::class,
        ];

        foreach ($behaviors as $name => $class) {
            $behavior = $this->StatsEntities->behaviors()->get($name);
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
        $Associations = $this->StatsEntities->associations();

        ////// foreach association.
        // make sure the association exists
        $this->assertNotNull($Associations->get('StatsCounts'));
        $this->assertInstanceOf(\Cake\ORM\Association\HasMany::class, $Associations->get('StatsCounts'));
        $this->assertInstanceOf(\Sis\Stats\Model\Table\StatsCountsTable::class, $Associations->get('StatsCounts')->getTarget());
        $Association = $this->StatsEntities->StatsCounts;
        $this->assertSame('StatsCounts', $Association->getName());
        $this->assertSame('Sis/Stats.StatsCounts', $Association->getClassName());
        $this->assertSame('stats_entity_id', $Association->getForeignKey());

        // make sure the association exists
        $this->assertNotNull($Associations->get('Ics'));
        $this->assertInstanceOf(\Cake\ORM\Association\BelongsTo::class, $Associations->get('Ics'));
        $this->assertInstanceOf(\Sis\Orgs\Model\Table\IcsTable::class, $Associations->get('Ics')->getTarget());
        $Association = $this->StatsEntities->Ics;
        $this->assertSame('Ics', $Association->getName());
        $this->assertSame('Sis/Orgs.Ics', $Association->getClassName());
        $this->assertSame('ic_id', $Association->getForeignKey());
    }

    /**
     * Testing a method.
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        // test no set fields
        $entity = $this->StatsEntities->newEntity([]);
        $expected = [
            'key' => [
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
            'key' => [
                '_required' => 'This field is required',
            ],
        ];

        $this->assertSame($expected, $entity->getErrors());

        // test empty fields
        $entity = $this->StatsEntities->newEntity([
            'name' => '',
            'key' => '',
        ]);

        $expected = [
            'key' => [
                '_empty' => 'The Key is required, and can not be empty.',
            ],
            'name' => [
                '_empty' => 'The Name is required, and can not be empty.',
            ],
        ];

        $this->assertSame($expected, $entity->getErrors());

        // test valid entity
        $entity = $this->StatsEntities->newEntity([
            'name' => 'Entity Name',
            'key' => 'Entity.name',
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
        $entity = $this->StatsEntities->get(1, [
            'contain' => [
                'Ics',
            ],
        ]);

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $entity);

        $this->assertSame('Open', $entity->get('name'));
        $this->assertSame('PenTest.Results.open', $entity->get('key'));
        $this->assertTrue($entity->get('active'));
        $this->assertSame(1, $entity->get('ic_id'));
        $this->assertTrue($entity->has('ic'));
    }

    /**
     * Testing finder method.
     *
     * @return void
     */
    public function testFindAll(): void
    {
        $query = $this->StatsEntities->find('all');
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
        $query = $this->StatsEntities->find('byKey', [
            'key' => 'PenTest.Results.open',
        ]);
        $count = $query->count();
        $this->assertSame(1, $count);

        $query = $this->StatsEntities->find('byKey', [
            'key' => 'PenTest.Results.dontexist',
        ]);
        $count = $query->count();
        $this->assertSame(0, $count);

        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Missing key in options.');

        $query = $this->StatsEntities->find('byKey');
        $count = $query->count();
    }
}
