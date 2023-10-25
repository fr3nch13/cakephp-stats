<?php

declare(strict_types=1);

namespace Fr3nch13\Stats\Test\Fixture;

use Cake\I18n\DateTime;

/**
 * StatsObjects Fixture
 */
class StatsObjectsFixture extends CoreFixture
{
    /**
     * Table property
     *
     * @var string
     */
    public string $table = 'stats_objects';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->io->out(__('--- Init Fixture: {0} ---', [self::class]));

        $this->records = [
            [
                'id' => 1,
                'okey' => 'Stats.Tests.open',
                'name' => 'Open',
                'color' => '#FFFF00',
                'created' => new DateTime(),
                'last_updated' => new DateTime(),
                'active' => true,
            ],
            [
                'id' => 2,
                'okey' => 'Stats.Tests.closed',
                'name' => 'Tests Closed',
                'color' => '#00FF00',
                'created' => new DateTime(),
                'last_updated' => new DateTime(),
                'active' => true,
            ],
            [
                'id' => 3,
                'okey' => 'Stats.Tests.pending',
                'name' => 'Tests Pending',
                'color' => '#0000FF',
                'created' => new DateTime(),
                'last_updated' => new DateTime(),
                'active' => true,
            ],
            [
                'id' => 4,
                'okey' => 'Stats.Tests.nocounts',
                'name' => 'Tests No Counts',
                'color' => '#FF0000',
                'created' => new DateTime(),
                'last_updated' => new DateTime(),
                'active' => true,
            ],
            [
                'id' => 5,
                'okey' => 'Stats.Tests.inactive',
                'name' => 'Tests Inactive',
                'color' => '#CCCCCC',
                'created' => new DateTime(),
                'last_updated' => new DateTime(),
                'active' => false,
            ],
        ];
        parent::init();
    }
}
