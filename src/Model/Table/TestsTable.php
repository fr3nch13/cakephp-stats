<?php
declare(strict_types=1);

/**
 * TestsTable
 */

namespace Sis\Stats\Model\Table;

use Cake\Event\Event;

/**
 * Tests Model
 *
 * @mixin \Sis\Stats\Model\Behavior\StatsBehavior
 * @method \Sis\Stats\Model\Entity\Test get(mixed $primaryKey, array $options = [])
 * @method \Sis\Stats\Model\Entity\Test newEntity($data = null, array $options = [])
 * @method \Sis\Stats\Model\Entity\Test[] newEntities(array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\Test|false save(\Sis\Stats\Model\Entity\Test $entity, array $options = [])
 * @method \Sis\Stats\Model\Entity\Test saveOrFail(\Sis\Stats\Model\Entity\Test $entity, array $options = [])
 * @method \Sis\Stats\Model\Entity\Test patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\Test[] patchEntities($entities, array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\Test findOrCreate($search, callable $callback = null, array $options = [])
 */
class TestsTable extends \Sis\Core\Model\Table\Table
{
    /**
     * @var array<string, int> List of stats used to test the StatsListener
     */
    public $stats = [
        'total' => 0,
        'new' => 0,
        'updated' => 0,
        'active' => 0,
    ];

    /**
     * Initialize method
     *
     * @param array<mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config = []): void
    {
        parent::initialize($config);

        $this->setTable('stats_tests');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
    }

    /**
     * Used by tests/TestCase/Table/TestsTableTest.php to test
     * the event src/Event/StatsListener.php
     *
     * @return void
     */
    public function testStatsListener(): void
    {
        // trigger the Sis\Stats\Event\TestListener::onBefore();
        // The Event's name/key should match what you defined in your TestsListeners::implementedEvents()
        $event = $this->getEventManager()->dispatch(new Event('Stats.Stats.Test.before', $this));

        // do stuff that updates the stats that match the same keys as your defined in your
        // Listener::onBefore() method.
        $this->stats['total'] = 10;
        $this->stats['new'] = 5;
        $this->stats['updated'] = 3;
        $this->stats['active'] = 9;

        // trigger the Sis\Stats\Event\TestListener::onAfter();
        $this->getEventManager()->dispatch(new Event('Stats.Stats.Test.after', $this));
    }

    /**
     * Gets the cron counts for the events.
     *
     * @return array<int> The list of cron counts.
     */
    public function cronCounts(): array
    {
        return [
            'total' => $this->stats['total'],
            'new' => $this->stats['new'],
            'updated' => $this->stats['updated'],
            'active' => $this->stats['active'],
            // here to test StatsListener::getEntity();
            '' => 8,
        ];
    }
}
