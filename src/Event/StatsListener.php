<?php
declare(strict_types=1);

/**
 * StatsListener
 */

namespace Sis\Stats\Event;

use Cake\Event\EventListenerInterface;

/**
 * Stats Listener
 *
 * Event listener to add statistics about Ns Issues.
 */
class StatsListener implements EventListenerInterface
{
    /**
     * @var string The entity prefix for the entity keys.
     */
    public $entityKeyPrefix = '';

    /**
     * @var array<string, mixed> The entities to process.
     */
    public $entities = [];

    /**
     * @var array<string, mixed> The allowed fields, and defaults
     */
    protected $defaultFields = [
        'name' => '',
        'color' => '#000000',
        'timeperiods' => ['hour', 'day', 'week', 'month', 'year'],
        'count' => 0,
    ];

    /**
     * Here to be compliant with the Interface.
     *
     * @return array<string> The map of event names to methods.
     */
    public function implementedEvents(): array
    {
        return [];
    }

    /**
     * Used to track stats before the Model\Table\Issues::processReport method starts.
     *
     * @param \Cake\Event\Event<mixed> $event The triggered event to process.
     * @return bool True if processed correctly. False if we need to stop propagation.
     */
    public function onBefore(\Cake\Event\Event $event): bool
    {
        if (!$event->getSubject()->behaviors()->has('Stats')) {
            $event->getSubject()->addBehavior('Sis/Stats.Stats');
        }
        if (count($this->getEntities())) {
            $event->getSubject()->behaviors()->get('Stats')->initialize([
                'entities' => $this->getEntities(),
            ]);
        }

        return true;
    }

    /**
     * Used to track stats after the Model\Table\Issues::processReport method is finished.
     *
     * @param \Cake\Event\Event<mixed> $event The triggered event to process.
     * @return bool True if processed correctly. False if we need to stop propagation.
     */
    public function onAfter(\Cake\Event\Event $event): bool
    {
        $event->getSubject()->behaviors()->get('Stats')->initialize([
            'entities' => $this->getEntities(),
        ]);

        return true;
    }

    /**
     * Gets the entity key prefix.
     *
     * @return string The prefix.
     */
    public function getEntityPrefix(): string
    {
        return $this->entityKeyPrefix;
    }

    /**
     * Sets the entity key prefix.
     *
     * @param string $prefix The prefix to set.
     * @return void
     */
    public function setEntityPrefix(string $prefix): void
    {
        $this->entityKeyPrefix = trim($prefix, '.');
    }

    /**
     * Returns the Entities.
     *
     * @return array<string, mixed> The Entities.
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * Sets the entities.
     *
     * @param array<string, mixed> $entities The entities.
     * @return void
     */
    public function setEntities(array $entities): void
    {
        foreach ($entities as $entityKey => $fields) {
            $entityKey = strval($entityKey);
            $this->setEntity($entityKey, $fields);
        }
    }

    /**
     * Gets a specific entity.
     *
     * @param string $entityKey The key of the entity to return.
     * @return null|array<string, mixed> The entity information
     */
    public function getEntity(string $entityKey): ?array
    {
        $entityKey = $this->formatEntityKey($entityKey);

        $entities = $this->getEntities();
        if (isset($entities[$entityKey])) {
            return $entities[$entityKey];
        }

        return null;
    }

    /**
     * Sets an entity.
     *
     * @param string $entityKey The entity key.
     * @param array<string, mixed> $fields The array of fields to set.
     *      If any are missing, they'll be filled in by the default settings aboce.
     * @return void
     */
    public function setEntity(string $entityKey, array $fields = []): void
    {
        $entityKey = $this->formatEntityKey($entityKey);

        $fields = $fields + $this->defaultFields;

        $this->entities[$entityKey] = $fields;
    }

    /**
     * Updates an entity's count.
     *
     * @param string $entityKey The entity key.
     * @param int $count The count to set the entity to.
     * @return void
     */
    public function updateEntityCount(string $entityKey, int $count): void
    {
        $entityKey = $this->formatEntityKey($entityKey);

        $entity = $this->getEntity($entityKey);
        if ($entity) {
            $this->entities[$entityKey]['count'] = $count;
        }
    }

    /**
     * Formats the entity key.
     *
     * @param string $entityKey The key to check/format.
     * @return string The formatted entity key.
     */
    public function formatEntityKey(string $entityKey): string
    {
        if (strpos((string)$entityKey, $this->getEntityPrefix()) === false) {
            $entityKey = $this->getEntityPrefix() . '.' . $entityKey;
        }

        return $entityKey;
    }
}
