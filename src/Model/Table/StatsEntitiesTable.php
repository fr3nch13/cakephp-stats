<?php
declare(strict_types=1);

/**
 * StatsEntitiesTable
 */

namespace Sis\Stats\Model\Table;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\FrozenTime;
use Cake\Validation\Validator;
use Fr3nch13\Utilities\Model\Table\ToggleTrait;

/**
 * StatsEntities Model
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \Sis\Orgs\Model\Table\IcsTable&\Cake\ORM\Association\BelongsTo $Ics
 * @property \Sis\Stats\Model\Table\StatsCountsTable&\Cake\ORM\Association\HasMany $StatsCounts
 * @method \Sis\Stats\Model\Entity\StatsEntity get(mixed $primaryKey, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsEntity newEntity($data = null, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsEntity[] newEntities(array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsEntity|false save(\Sis\Stats\Model\Entity\StatsEntity $entity, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsEntity saveOrFail(\Sis\Stats\Model\Entity\StatsEntity $entity, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsEntity patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsEntity[] patchEntities($entities, array $data, array $options = [])
 * @method \Sis\Stats\Model\Entity\StatsEntity findOrCreate($search, callable $callback = null, array $options = [])
 */
class StatsEntitiesTable extends \Sis\Core\Model\Table\Table
{
    /**
     * Toggle Trait.
     */
    use ToggleTrait;

    /**
     * Initialize method
     *
     * @param array<mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config = []): void
    {
        parent::initialize($config);

        $this->setTable('stats_entities');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Ics')
            ->setForeignKey('ic_id')
            ->setClassName('Sis/Orgs.Ics');

        $this->hasMany('StatsCounts')
            ->setForeignKey('stats_entity_id')
            ->setClassName('Sis/Stats.StatsCounts');
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
            ->ascii('key')
            ->maxLength('key', 255)
            ->notEmptyString('key', __('The Key is required, and can not be empty.'))
            ->requirePresence('key', Validator::WHEN_CREATE);

        $validator
            ->ascii('name')
            ->maxLength('name', 255)
            ->notEmptyString('name', __('The Name is required, and can not be empty.'))
            ->requirePresence('name', Validator::WHEN_CREATE);

        $validator
            ->ascii('description')
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
    public function findByKey(\Cake\ORM\Query $query, array $options = []): \Cake\ORM\Query
    {
        if (!isset($options['key'])) {
            throw new RecordNotFoundException(__('Missing key in options.'));
        }

        $query->where([$this->getAlias() . '.key' => $options['key']]);

        return $query;
    }

    /**
     * Wrapper to allow registration of multiple notifications
     *
     * @param array<string, mixed> $statsEntities The list of notifications to register.
     * @return array<string, mixed> An array of the registered notification entities..
     */
    public function registerMany(array $statsEntities = []): array
    {
        $_statsEntities = [];
        foreach ($statsEntities as $key => $fields) {
            $_statsEntities[$key] = $this->register($key, $fields);
        }

        return $_statsEntities;
    }

    /**
     * Used to register a new Stats Entity, update an existing Stats Entity, or make sure it exists.
     *
     * @param string $key The key/unique id of the Stats Entity.
     * @param array<string, mixed> $fields The different fields in a record.
     * @return \Sis\Stats\Model\Entity\StatsEntity
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @TODO Use a more specific Exception when the save fails
     */
    public function register(string $key, array $fields = []): \Sis\Stats\Model\Entity\StatsEntity
    {
        $count = null;
        if (isset($fields['count'])) {
            $count = (int)$fields['count'];
            unset($fields['count']);
        }
        $timestamp = null;
        if (isset($fields['timestamp'])) {
            $timestamp = $fields['timestamp'];
            unset($fields['timestamp']);
        }
        $increment = false;
        if (isset($fields['increment'])) {
            $increment = $fields['increment'] ? true : false;
            unset($fields['increment']);
        }
        $timeperiods = ['hour'];
        if (isset($fields['timeperiods']) && is_array($fields['timeperiods'])) {
            $timeperiods = $fields['timeperiods'];
            unset($fields['timeperiods']);
        }
        $fields['key'] = $key;
        $fields = $this->fixVariables($fields);
        $statsEntity = $this->find('all')
            ->where([$this->getAlias() . '.key' => $key])
            ->first();
        if ($statsEntity instanceof \Sis\Stats\Model\Entity\StatsEntity) {
            $statsEntity = $this->patchEntity($statsEntity, $fields);
        } else {
            $statsEntity = $this->newEntity($fields);
        }

        if ($statsEntity->isDirty()) {
            if ($statsEntity->isNew() && !$statsEntity->get('name')) {
                $statsEntity->set('name', str_replace('.', ' ', $statsEntity->get('key')));
            }
            $statsEntity->set('last_updated', new FrozenTime());
            $statsEntity = $this->saveOrFail($statsEntity);
        }

        // if a count exists and is greater than 0, then create a count for this entity as well.
        // This keeps the stats_counts table from being loaded with a bunch of 0 counts.
        // when pulling the data to display these results, that process already creates generic
        // counts for each increment in the range, and then fills in the ones with an actual count.
        if ($count) {
            $this->StatsCounts->addUpdateCount($statsEntity, $count, $timestamp, $timeperiods, $increment);
        }

        return $statsEntity;
    }

    /**
     * Fixes any variables in a statsEntity before saving it.
     *
     * @param array<string, mixed> $variables The array of fields.
     * @return array<string, mixed> The fixed fields.
     */
    public function fixVariables($variables = [])
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
