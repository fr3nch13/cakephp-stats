<?php
declare(strict_types=1);

/**
 * StatsCountsTable
 */

namespace Sis\Stats\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;

/**
 * StatsCounts Model
 *
 * @property \Sis\Stats\Model\Table\StatsEntitiesTable&\Cake\ORM\Association\BelongsTo $StatsEntities
 * @method \Sis\Stats\Model\Entity\StatsCount get(mixed $primaryKey, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsCount newEntity($data = null, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsCount[] newEntities(array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsCount|false save(\Sis\Stats\Model\Entity\StatsCount $entity, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsCount saveOrFail(\Sis\Stats\Model\Entity\StatsCount $entity, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsCount patchEntity(\Sis\Stats\Model\Entity\StatsCount $entity, array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsCount[] patchEntities($entities, array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsCount findOrCreate($search, callable $callback = null, array $options = [])
 */
class StatsCountsTable extends \Sis\Core\Model\Table\Table
{
    /**
     * @var array<string, string> The available time periods, and their format.
     */
    public $time_periods = [
         'year' => 'yyyy',
        'month' => 'yyyyMM',
        'week' => 'yyyyww',
        'day' => 'yyyyMMdd',
        'hour' => 'yyyyMMddHH',
    ];

    /**
     * @var array<string, string> Human readable format for time periods.
     */
    public $nice_time_periods = [
        'year' => 'F',
        'month' => 'jS',
        'week' => 'l',
        'day' => 'ga',
        'hour' => 'i',
    ];

    /**
     * @var array<string, string> Human readable format for time period ranges.
     */
    public $nice_time_periods_range = [
        'year' => 'Y',
        'month' => 'M Y',
        'week' => 'D',
        'day' => 'M j, Y',
        'hour' => 'ga',
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

        $this->setTable('stats_counts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('StatsEntities')
            ->setForeignKey('stats_entity_id')
            ->setJoinType('INNER')
            ->setClassName('Sis/Stats.StatsEntities');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyFor('id', Validator::EMPTY_NULL, Validator::WHEN_CREATE);

        $validator
            ->integer('stats_entity_id')
            ->requirePresence('stats_entity_id', Validator::WHEN_CREATE);

        $validator
            ->ascii('time_period')
            ->maxLength('time_period', 20)
            ->notEmptyString('time_period')
            ->requirePresence('time_period', Validator::WHEN_CREATE);

        $validator
            ->integer('time_stamp')
            ->notEmptyString('time_stamp')
            ->requirePresence('time_stamp', Validator::WHEN_CREATE);

        $validator
            ->integer('time_count')
            ->notEmptyString('time_count')
            ->requirePresence('time_count', Validator::WHEN_CREATE);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(\Cake\ORM\RulesChecker $rules): \Cake\ORM\RulesChecker
    {
        $rules->add($rules->existsIn(['stats_entity_id'], 'StatsEntities'));

        return $rules;
    }

    /**
     * Adds a count for an entity
     *
     * @param \Sis\Stats\Model\Entity\StatsEntity $statsEntity The entity this count will belong to.
     * @param int $time_count The count for this count instance/record.
     * @param null|\Cake\I18n\FrozenTime $timestamp The timestamp of the count being added/updated.
     * @param string|array<int|string, string> $timeperiods The timeperiods to use for this count.
     * @param mixed $increment If we should imcrement the count, or overwrite it. default is to overwrite.
     * @return array<int,\Sis\Stats\Model\Entity\StatsCount> entities.
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @TODO Use a more specific Exception when the save fails
     */
    public function addUpdateCount(
        \Sis\Stats\Model\Entity\StatsEntity $statsEntity,
        int $time_count,
        $timestamp = null,
        $timeperiods = ['hour'],
        $increment = false
    ) {
        $out = [];
        if (!$timestamp) {
            $timestamp = new FrozenTime();
        }
        if (is_string($timeperiods)) {
            $timeperiods = [$timeperiods];
        }
        $timestamps = $this->getTimeStamps($timestamp);

        foreach ($timeperiods as $timeperiod) {
            // see if this exists.
            $where = [
                'stats_entity_id' => $statsEntity->get('id'),
                'time_period' => $timeperiod,
                'time_stamp' => $timestamps[$timeperiod],
            ];
            /** @var \Sis\Stats\Model\Entity\StatsCount|null $statsCount */
            $statsCount = $this->find('all')
                ->where($where)
                ->first();
            if ($statsCount === null) {
                $statsCount = $this->newEntity($where);
            }
            if ($increment) {
                if ($increment === true) {
                    $statsCount->set('time_count', $statsCount->get('time_count') + $time_count);
                } elseif ($increment == 'diff' && $time_count > $statsCount->get('time_count')) {
                    $diff = $time_count - $statsCount->get('time_count');
                    $statsCount->set('time_count', $statsCount->get('time_count') + $diff);
                }
            } elseif ($statsCount->get('time_count') !== $time_count) {
                $statsCount->set('time_count', $time_count);
            }
            if ($statsCount->isDirty() || $statsCount->isNew()) {
                $statsCount = $this->saveOrFail($statsCount);
                // update the entities last updated timestamp.
                $statsEntity->set('last_updated', new FrozenTime());
                $statsEntity = $this->StatsEntities->saveOrFail($statsEntity);
            }
            $out[intval($statsCount->get('id'))] = $statsCount;
        }

        return $out;
    }

    /**
     * Creates the matrix of timestamps.
     *
     * @param null|\Cake\I18n\FrozenTime|\DateTimeImmutable $timestamp The timestamp to generate the matrix from.
     * @return array<string, string> The timestamp matrix.
     */
    public function getTimeStamps($timestamp = null): array
    {
        if (!$timestamp) {
            $timestamp = new FrozenTime();
        }
        if ($timestamp instanceof \DateTimeImmutable) {
            $timestamp = new FrozenTime($timestamp);
        }

        $timestamps = [];
        foreach ($this->time_periods as $time_period => $time_format) {
            $time_stamp = strval($timestamp->i18nFormat($time_format));
            $timestamps[$time_period] = $time_stamp;
        }

        return $timestamps;
    }

    /**
     * Gets the list of valid time periods
     *
     * @return array<int, string>
     */
    public function getTimePeriods(): array
    {
        return array_keys($this->time_periods);
    }

    /**
     * Calculates the timestamp ranges needed to get the proper counts.
     *
     * @param \Cake\I18n\FrozenTime $timestamp The stating point for the range
     * @param int $range The range length
     * @param string $timeperiod The type of range, (hours, days, months, etc)
     * @return array<int, string> The calculated and generated matrix of dates/times.
     */
    public function getTimestampRange(FrozenTime $timestamp, int $range, string $timeperiod): array
    {
        $timeperiodPlural = Inflector::pluralize($timeperiod);
        $dates = [];
        // count down the timestamp from the current time to the end of range.
        while ($range) {
            $now = $timestamp->modify(__('-{0} {1}', [$range, ($range > 1 ? $timeperiod : $timeperiodPlural )]));
            $time_stamps = $this->getTimeStamps($now);
            $dates[$range] = $time_stamps[$timeperiod];
            $range--;
        }

        $time_stamps = $this->getTimeStamps($timestamp);
        $time_stamp = $time_stamps[$timeperiod];
        $dates[0] = $time_stamp;

        return $dates;
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
    public function getEntityCounts(
        string $entityKey,
        FrozenTime $timestamp,
        int $range,
        string $timeperiod
    ): ?array {
        $entity = $this->StatsEntities->find('byKey', [
            'key' => $entityKey,
        ])->first();

        if (!$entity) {
            // if the Entity can't be found, create a dummy one.
            $entity = $this->StatsEntities->newEntity([
                'key' => $entityKey,
                'name' => str_replace('.', ' ', $entityKey),
                'color' => '#FF0000',
                'description' => null,
                'active' => true,
                'ic_id' => null,
            ]);
            $entity->set('id', 0);
        }

        $return = [
            'entity' => $entity,
            'counts' => [],
        ];

        //// calculate the range of timestamps that we need.

        // not a valid timeperiod.
        if (!in_array($timeperiod, $this->getTimePeriods())) {
            return $return;
        }

        // get the calculated timestam range
        $range = $this->getTimestampRange($timestamp, $range, $timeperiod);

        // prefill the counts based on the range
        $counts = [];
        foreach ($range as $date) {
            $counts[$date] = $this->newEntity([
                'stats_entity_id' => $entity->get('id'),
                'time_period' => $timeperiod,
                'time_stamp' => $date,
                'time_count' => 0,
            ]);
        }

        $where = [
            'StatsCounts.stats_entity_id' => $entity->get('id'),
            'StatsCounts.time_period' => $timeperiod,
            'StatsCounts.time_stamp IN' => $range,
        ];

        $query = $this->find('all')
            ->where($where);

        foreach ($query as $count) {
            $time_stamp = $count->get('time_stamp');
            $counts[$time_stamp] = $count;
        }
        $return['counts'] = $counts;

        return $return;
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
    public function getEntitiesCounts(
        array $entityKeys,
        FrozenTime $timestamp,
        int $range,
        string $timeperiod
    ): array {
        $return = [];

        foreach ($entityKeys as $entityKey) {
            $return[$entityKey] = $this->getEntityCounts($entityKey, $timestamp, $range, $timeperiod);
        }

        return $return;
    }
}
