<?php
declare(strict_types=1);

/**
 * TicketsTable
 *
 * Used to test the event listeners
 */

namespace Fr3nch13\TestApp\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Table;

/**
 * Tickets Model
 *
 * @method \Fr3nch13\Stats\Model\Entity\Tickets get(mixed $primaryKey, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\Tickets newEntity($data = null, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\Tickets[] newobjects(array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\Tickets|false save(\Fr3nch13\Stats\Model\Entity\Tickets $entity, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\Tickets saveOrFail(\Fr3nch13\Stats\Model\Entity\Tickets $entity, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\Tickets patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\Tickets[] patchobjects($objects, array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\Tickets findOrCreate($search, callable $callback = null, array $options = [])
 */
class TicketsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config = []): void
    {
        parent::initialize($config);

        $this->setTable('stats_tickets');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
    }

    /**
     * Used by tests/TicketsCase/Table/TicketsTableTickets.php to test
     * the event src/Event/StatsListener.php
     *
     * @return void
     */
    public function testStatsListener(): void
    {
        $this->getEventManager()->dispatch(new Event('Tickets.count', $this, [
            'key' => 'test',
        ]));
    }
}
