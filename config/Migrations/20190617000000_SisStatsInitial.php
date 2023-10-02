<?php

declare(strict_types=1);

/**
 * Migration Definition.
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Generic.Files.LineLength.TooLong

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * Initial Schema
 */
class SisStatsInitial extends AbstractMigration
{
    /**
     * Create the tables.
     *
     * @return void
     */
    public function up(): void
    {
        $io = new ConsoleIo();
        $io->out(__('--- Running Migration: {0}:up ---', [self::class]));
        if ($this->getAdapter()->getAdapterType() == 'mysql') {
            $this->execute('SET UNIQUE_CHECKS = 0;');
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->execute("ALTER DATABASE CHARACTER SET 'utf8mb4';");
            $this->execute("ALTER DATABASE COLLATE='utf8mb4_general_ci';");
        }

        if (!$this->hasTable('stats_entities')) {
            $io->out(__('Creating Table: {0}', ['stats_entities']));
            $table = $this->table('stats_entities', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'comment' => '', 'row_format' => 'Dynamic']);
            $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable']);
            $table->addColumn('key', 'string', ['null' => true, 'limit' => 255, 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'after' => 'id']);
            $table->addColumn('name', 'string', ['null' => true, 'limit' => 255, 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'after' => 'key']);
            $table->addColumn('description', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG, 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'after' => 'name']);
            $table->addColumn('created', 'datetime', ['null' => true, 'after' => 'description']);
            $table->addColumn('modified', 'datetime', ['null' => true, 'after' => 'created']);
            $table->addColumn('last_updated', 'datetime', ['null' => true, 'after' => 'modified']);
            $table->addColumn('active', 'boolean', ['null' => false, 'default' => '1', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'last_updated']);
            $table->addColumn('ic_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'active']);
            $table->addIndex(['key'], ['name' => 'key', 'unique' => false]);
            $table->addIndex(['active'], ['name' => 'active', 'unique' => false]);
            $table->addIndex(['ic_id'], ['name' => 'ic_id', 'unique' => false]);
            $table->save();
        }
        if (!$this->hasTable('stats_counts')) {
            $io->out(__('Creating Table: {0}', ['stats_counts']));
            $table = $this->table('stats_counts', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'comment' => '', 'row_format' => 'Dynamic']);
            $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable']);
            $table->addColumn('stats_entity_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'id']);
            $table->addColumn('time_period', 'string', ['null' => true, 'limit' => 20, 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'after' => 'stats_entity_id']);
            $table->addColumn('time_stamp', 'integer', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'time_period']);
            $table->addColumn('time_count', 'integer', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'time_stamp']);
            $table->addIndex(['stats_entity_id'], ['name' => 'stats_entity_id', 'unique' => false]);
            $table->addIndex(['time_stamp'], ['name' => 'time_stamp', 'unique' => false]);
            $table->addIndex(['time_period'], ['name' => 'time_period', 'unique' => false]);
            $table->save();
        }
        // only create this table if we're doing unit testing.
        if(Configure::read('Tests.Migrations')) {
            if (!$this->hasTable('stats_tests')) {
                $io->out(__('Creating Table: {0}', ['stats_tests']));
                $table = $this->table('stats_tests', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'comment' => '', 'row_format' => 'Dynamic']);
                $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable']);
                $table->addColumn('name', 'string', ['null' => true, 'limit' => 20, 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'after' => 'stats_entity_id']);
                $table->save();
            }
        }

        $io->out(__('Adding Foreign Keys'));
        $this->table('stats_entities')->addForeignKey('ic_id', 'ics', 'id', ['constraint' => 'stats_entities_ibfk_1', 'update' => 'RESTRICT', 'delete' => 'CASCADE'])->save();
        $this->table('stats_counts')->addForeignKey('stats_entity_id', 'stats_entities', 'id', ['constraint' => 'stats_counts_ibfk_1', 'update' => 'RESTRICT', 'delete' => 'CASCADE'])->save();

        if ($this->getAdapter()->getAdapterType() == 'mysql') {
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
            $this->execute('SET UNIQUE_CHECKS = 1;');
        }
    }

    /**
     * Drops tables.
     *
     * @return void
     */
    public function down(): void
    {
        $io = new ConsoleIo();
        $io->out(__('--- Running Migration: {0}:down ---', [self::class]));
        if ($this->getAdapter()->getAdapterType() == 'mysql') {
            $this->execute('SET UNIQUE_CHECKS = 0;');
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->execute("ALTER DATABASE CHARACTER SET 'utf8mb4';");
            $this->execute("ALTER DATABASE COLLATE='utf8mb4_general_ci';");
        }

        if ($this->hasTable('stats_entities')) {
            $io->out(__('Dropping the table: {0}', ['stats_entities']));
            $this->table('stats_entities')->drop()->save();
        }
        if ($this->hasTable('stats_counts')) {
            $io->out(__('Dropping the table: {0}', ['stats_counts']));
            $this->table('stats_counts')->drop()->save();
        }
        if ($this->hasTable('stats_tests')) {
            $io->out(__('Dropping the table: {0}', ['stats_tests']));
            $this->table('stats_tests')->drop()->save();
        }

        if ($this->getAdapter()->getAdapterType() == 'mysql') {
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
            $this->execute('SET UNIQUE_CHECKS = 1;');
        }
    }
}
