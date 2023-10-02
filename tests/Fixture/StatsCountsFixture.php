<?php

declare(strict_types=1);

namespace Sis\Stats\Test\Fixture;

use Cake\Core\Configure;
use Sis\Core\Test\Fixture\CoreFixture;

/**
 * StatsCounts Fixture
 */
class StatsCountsFixture extends CoreFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        if (Configure::read('Tests.Fixtures.init')) {
            $this->io->out(__('--- Init Fixture: {0} ---', [self::class]));
        }

        $this->import = [
            'model' => 'Sis/Stats.StatsCounts',
        ];

        $this->records = [
            [
                'id' => 1,
                'stats_entity_id' => 1,
                'time_period' => 'hour',
                'time_stamp' => 2019062014,
                'time_count' => 2046,
            ],
            [
                'id' => 2,
                'stats_entity_id' => 1,
                'time_period' => 'day',
                'time_stamp' => 20190620,
                'time_count' => 2046,
            ],
            [
                'id' => 3,
                'stats_entity_id' => 1,
                'time_period' => 'week',
                'time_stamp' => 201925,
                'time_count' => 2046,
            ],
            [
                'id' => 4,
                'stats_entity_id' => 1,
                'time_period' => 'month',
                'time_stamp' => 201906,
                'time_count' => 1909,
            ],
            [
                'id' => 5,
                'stats_entity_id' => 1,
                'time_period' => 'year',
                'time_stamp' => 2019,
                'time_count' => 635,
            ],
            [
                'id' => 6,
                'stats_entity_id' => 2,
                'time_period' => 'hour',
                'time_stamp' => 2019062014,
                'time_count' => 150,
            ],
            [
                'id' => 7,
                'stats_entity_id' => 2,
                'time_period' => 'day',
                'time_stamp' => 20190620,
                'time_count' => 150,
            ],
            [
                'id' => 8,
                'stats_entity_id' => 2,
                'time_period' => 'week',
                'time_stamp' => 201925,
                'time_count' => 150,
            ],
            [
                'id' => 9,
                'stats_entity_id' => 2,
                'time_period' => 'month',
                'time_stamp' => 201906,
                'time_count' => 2,
            ],
            [
                'id' => 10,
                'stats_entity_id' => 2,
                'time_period' => 'year',
                'time_stamp' => 2019,
                'time_count' => 3,
            ],
            [
                'id' => 11,
                'stats_entity_id' => 4,
                'time_period' => 'day',
                'time_stamp' => 20190620,
                'time_count' => 2,
            ],
            [
                'id' => 12,
                'stats_entity_id' => 5,
                'time_period' => 'day',
                'time_stamp' => 20190620,
                'time_count' => 3,
            ],
            [
                'id' => 13,
                'stats_entity_id' => 6,
                'time_period' => 'day',
                'time_stamp' => 20190620,
                'time_count' => 5,
            ],
            [
                'id' => 14,
                'stats_entity_id' => 6,
                'time_period' => 'day',
                'time_stamp' => 20191001,
                'time_count' => 5,
            ],
            [
                'id' => 15,
                'stats_entity_id' => 6,
                'time_period' => 'day',
                'time_stamp' => 20191201,
                'time_count' => 5,
            ],
            [
                'id' => 16,
                'stats_entity_id' => 6,
                'time_period' => 'day',
                'time_stamp' => 20190220,
                'time_count' => 5,
            ],
            [
                'id' => 17,
                'stats_entity_id' => 6,
                'time_period' => 'day',
                'time_stamp' => 20190905,
                'time_count' => 5,
            ],
        ];
        parent::init();
    }

    /**
     * See where things are actually inserted
     */
    public function insert(\Cake\Datasource\ConnectionInterface $connection)
    {
        if (Configure::read('Tests.Fixtures.insert')) {
            $this->io->out(__('--- insert: {0} ---', [self::class]));
        }

        return parent::insert($connection);
    }

    /**
     * See where things are actually truncated
     */
    public function truncate(\Cake\Datasource\ConnectionInterface $connection): bool
    {
        if (Configure::read('Tests.Fixtures.truncate')) {
            $this->io->out(__('--- truncate: {0} ---', [self::class]));
        }

        return parent::truncate($connection);
    }
}
