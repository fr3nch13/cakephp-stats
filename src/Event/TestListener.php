<?php
declare(strict_types=1);

/**
 * TestListener
 */

namespace Sis\Stats\Event;

/**
 * Test Listener
 *
 * Event listener used to test the StatsListener.
 */
class TestListener extends StatsListener
{
    /**
     * Used to map the event names to their related methods.
     *
     * @return array<string> The map of event names to methods.
     */
    public function implementedEvents(): array
    {
        $listeners = parent::implementedEvents();

        $listeners += [
            'Stats.Stats.Test.before' => 'checkAddBefore',
            'Stats.Stats.Test.after' => 'checkAddAfter',
        ];

        return $listeners;
    }

    /**
     * Used to test StatsListener::onBefore
     *
     * @param \Cake\Event\Event<mixed> $event The triggered event to process.
     * @return bool True if processed correctly. False if we need to stop propagation.
     */
    public function checkAddBefore(\Cake\Event\Event $event): bool
    {
        $this->setEntityPrefix('Stats.Stats.Test');
        $entities = [];
        $entities['total'] = [
            'name' => __('Total'),
            'color' => '#000000',
        ];
        $entities['new'] = [
            'name' => __('New'),
            'color' => '#0000FF',
            'increment' => true,
        ];
        $entities['updated'] = [
            'name' => __('Updated'),
            'color' => '#FFFF00',
            'increment' => true,
        ];
        $entities['active'] = [
            'name' => __('Active'),
            'color' => '#00FF00',
            'increment' => true,
        ];

        $this->setEntities($entities);

        return $this->onBefore($event);
    }

    /**
     * Used to test StatsListener::onAfter
     *
     * @param \Cake\Event\Event<mixed> $event The triggered event to process.
     * @return bool True if processed correctly. False if we need to stop propagation.
     */
    public function checkAddAfter(\Cake\Event\Event $event): bool
    {
        // add the stats crom the cron job.
        $cronCounts = $event->getSubject()->cronCounts();
        foreach ($cronCounts as $key => $count) {
            $this->updateEntityCount($key, (int)$count);
        }

        return $this->onAfter($event);
    }
}
