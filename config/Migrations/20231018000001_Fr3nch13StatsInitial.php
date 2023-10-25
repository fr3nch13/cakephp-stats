<?php

declare(strict_types=1);

/**
 * Migration Definition.
 */

use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;
use Cake\Core\Configure;

/**
 * Initial Schema
 */
class Fr3nch13StatsInitial extends AbstractMigration
{
    use \Fr3nch13\Stats\Migrations\QrMigrationTrait;
    /**
     * Create the tables.
     *
     * @return void
     */
    public function change(): void
    {
        $this->beforeChange();

        $this->io->out(__('--- Running Migration: {0}:up ---', [self::class]));

        $this->io->out(__('Creating table: {0}', ['stats_objects']));
        $table = $this->table('stats_objects', $this->tableOptions());
        $table->addColumn('id', 'integer', $this->primaryKeyOptions());
        $table->addColumn('okey', 'string')
            ->addIndex('okey');
        $table->addColumn('name', 'string');
        $table->addColumn('description', 'text');
        $table->addColumn('created', 'datetime');
        $table->addColumn('modified', 'datetime');
        $table->addColumn('last_updated', 'datetime');
        $table->addColumn('active', 'boolean', ['default' => true])
            ->addIndex('active');
        $table->addColumn('color', 'string');
        $table->create();

        $this->io->out(__('Creating table: {0}', ['stats_counts']));
        $table = $this->table('stats_counts', $this->tableOptions());
        $table->addColumn('id', 'integer', $this->primaryKeyOptions());
        $table->addColumn('stats_object_id', 'integer', ['null' => false])
            ->addIndex('stats_object_id');
        $table->addColumn('time_period', 'string')
            ->addIndex('time_period');
        $table->addColumn('time_stamp', 'integer')
            ->addIndex('time_stamp');
        $table->addColumn('time_count', 'integer');
        $table->addForeignKey('stats_object_id', 'stats_objects', 'id', [
            'update' => 'RESTRICT',
            'delete' => 'CASCADE',
            'constraint' => 'stat_counts_stat_object_id',
        ]);
        $table->create();

        // only create this table if we're doing unit testing.
        if(Configure::read('Tests.Migrations')) {
            $this->io->out(__('Creating Table: {0}', ['stats_tests']));
            $table = $this->table('stats_tests', $this->tableOptions());
            $table->addColumn('id', 'integer', $this->primaryKeyOptions());
            $table->addColumn('name', 'string');
            $table->create();
        }

        $this->afterChange();
    }
}
