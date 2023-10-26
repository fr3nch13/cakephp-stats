<?php
declare(strict_types=1);

/**
 * StatsCountsTable
 */

namespace Fr3nch13\Stats\Model\Table;

use Cake\I18n\DateTime;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use Fr3nch13\Stats\Exception\CountsException;
use Fr3nch13\Stats\Model\Entity\StatsObject;

/**
 * StatsCounts Model
 *
 * @property \Fr3nch13\Stats\Model\Table\StatsObjectsTable&\Cake\ORM\Association\BelongsTo $StatsObjects
 * @method \Fr3nch13\Stats\Model\Entity\StatsCount get(mixed $primaryKey, array $contain = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsCount newEntity($data = null, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsCount[] newEntities(array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsCount|false save(\Fr3nch13\Stats\Model\Entity\StatsCount $entity, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsCount saveOrFail(\Fr3nch13\Stats\Model\Entity\StatsCount $entity, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsCount patchEntity(\Fr3nch13\Stats\Model\Entity\StatsCount $entity, array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsCount[] patchEntities($entities, array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsCount findOrCreate($search, callable $callback = null, array $options = [])
 */
class StatsCountsTable extends Table
{
    /**
     * @var array<string, string> The available time periods, and their format.
     */
    protected array $time_periods = [
         'year' => 'Y',
        'month' => 'Ym',
        'week' => 'YW',
        'day' => 'Ymd',
        'hour' => 'YmdH',
    ];

    /**
     * @var array<string, string> Human readable format for time periods.
     */
    protected array $nice_time_periods = [
        'year' => 'F',
        'month' => 'jS',
        'week' => 'l',
        'day' => 'ga',
        'hour' => 'i',
    ];

    /**
     * @var array<string, string> Human readable format for time period ranges.
     */
    protected array $nice_time_periods_range = [
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

        $this->belongsTo('StatsObjects')
            ->setForeignKey('stats_object_id')
            ->setJoinType('INNER')
            ->setClassName('Fr3nch13/Stats.StatsObjects');
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
            ->integer('stats_object_id')
            ->requirePresence('stats_object_id', Validator::WHEN_CREATE);

        $validator
            ->scalar('time_period')
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
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('stats_object_id', 'StatsObjects'), [
            'errorField' => 'stats_object_id',
            'message' => __('Unknown Stats Object'),
        ]);

        return $rules;
    }

    /**
     * Adds a count for an entity
     *
     * @param \Fr3nch13\Stats\Model\Entity\StatsObject $statsObject The entity this count will belong to.
     * @param int $time_count The count for this count instance/record.
     * @param \Cake\I18n\DateTime|null $timestamp The timestamp of the count being added/updated.
     * @param array<int|string, string>|null $timeperiods The timeperiods to use for this count.
     * @return array<string,\Fr3nch13\Stats\Model\Entity\StatsCount> objects.
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @TODO Use a more specific Exception when the save fails
     */
    public function addUpdateCount(
        StatsObject $statsObject,
        int $time_count = 1,
        ?DateTime $timestamp = null,
        ?array $timeperiods = null,
    ): array {
        $out = [];
        $validTimeperiods = $this->getTimePeriods();

        if (!$timestamp) {
            $timestamp = new DateTime();
        }

        if (!$timeperiods) {
            $timeperiods = $validTimeperiods;
        }

        $timestamps = $this->getTimeStamps($timestamp);

        foreach ($timeperiods as $timeperiod) {
            $this->checkTimePeriod($timeperiod);

            // see if this exists.
            $where = [
                'stats_object_id' => $statsObject->id,
                'time_period' => $timeperiod,
                'time_stamp' => $timestamps[$timeperiod],
            ];

            /** @var \Fr3nch13\Stats\Model\Entity\StatsCount|null $statsCount */
            $statsCount = $this->find('all')
                ->where($where)
                ->first();
            if ($statsCount === null) {
                $where['time_count'] = 0;
                $statsCount = $this->newEntity($where);
            }
            $statsCount->set('time_count', $statsCount->time_count + $time_count);

            if ($statsCount->isDirty() || $statsCount->isNew()) {
                $statsCount = $this->saveOrFail($statsCount);
                // update the objects last updated timestamp.
                $statsObject->set('last_updated', new DateTime());
                $statsObject = $this->StatsObjects->saveOrFail($statsObject);
            }
            $out[$timeperiod] = $statsCount;
        }

        return $out;
    }

    /**
     * Creates the matrix of timestamps.
     *
     * @param \Cake\I18n\DateTime|null $timestamp The timestamp to generate the matrix from.
     * @return array<string, int> The timestamp matrix.
     */
    public function getTimeStamps(?DateTime $timestamp = null): array
    {
        if (!$timestamp) {
            $timestamp = new DateTime();
        }

        $timestamps = [];
        foreach ($this->time_periods as $time_period => $time_format) {
            $time_stamp = $timestamp->format($time_format);
            $timestamps[$time_period] = intval($time_stamp);
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
     * @param \Cake\I18n\DateTime $timestamp The stating point for the range
     * @param int $range The range length
     * @param string $timeperiod The type of range, (hours, days, months, etc)
     * @return array<int, int> The calculated and generated matrix of dates/times.
     */
    public function getTimestampRange(DateTime $timestamp, int $range, string $timeperiod): array
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
     * Gets the counts for a key with the given DateTime as the start, and going back X timeperiods.
     *
     * @param string $objectKey The key of the entity we want to get.
     * @param \Cake\I18n\DateTime $timestamp The date that we should start at, if null, then today will be used.
     * @param int $range How far we should go back in $timeperiod.
     * @param string $timeperiod The timeperiod we should use. see \Fr3nch13\Stats\Model\Table\StatsCounts::$time_periods.
     * @return array<string, array<mixed>>|null Returns the entity and it's counts.
     */
    public function getObjectCounts(
        string $objectKey,
        DateTime $timestamp,
        int $range,
        string $timeperiod
    ): ?array {
        // make sure it's a valid time period

        $this->checkTimePeriod($timeperiod);

        $object = $this->StatsObjects->find('byKey', key: $objectKey)->first();

        if (!$object) {
            // if the Object can't be found, create a dummy one.
            $object = $this->StatsObjects->newEntity([
                'key' => $objectKey,
                'name' => str_replace('.', ' ', $objectKey),
                'color' => '#FF0000',
                'description' => null,
                'active' => true,
                'ic_id' => null,
            ]);
            $object->set('id', 0);
        }

        $return = [
            'object' => $object,
            'counts' => [],
        ];

        //// calculate the range of timestamps that we need.

        // get the calculated timestam range
        $range = $this->getTimestampRange($timestamp, $range, $timeperiod);

        // prefill the counts based on the range
        $counts = [];
        foreach ($range as $date) {
            $counts[$date] = $this->newEntity([
                'stats_object_id' => $object->id,
                'time_period' => $timeperiod,
                'time_stamp' => $date,
                'time_count' => 0,
            ]);
        }

        $where = [
            'StatsCounts.stats_object_id' => $object->id,
            'StatsCounts.time_period' => $timeperiod,
            'StatsCounts.time_stamp IN' => $range,
        ];

        $query = $this->find('all')
            ->where($where);

        foreach ($query as $count) {
            $time_stamp = $count->time_stamp;
            $counts[$time_stamp] = $count;
        }
        $return['counts'] = $counts;

        return $return;
    }

    /**
     * Returns an array of counts for multiple objects.
     *
     * @param array<string> $objectKeys The array of entity keys we want to get.
     * @param \Cake\I18n\DateTime $timestamp The date that we should start at, if null, then today will be used.
     * @param int $range How far we should go back in $timeperiod.
     * @param string $timeperiod The timeperiod we should use. see \Fr3nch13\Stats\Model\Table\StatsCounts::$time_periods.
     * @return array<int|string, mixed> Returns the objects and their counts.
     */
    public function getobjectsCounts(
        array $objectKeys,
        DateTime $timestamp,
        int $range,
        string $timeperiod
    ): array {
        $return = [];

        foreach ($objectKeys as $objectKey) {
            $return[$objectKey] = $this->getObjectCounts($objectKey, $timestamp, $range, $timeperiod);
        }

        return $return;
    }

    /**
     * Checks to make sure the timeperiod is valid
     *
     * @param string $timeperiod The time period to check
     * @return void
     * @throws \Fr3nch13\Stats\Exception\CountsException If the timeperiod is invalid.
     */
    public function checkTimePeriod(string $timeperiod): void
    {
        if (!in_array($timeperiod, $this->getTimePeriods(), true)) {
            throw new CountsException(__('Invalid timeperiod: {0}', [
                $timeperiod,
            ]));
        }
    }
}
