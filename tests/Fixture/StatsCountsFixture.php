<?php

declare(strict_types=1);

namespace Fr3nch13\Stats\Test\Fixture;

use Cake\I18n\DateTime;

/**
 * StatsCounts Fixture
 */
class StatsCountsFixture extends CoreFixture
{
    /**
     * Table property
     *
     * @var string
     */
    public string $table = 'stats_counts';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->io->out(__('--- Init Fixture: {0} ---', [self::class]));

        $this->records = [
            // object 1
            [
                'id' => 1,
                'stats_object_id' => 1,
                'time_period' => 'hour',
                'time_stamp' => (new DateTime())->format('YmdH'),
                'time_count' => 11,
            ],
            [
                'id' => 2,
                'stats_object_id' => 1,
                'time_period' => 'day',
                'time_stamp' => (new DateTime())->format('Ymd'),
                'time_count' => 101,
            ],
            [
                'id' => 3,
                'stats_object_id' => 1,
                'time_period' => 'week',
                'time_stamp' => (new DateTime())->format('YW'),
                'time_count' => 701,
            ],
            [
                'id' => 4,
                'stats_object_id' => 1,
                'time_period' => 'month',
                'time_stamp' => (new DateTime())->format('Ym'),
                'time_count' => 3001,
            ],
            [
                'id' => 5,
                'stats_object_id' => 1,
                'time_period' => 'year',
                'time_stamp' => (new DateTime())->format('Y'),
                'time_count' => 12001,
            ],

            // object 2
            [
                'id' => 6,
                'stats_object_id' => 2,
                'time_period' => 'hour',
                'time_stamp' => (new DateTime())->format('YmdH'),
                'time_count' => 12,
            ],
            [
                'id' => 7,
                'stats_object_id' => 2,
                'time_period' => 'day',
                'time_stamp' => (new DateTime())->format('Ymd'),
                'time_count' => 102,
            ],
            [
                'id' => 8,
                'stats_object_id' => 2,
                'time_period' => 'week',
                'time_stamp' => (new DateTime())->format('YW'),
                'time_count' => 702,
            ],
            [
                'id' => 9,
                'stats_object_id' => 2,
                'time_period' => 'month',
                'time_stamp' => (new DateTime())->format('Ym'),
                'time_count' => 3002,
            ],
            [
                'id' => 10,
                'stats_object_id' => 2,
                'time_period' => 'year',
                'time_stamp' => (new DateTime())->format('Y'),
                'time_count' => 12002,
            ],

            // object 3
            [
                'id' => 11,
                'stats_object_id' => 3,
                'time_period' => 'hour',
                'time_stamp' => (new DateTime())->format('YmdH'),
                'time_count' => 13,
            ],
            [
                'id' => 12,
                'stats_object_id' => 3,
                'time_period' => 'day',
                'time_stamp' => (new DateTime())->format('Ymd'),
                'time_count' => 103,
            ],
            [
                'id' => 13,
                'stats_object_id' => 3,
                'time_period' => 'week',
                'time_stamp' => (new DateTime())->format('YW'),
                'time_count' => 703,
            ],
            [
                'id' => 14,
                'stats_object_id' => 3,
                'time_period' => 'month',
                'time_stamp' => (new DateTime())->format('Ym'),
                'time_count' => 3003,
            ],
            [
                'id' => 15,
                'stats_object_id' => 3,
                'time_period' => 'year',
                'time_stamp' => (new DateTime())->format('Y'),
                'time_count' => 12003,
            ],

            // object 4 doesn't have any

            // object 5
            [
                'id' => 16,
                'stats_object_id' => 5,
                'time_period' => 'hour',
                'time_stamp' => (new DateTime())->format('YmdH'),
                'time_count' => 15,
            ],
            [
                'id' => 17,
                'stats_object_id' => 5,
                'time_period' => 'day',
                'time_stamp' => (new DateTime())->format('Ymd'),
                'time_count' => 105,
            ],
            [
                'id' => 18,
                'stats_object_id' => 5,
                'time_period' => 'week',
                'time_stamp' => (new DateTime())->format('YW'),
                'time_count' => 705,
            ],
            [
                'id' => 19,
                'stats_object_id' => 5,
                'time_period' => 'month',
                'time_stamp' => (new DateTime())->format('Ym'),
                'time_count' => 3005,
            ],
            [
                'id' => 20,
                'stats_object_id' => 5,
                'time_period' => 'year',
                'time_stamp' => (new DateTime())->format('Y'),
                'time_count' => 12005,
            ],
        ];
        parent::init();
    }
}
