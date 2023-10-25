<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Test\TestCase\Model\Table;

use App\Application;
use Cake\Core\Configure;
use Cake\Event\EventList;
use Cake\TestSuite\TestCase;
use Fr3nch13\Stats\Model\Entity\Test;
use Fr3nch13\Stats\Model\Table\StatsObjectsTable;
use Fr3nch13\Stats\Model\Table\TestsTable;

/**
 * Fr3nch13\Stats\Model\Table\TestsTable Test Case
 */
class TestsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Fr3nch13\Stats\Model\Table\TestsTable
     */
    public $Tests;

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
            'plugin.Fr3nch13/Stats.Tests',
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
        Configure::write('debug', true);

        //$this->loadRoutes();

        $config = $this->getTableLocator()->exists('Fr3nch13/Stats.Tests') ? [] : ['className' => TestsTable::class];
        /** @var \Fr3nch13\Stats\Model\Table\TestsTable $Tests */
        $Tests = $this->getTableLocator()->get('Tests', $config);
        $this->Tests = $Tests;
        debug($this->Tests);
    }

    /**
     * Tests the class name of the Table
     *
     * @return void
     */
    public function testClassInstance(): void
    {
        $this->assertInstanceOf(TestsTable::class, $this->Tests);
    }

    /**
     * Testing a method.
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertSame('stats_tests', $this->Tests->getTable());
        $this->assertSame('name', $this->Tests->getDisplayField());
        $this->assertSame('id', $this->Tests->getPrimaryKey());
    }

    /**
     * Test the behaviors
     *
     * @return void
     */
    public function testBehaviors(): void
    {
        // only test that there are no behaviors loaded for this table.
        $behaviors = $this->Tests->behaviors()->loaded();
        $this->assertEmpty($behaviors);
    }

    /**
     * Test the entity itself
     *
     * @return void
     */
    public function testEntity(): void
    {
        $entity = $this->Tests->get(1);

        $this->assertInstanceOf(Test::class, $entity);

        $this->assertSame('Test 1', $entity->get('name'));
    }

    /**
     * Testing finder method.
     *
     * @return void
     */
    public function testFindAll(): void
    {
        $query = $this->Tests->find('all');
        $this->assertSame(5, $query->count());
    }

    /**
     * Testing the StatsListener via the TestListener via the TestsTable.
     *
     * @return void
     */
    public function testStatsListener(): void
    {

        $config = $this->getTableLocator()->exists('Fr3nch13/Stats.StatsObjects') ? [] : ['className' => StatsObjectsTable::class];
        /** @var \Fr3nch13\Stats\Model\Table\StatsObjectsTable $StatsObjects */
        $StatsObjects = $this->getTableLocator()->get('StatsObjects', $config);

        // make sure the object doesn't exist
        $this->assertSame(0, $StatsObjects->find('byKey', key: 'test')->count());

        // Trigger the Event
        $this->Tests->testStatsListener();

        // make sure the object doesn't exist
        $this->assertSame(1, $StatsObjects->find('byKey', key: 'test')->count());

        // make phpstan happy.
        /** @var \Cake\Event\EventManager $eventManager */
        $eventManager = $this->Tests->getEventManager();

        $this->assertEventFired('Stats.Test.before', $eventManager);

        // check of the objects were created.

        $this->assertEventFired('Stats.Test.after', $eventManager);

        // check of the counts for the objects were created/updated.
    }
}
