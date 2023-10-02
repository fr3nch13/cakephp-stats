<?php

declare(strict_types=1);

namespace Sis\Stats\Test\Fixture;

use Cake\Core\Configure;
use Sis\Core\Test\Fixture\CoreFixture;

/**
 * StatsEntities Fixture
 */
class StatsEntitiesFixture extends CoreFixture
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
            'model' => 'Sis/Stats.StatsEntities',
        ];

        $this->records = [
            [
                'id' => 1,
                'key' => 'PenTest.Results.open',
                'name' => 'Open',
                'description' => null,
                'color' => '#ff851b',
                'created' => '2019-06-20 14:04:39',
                'modified' => '2020-08-21 12:12:50',
                'last_updated' => '2020-08-21 12:12:50',
                'active' => true,
                'ic_id' => 1,
            ],
            [
                'id' => 2,
                'key' => 'PenTest.Results.pastDue',
                'name' => 'Past Due',
                'description' => null,
                'color' => '#dd4b39',
                'created' => '2019-06-20 14:04:40',
                'modified' => '2020-08-21 12:12:50',
                'last_updated' => '2020-08-21 12:12:50',
                'active' => true,
                'ic_id' => 2,
            ],
            [
                'id' => 3,
                'key' => 'PenTest.Results.needsReview',
                'name' => 'Needs Review',
                'description' => null,
                'color' => '#605ca8',
                'created' => '2019-06-20 14:04:40',
                'modified' => '2020-08-19 19:17:59',
                'last_updated' => '2020-08-19 19:17:59',
                'active' => true,
                'ic_id' => 3,
            ],
            [
                'id' => 4,
                'key' => 'PenTest.Results.108.open',
                'name' => 'Open',
                'description' => null,
                'color' => '#ff851b',
                'created' => '2019-06-20 14:04:40',
                'modified' => '2020-08-21 12:12:51',
                'last_updated' => '2020-08-21 12:12:51',
                'active' => true,
                'ic_id' => 108,
            ],
            [
                'id' => 5,
                'key' => 'PenTest.Results.11.open',
                'name' => 'Open',
                'description' => null,
                'color' => '#ff851b',
                'created' => '2019-06-20 14:04:40',
                'modified' => '2020-08-21 12:12:50',
                'last_updated' => '2020-08-21 12:12:50',
                'active' => true,
                'ic_id' => 11,
            ],
            [
                'id' => 6,
                'key' => 'PenTest.Results.52.open',
                'name' => 'Open Results',
                'description' => null,
                'color' => null,
                'created' => '2019-06-20 14:04:40',
                'modified' => '2019-06-20 14:09:12',
                'last_updated' => '2019-06-20 14:09:12',
                'active' => true,
                'ic_id' => 25,
            ],
            [
                'id' => 7,
                'key' => 'PenTest.Results.44.open',
                'name' => 'Open Results',
                'description' => null,
                'color' => null,
                'created' => '2019-06-20 14:04:41',
                'modified' => '2019-06-20 14:09:12',
                'last_updated' => '2019-06-20 14:09:12',
                'active' => true,
                'ic_id' => 44,
            ],
            [
                'id' => 9,
                'key' => 'PenTest.Results.1.open',
                'name' => 'Open Results',
                'description' => null,
                'color' => null,
                'created' => '2019-06-20 14:04:41',
                'modified' => '2019-10-07 09:17:06',
                'last_updated' => '2019-10-07 09:17:06',
                'active' => true,
                'ic_id' => 1,
            ],
            [
                'id' => 10,
                'key' => 'PenTest.Results.36.open',
                'name' => 'Open Results',
                'description' => null,
                'color' => null,
                'created' => '2019-06-20 14:04:42',
                'modified' => '2019-09-09 07:17:06',
                'last_updated' => '2019-09-09 07:17:06',
                'active' => true,
                'ic_id' => 36,
            ],
            [
                'id' => 11,
                'key' => 'PenTest.Results.93.open',
                'name' => 'Open Results',
                'description' => null,
                'color' => null,
                'created' => '2019-06-20 14:04:42',
                'modified' => '2019-06-20 14:09:13',
                'last_updated' => '2019-06-20 14:09:13',
                'active' => true,
                'ic_id' => 42,
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
