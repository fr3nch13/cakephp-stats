<?php

declare(strict_types=1);

namespace Fr3nch13\Stats\Test\Fixture;

/**
 * Tests Fixture
 */
class TestsFixture extends CoreFixture
{
    /**
     * Table property
     *
     * @var string
     */
    public string $table = 'stats_tests';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->io->out(__('--- Init Fixture: {0} ---', [self::class]));
        $this->records = [
            ['id' => 1, 'name' => 'Test 1'],
            ['id' => 2, 'name' => 'Test 2'],
            ['id' => 3, 'name' => 'Test 3'],
            ['id' => 4, 'name' => 'Test 4'],
            ['id' => 5, 'name' => 'Test 5'],
        ];
        parent::init();
    }
}
