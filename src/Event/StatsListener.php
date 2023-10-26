<?php
declare(strict_types=1);

/**
 * StatsListener
 */

namespace Fr3nch13\Stats\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Fr3nch13\Stats\Model\Entity\StatsObject;
use Fr3nch13\Stats\Model\Table\StatsObjectsTable;

/**
 * Stats Listener
 *
 * Event listener to add statistics about Ns Issues.
 */
class StatsListener implements EventListenerInterface
{
    use LocatorAwareTrait;

    /**
     * @var ?\Fr3nch13\Stats\Model\Table\StatsObjectsTable
     */
    public ?StatsObjectsTable $StatsObjects = null;

    /**
     * Used to map the event names to their related methods.
     *
     * @return array<string> The map of event names to methods.
     */
    public function implementedEvents(): array
    {
        return [
            'Fr3nch13.Stats.count' => 'recordCount',
        ];
    }

    /**
     * The main entry point into the Event.
     *
     * @param \Cake\Event\Event $event The triggered event.
     * @param string $key The StatsObject key
     * @param int $count The count to register, defaults to 1
     * @return \Fr3nch13\Stats\Model\Entity\StatsObject The object with the attached counts, if count > 0
     */
    public function recordCount(Event $event, string $key, int $count = 1): StatsObject
    {
        $this->loadTables();

        // either get or create the object.
        $object = $this->StatsObjects->register($key, ['count' => $count]);

        // record the counts.
        return $object;
    }

    /**
     * Loads the Stats Object Table
     *
     * @return void
     */
    private function loadTables(): void
    {
        if (!$this->StatsObjects) {
            $config = $this->getTableLocator()->exists('StatsObjects') ? [] : ['className' => StatsObjectsTable::class];
            /** @var \Fr3nch13\Stats\Model\Table\StatsObjectsTable $StatsObjects */
            $StatsObjects = $this->getTableLocator()->get('StatsObjects', $config);
            $this->StatsObjects = $StatsObjects;
        }
    }
}
