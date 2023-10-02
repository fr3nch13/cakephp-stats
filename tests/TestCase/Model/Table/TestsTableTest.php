<?php
declare(strict_types=1);

namespace Sis\Stats\Test\TestCase\Model\Table;

use App\Application;
use Cake\Event\EventList;
use Cake\TestSuite\TestCase;

/**
 * Sis\Stats\Model\Table\TestsTable Test Case
 */
class TestsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Sis\Stats\Model\Table\TestsTable
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
            'plugin.Sis/Stats.StatsEntities',
            'plugin.Sis/Stats.StatsCounts',
            'plugin.Sis/Stats.Tests',
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

        // needed here so that the TestsListener (StatsListener) gets registered.
        $app = new Application(CONFIG);
        $app->bootstrap();
        $app->pluginBootstrap();

        /** @var \Cake\ORM\Locator\TableLocator $Locator */
        $Locator = $this->getTableLocator();
        $Locator->allowFallbackClass(false);

        /** @var \Sis\Stats\Model\Table\TestsTable $Tests */
        $Tests = $Locator->get('Sis/Stats.Tests');
        $this->Tests = $Tests;

        // make phpstan happy.
        /** @var \Cake\Event\EventManager $eventManager */
        $eventManager = $this->Tests->getEventManager();

        $eventManager->setEventList(new EventList());
    }

    /**
     * Tests the class name of the Table
     *
     * @return void
     */
    public function testClassInstance(): void
    {
        $this->assertInstanceOf(\Sis\Stats\Model\Table\TestsTable::class, $this->Tests);
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

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\Test::class, $entity);

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
        // remove the behavior so that we can test that the listener is adding the behavior
        $this->Tests->testStatsListener();

        // test if the stats behavior was added.
        $behaviors = $this->Tests->behaviors()->loaded();
        $this->assertSame(['Stats'], $behaviors);

        // make phpstan happy.
        /** @var \Cake\Event\EventManager $eventManager */
        $eventManager = $this->Tests->getEventManager();

        $this->assertEventFired('Stats.Stats.Test.before', $eventManager);

        // check of the entities were created.

        $this->assertEventFired('Stats.Stats.Test.after', $eventManager);

        // check of the counts for the entities were created/updated.
    }
}
