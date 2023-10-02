<?php
declare(strict_types=1);

/**
 * Test suite bootstrap.
 *
 * This function is used to find the location of CakePHP whether CakePHP
 * has been installed as a dependency of the plugin, or the plugin is itself
 * installed as a dependency of an application.
 */

use Cake\Core\Configure;

// Configure your stuff here for the plugin_bootstrap.php below.
define('TESTS', __DIR__ . DS);

$localEnv = TESTS . '.env';

if (\file_exists($localEnv)) {
    $dotenv = new \Symfony\Component\Dotenv\Dotenv();
    $dotenv->load($localEnv);
}

$dbconfig = [
    'className' => \Cake\Database\Connection::class,
    'driver' => \Cake\Database\Driver\Mysql::class,
    'persistent' => false,
    'host' => env('CI_HOST', null),
    'username' => env('CI_USERNAME', null),
    'password' => env('CI_PASSWORD', null),
    'database' => env('CI_DATABASE', null),
    'encoding' => 'utf8',
    'timezone' => 'UTC',
    'flags' => [],
    'cacheMetadata' => true,
    'log' => false,
    'quoteIdentifiers' => true,
];

Configure::write('Tests.DbConfig', $dbconfig);

Configure::write('Tests.Plugins', [
    'Sis/Core',
    'Sis/Notifications',
    'Sis/Orgs',
    'Sis/Stats',
    'Sis/Users',
]);

Configure::write('Tests.Migrations', [
    ['plugin' => 'Sis/Core'],
    ['plugin' => 'Sis/Notifications'],
    ['plugin' => 'Sis/Orgs'],
    ['plugin' => 'Sis/Stats'],
    ['plugin' => 'Sis/Users'],
]);

Configure::write('Tests.Fixtures', [
    'init' => true,
    'insert' => false,
    'truncate' => false,
]);

////// Ensure we can setup an environment for the Test Application instance.
$root = dirname(__DIR__);
chdir($root);
require_once $root . '/vendor/fr3nch13/cakephp-pta/tests/plugin_bootstrap.php';
