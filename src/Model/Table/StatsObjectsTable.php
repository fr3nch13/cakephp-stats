<?php
declare(strict_types=1);

/**
 * StatsObjectsTable
 */

namespace Fr3nch13\Stats\Model\Table;

use Cake\I18n\DateTime;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Fr3nch13\Stats\Exception\CountsException;
use Fr3nch13\Stats\Model\Entity\StatsObject;

/**
 * StatsObjects Model
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \Fr3nch13\Stats\Model\Table\StatsCountsTable&\Cake\ORM\Association\HasMany $StatsCounts
 * @method \Fr3nch13\Stats\Model\Entity\StatsObject get(mixed $primaryKey, array $contain = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsObject newEntity($data = null, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsObject[] newEntities(array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsObject|false save(\Fr3nch13\Stats\Model\Entity\StatsObject $entity, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsObject saveOrFail(\Fr3nch13\Stats\Model\Entity\StatsObject $entity, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsObject patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsObject[] patchEntities($entities, array $data, array $options = [])
 * @method \Fr3nch13\Stats\Model\Entity\StatsObject findOrCreate($search, callable $callback = null, array $options = [])
 */
class StatsObjectsTable extends Table
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

        $this->setTable('stats_objects');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('StatsCounts')
            ->setForeignKey('stats_object_id')
            ->setClassName('Fr3nch13/Stats.StatsCounts');

        $this->addBehavior('Timestamp');
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
            ->scalar('okey')
            ->maxLength('okey', 255)
            ->notEmptyString('okey')
            ->requirePresence('okey', Validator::WHEN_CREATE)
            ->add('okey', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => __('This Key already exists.'),
            ]);

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->dateTime('last_updated')
            ->allowEmptyDateTime('last_updated');

        $validator
            ->boolean('active');

        return $validator;
    }

    /**
     * Custom Finders
     */

    /**
     * Find an Entity by it's key.
     *
     * @param \Cake\ORM\Query<mixed> $query The query object to modify.
     * @param array<mixed> $options The options either specific to this finder, or to pass through.
     * @return \Cake\ORM\Query<mixed> Return the modified query object.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException if the 'key options isn't set, or is null.
     */
    public function findByKey(Query $query, string $key): Query
    {
        return $query->where([$this->getAlias() . '.okey' => $key]);
    }

    /**
     * Wrapper to allow registration of multiple notifications
     *
     * @param array<string, mixed> $statsobjects The list of notifications to register.
     * @return array<string, mixed> An array of the registered notification objects..
     */
    public function registerMany(array $statsobjects = []): array
    {
        $_statsobjects = [];
        foreach ($statsobjects as $key => $fields) {
            $_statsobjects[$key] = $this->register($key, $fields);
        }

        return $_statsobjects;
    }

    /**
     * Used to register a new Stats Entity, update an existing Stats Entity, or make sure it exists.
     *
     * @param string $key The key/unique id of the Stats Entity.
     * @param array<string, mixed> $fields The different fields in a record.
     * @return \Fr3nch13\Stats\Model\Entity\StatsObject
     * @throws \Fr3nch13\Stats\Exception\CountsException If any of the time periods are invalid
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @TODO Use a more specific Exception when the save fails
     */
    public function register(string $key, array $fields = []): StatsObject
    {
        $count = null;
        if (isset($fields['count'])) {
            $count = (int)$fields['count'];
            unset($fields['count']);
        }

        $fields['key'] = $key;
        $fields = $this->fixVariables($fields);

        $statsObject = $this->find('byKey', key: $key)
            ->first();
        if ($statsObject instanceof StatsObject) {
            $statsObject = $this->patchEntity($statsObject, $fields);
        } else {
            $fields['okey'] = $key;
            $statsObject = $this->newEntity($fields);
        }

        if ($statsObject->isDirty() || $statsObject->isNew()) {
            if ($statsObject->isNew() && !$statsObject->get('name')) {
                $statsObject->set('name', str_replace('.', ' ', $statsObject->okey));
            }
            $statsObject->set('last_updated', new DateTime());
            $statsObject = $this->saveOrFail($statsObject);
        }

        // if a count exists and is greater than 0, then create a count for this entity as well.
        // This keeps the stats_counts table from being loaded with a bunch of 0 counts.
        // when pulling the data to display these results, that process already creates generic
        // counts for each increment in the range, and then fills in the ones with an actual count.
        $counts = [];
        if ($count) {
            $timestamp = null;
            if (isset($fields['timestamp'])) {
                $timestamp = $fields['timestamp'];
                // make sure it's a valid time period
                if (!$timestamp instanceof DateTime) {
                    throw new CountsException(__('Invalid timestamp field'));
                }
                unset($fields['timestamp']);
            }

            $timeperiods = null;
            if (isset($fields['timeperiods']) && is_array($fields['timeperiods'])) {
                // make sure they're all valid time periods
                foreach ($fields['timeperiods'] as $timeperiod) {
                    $this->StatsCounts->checkTimePeriod($timeperiod);
                }

                $timeperiods = $fields['timeperiods'];
                unset($fields['timeperiods']);
            }

            $counts = $this->StatsCounts->addUpdateCount($statsObject, $count, $timestamp, $timeperiods);
        }
        $statsObject->stats_counts = $counts;

        return $statsObject;
    }

    /**
     * Fixes any variables in a statsObject before saving it.
     *
     * @param array<string, mixed> $variables The array of fields.
     * @return array<string, mixed> The fixed fields.
     */
    public function fixVariables(array $variables = []): array
    {
        $pattern = '/\{(\w+)__(\w+)\}/i';
        $replacement = '{${1}.${2}}';
        foreach ($variables as $k => $v) {
            if (is_string($v)) {
                $variables[$k] = preg_replace($pattern, $replacement, $v);
            }
        }

        return $variables;
    }
}
