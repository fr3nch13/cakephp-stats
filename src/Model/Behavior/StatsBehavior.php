<?php
declare(strict_types=1);

/**
 * StatsBehavior
 */

namespace Sis\Stats\Model\Behavior;

use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Behavior;

/**
 * Stats behavior
 *
 * Provides a simpler interface to the StatsCounts and StatsEntities Tables.
 * Many of the methods are just passthrough functions to the Tables.
 */
class StatsBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [];

    /**
     * Active configuration.
     *
     * @var array<string, mixed>
     */
    protected $config = [];

    /**
     * Defined entities for the stats.
     *
     * @var array<string, mixed>
     */
    public $entities = [];

    /**
     * The Entities table
     *
     * @var \Sis\Stats\Model\Table\StatsEntitiesTable
     */
    public $StatsEntities = null;

    /**
     * The Counts table
     *
     * @var \Sis\Stats\Model\Table\StatsCountsTable
     */
    public $StatsCounts = null;

    /**
     * The caching of the stats
     *
     * @var array<string, mixed>
     */
    public $stats = [];

    /**
     * Initialization method.
     *
     * @param array<string, mixed> $config The configuration options for this behavior.
     * @return void
     */
    public function initialize(array $config = []): void
    {
        $this->statsAttachModels();

        if (isset($config['entities'])) {
            $this->addEntities($config['entities']);
        }

        parent::initialize($config);
    }

    /**
     * Adds entities is this behavior if its already intalized.
     *
     * @param array<string, mixed> $entities List of entities to track stats for.
     * @return void
     */
    public function addEntities(array $entities = []): void
    {
        $this->config = Configure::read('Stats');

        // register the entities
        if ($entities) {
            $this->entities = $this->StatsEntities->registerMany($entities);
        }
    }

    /**
     * Gets a specific stats, so the model knows what it needs to include.
     *
     * @param null|string $key The key of the stats.
     * @return \Sis\Stats\Model\Entity\StatsEntity|null The stats entity.
     */
    public function statsGetByKey(?string $key = null): ?\Sis\Stats\Model\Entity\StatsEntity
    {
        if (!isset($this->stats[$key])) {
            $stats = $this->StatsEntities->find('all')
                ->where([$this->StatsEntities->getAlias() . '.key' => $key])
                ->first();
            if ($stats) {
                $this->stats[$key] = $stats;
            }
        }

        return $this->stats[$key] ?? null;
    }

    /**
     * Attaches the Models to use.
     *
     * @return void
     */
    protected function statsAttachModels(): void
    {
        /** @var \Cake\ORM\Locator\TableLocator $tableLocator */
        $tableLocator = \Cake\Datasource\FactoryLocator::get('Table');

        if ($this->StatsEntities === null) {
            /** @var \Sis\Stats\Model\Table\StatsEntitiesTable $StatsEntities */
            $StatsEntities = $tableLocator->get('Sis/Stats.StatsEntities');
            $this->StatsEntities = $StatsEntities;
        }

        if ($this->StatsCounts === null) {
            /** @var \Sis\Stats\Model\Table\StatsCountsTable $StatsCounts */
            $StatsCounts = $tableLocator->get('Sis/Stats.StatsCounts');
            $this->StatsCounts = $StatsCounts;
        }
    }

    /**
     * Gets the available time formats, or one.
     *
     * @param null|string $timeperiod The specific fromat of the timeperiod, if needed.
     * @return null|string The format for that timeperiod
     */
    public function statsTimeperiodFormats($timeperiod): ?string
    {
        $timeperiods = $this->StatsCounts->time_periods;
        if (isset($timeperiods[$timeperiod])) {
            return $timeperiods[$timeperiod];
        }

        return null;
    }

    /**
     * Generates, and returns the formatted timestamps based on a given start date.
     *
     * @param \Cake\I18n\FrozenTime|null $timestamp The timestamp to base it off of.
     * @return array<string, string>  The calculated array.
     */
    public function statsGetTimeStamps(?FrozenTime $timestamp = null): array
    {
        return $this->StatsCounts->getTimeStamps($timestamp);
    }

    /**
     * Returns the list of available/valid timeperiods.
     *
     * @return array<int, string> The calculated array.
     */
    public function statsGetTimeperiods(): array
    {
        return $this->StatsCounts->getTimePeriods();
    }

    /**
     * Gets the counts for a key with the given FrozenTime as the start, and going back X timeperiods.
     *
     * @param string $entityKey The key of the entity we want to get.
     * @param \Cake\I18n\FrozenTime $timestamp The date that we should start at, if null, then today will be used.
     * @param int $range How far we should go back in $timeperiod.
     * @param string $timeperiod The timeperiod we should use. see \Sis\Stats\Model\Table\StatsCounts::$time_periods.
     * @return null|array<string, mixed> Returns the entity and it's counts.
     */
    public function statsGetEntityCounts(
        string $entityKey,
        FrozenTime $timestamp,
        int $range,
        string $timeperiod
    ): ?array {
        return $this->StatsCounts->getEntityCounts($entityKey, $timestamp, $range, $timeperiod);
    }

    /**
     * Returns an array of counts for multiple entities.
     *
     * @param array<string> $entityKeys The array of entity keys we want to get.
     * @param \Cake\I18n\FrozenTime $timestamp The date that we should start at, if null, then today will be used.
     * @param int $range How far we should go back in $timeperiod.
     * @param string $timeperiod The timeperiod we should use. see \Sis\Stats\Model\Table\StatsCounts::$time_periods.
     * @return array<int|string, mixed> Returns the entities and their counts.
     */
    public function statsGetEntitiesCounts(
        array $entityKeys,
        FrozenTime $timestamp,
        int $range,
        string $timeperiod
    ): array {
        return $this->StatsCounts->getEntitiesCounts($entityKeys, $timestamp, $range, $timeperiod);
    }
}
