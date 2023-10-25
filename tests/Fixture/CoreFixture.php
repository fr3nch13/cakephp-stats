<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Test\Fixture;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * Core Fixture
 */
class CoreFixture extends TestFixture
{
    /**
     * @var \Cake\Console\ConsoleIo
     */
    public $io;

    public function __construct()
    {
        $this->loadIo();
        parent::__construct();
    }

    /**
     * Loads the I/O for the console.
     */
    public function loadIo(): void
    {
        if (!$this->io) {
            $this->io = new ConsoleIo();
        }
    }
}
