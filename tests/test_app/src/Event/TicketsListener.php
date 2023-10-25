<?php
declare(strict_types=1);

/**
 * TicketsListener
 */

namespace Fr3nch13\TestApp\Event;

use Cake\Event\Event;
use Fr3nch13\Stats\Event\StatsListener;

/**
 * Tickets Listener
 *
 * Event listener used to test the StatsListener.
 */
class TicketsListener extends StatsListener
{
    /**
     * Used to map the event names to their related methods.
     *
     * @return array<string> The map of event names to methods.
     */
    public function implementedEvents(): array
    {
        return [
            'Tickets.count' => 'registerCount',
        ];
    }

    /**
     * Register a count
     *
     * @param \Cake\Event\Event<mixed> $event The triggered event to process.
     * @param string $key The StatsObject Key
     * @param int $count The Count to increment.
     * @return void
     */
    public function registerCount(Event $event, string $key, int $count = 1): bool
    {
        $statsObject = $this->recordCount($event, $key, $count);

        return !empty($statsObject->stats_counts);
    }
}
