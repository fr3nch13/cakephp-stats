<?php

declare(strict_types=1);

/**
 * Migration Definition.
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Generic.Files.LineLength.TooLong

use Cake\Console\ConsoleIo;
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * Add Color To Entities
 */
class SisStatsAddColorToEntities extends AbstractMigration
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
        
        if ($this->hasTable('stats_entities')) {
            $io->out(__('Adding fields to Table: {0}', ['stats_entities']));
            $table = $this->table('stats_entities');
            if (!$table->hasColumn('color')) {
                $io->out(__('Adding column {0}.{1}', ['stats_entities', 'color']));
                $table->addColumn('color', 'string', ['null' => true, 'limit' => 10, 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci', 'after' => 'description'])
                    ->save();
            }
        }

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
        
        $io->out(__('Updating UID for Table: {0}', ['stats_entities']));
        $this->table('stats_entities')->changeColumn('id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'identity' => true])->update();

        $io->out(__('Updating UID for Table: {0}', ['stats_counts']));
        $this->table('stats_counts')->changeColumn('id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'identity' => true])->update();

        if ($this->getAdapter()->getAdapterType() == 'mysql') {
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
            $this->execute('SET UNIQUE_CHECKS = 1;');
        }
    }
}
