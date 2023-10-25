<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Migrations;

use Cake\Console\ConsoleIo;
use Phinx\Db\Adapter\MysqlAdapter;

trait QrMigrationTrait
{
    /**
     * @var \Cake\Console\ConsoleIo console object
     */
    public $io;

    /**
     * Makes the I/O object
     *
     * @return void
     */
    public function makeIo(): void
    {
        if (!$this->io) {
            $this->io = new ConsoleIo();
        }
    }

    /**
     * The default table options
     *
     * @return array The array of options
     */
    public function tableOptions(): array
    {
        return [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'Dynamic',
        ];
    }

    /**
     * The default options for the primary key
     *
     * @return array
     */
    public function primaryKeyOptions(): array
    {
        return [
            'null' => false,
            'limit' => MysqlAdapter::INT_REGULAR,
            'precision' => 10,
            'identity' => true,
        ];
    }

    /**
     * Run stuff before Change
     *
     * @return void
     */
    public function beforeChange(): void
    {
        $this->makeIo();
        $this->io->out(__('--- Running Migration: {0}:beforeChange ---', [self::class]));
        if ($this->getAdapter()->getAdapterType() == 'mysql') {
            $this->execute('SET UNIQUE_CHECKS = 0;');
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->execute("ALTER DATABASE CHARACTER SET 'utf8mb4';");
            $this->execute("ALTER DATABASE COLLATE='utf8mb4_general_ci';");
        }
    }

    /**
     * Run stuff after Change
     *
     * @return void
     */
    public function afterChange(): void
    {
        $this->makeIo();
        $this->io->out(__('--- Running Migration: {0}:afterChange ---', [self::class]));
        if ($this->getAdapter()->getAdapterType() == 'mysql') {
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
            $this->execute('SET UNIQUE_CHECKS = 1;');
        }
    }
}
