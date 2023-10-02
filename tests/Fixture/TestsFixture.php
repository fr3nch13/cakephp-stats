<?php

declare(strict_types=1);

namespace Sis\Stats\Test\Fixture;

use Cake\Core\Configure;
use Sis\Core\Test\Fixture\CoreFixture;

/**
 * Tests Fixture
 */
class TestsFixture extends CoreFixture
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
            'model' => 'Sis/Stats.Tests',
        ];

        $this->records = [
            ['id' => 1, 'name' => 'Test 1'],
            ['id' => 2, 'name' => 'Test 2'],
            ['id' => 3, 'name' => 'Test 3'],
            ['id' => 4, 'name' => 'Test 4'],
            ['id' => 5, 'name' => 'Test 5'],
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
