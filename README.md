# Stats

[![pipeline status](https://dfir-bedlam.ocio.nih.gov/sis-plugins/stats/badges/master/pipeline.svg)](https://dfir-bedlam.ocio.nih.gov/sis-plugins/stats/-/commits/master)
[![coverage report](https://dfir-bedlam.ocio.nih.gov/sis-plugins/stats/badges/master/coverage.svg)](https://dfir-bedlam.ocio.nih.gov/sis-plugins/stats/-/commits/master)

Used to track and display statistics, and Trends.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

Add Satis to your `composer.json` file first:
(make sure packagist is listed first)
```json
    "repositories": [
        {
            "type": "composer",
            "url": "https://packagist.org"
        },
        {
            "type": "composer",
            "url": "https://satis.ocio.nih.gov",
            "options": {
                "ssl": {
                    "allow_self_signed": true,
                    "verify_peer": false
                }
            }
        }
    ],
```


The recommended way to install composer packages is:

```bash
composer require sis-plugins/stats
```

## Usage

There are two main components of this plugin. The `StatsListener`, and the `StatsBehavior`. The `StatsListener` is designed to be triggered from within a `Model/Table` as it uses the `StatsBehavior` to save/update entities and their counts.

To use this plugin, you need to extend the `StatsListener`, define your `Entities` in it, and register your listener.
As an example of how to extend the `StatsListener` and define your `Entities`, see the: [`TestListener`](src/Event/TestListener.php). For more on how CakePHP 4 Events work, see [The Event System](https://book.cakephp.org/4/en/core-libraries/events.html)

src/Event/TestListener.php:
```php
<?php
declare(strict_types=1);

namespace Sis\Stats\Event;

class TestListener extends StatsListener
{
    public function implementedEvents(): array
    {
        $listeners = parent::implementedEvents();

        $listeners += [
            'Stats.Stats.Test.before' => 'checkAddBefore',
            'Stats.Stats.Test.after' => 'checkAddAfter',
        ];

        return $listeners;
    }

    public function checkAddBefore(\Cake\Event\Event $event): bool
    {
        $this->setEntityPrefix('Stats.Stats.Test');
        $entities = [];
        $entities['total'] = [
            'name' => __('Total'),
            'color' => '#000000',
        ];
        $entities['new'] = [
            'name' => __('New'),
            'color' => '#0000FF',
            'increment' => true,
        ];
        $entities['updated'] = [
            'name' => __('Updated'),
            'color' => '#FFFF00',
            'increment' => true,
        ];

        $this->setEntities($entities);

        return $this->onBefore($event);
    }

    public function checkAddAfter(\Cake\Event\Event $event): bool
    {
        $cronCounts = $event->getSubject()->cronCounts();
        foreach ($cronCounts as $key => $count) {
            $this->updateEntityCount($key, (int)$count);
        }

        return $this->onAfter($event);
    }
}

```

Once you've created your Listener that has been extended from the `StatsListener`, you need to register it. In either your `Application.php` (if you're directly using it within an app), or your `Plugin.php` (if your using this within another plugin), you need to Use the EventManager to register your Listener in the `bootstrap()` method. For an example, See: [`Plugin.php`](src/Plugin.php)'s `bootstrap()`.

src/Plugin.php
```php
<?php
declare(strict_types=1);

namespace Sis\Stats;

use Cake\Core\BasePlugin;
use Cake\Event\EventManager;
use Sis\Stats\Event\TestListener;


class Plugin extends BasePlugin
{
    // other code

    public function bootstrap(\Cake\Core\PluginApplicationInterface $app): void
    {
        // Register your listener with the Event Manager
        EventManager::instance()->on(new TestListener());

        parent::bootstrap($app);
    }

    /// other code
}

```

To trigger the onBefore and onAfter, you do it where ever you want to track the counts, like in a cron job, or something along those lines. The `StatsListener` also requires that you have a `cronCounts()` method as this is where the onAfter method will get the counts from. See: [`TestsTable`](src/Model/Table/TestsTable.php)'s `testStatsListener()` method on how to trigger the events.

src/Model/Table/ExampleTable.php
```php
<?php
declare(strict_types=1);

namespace Sis\Stats\Model\Table;

use Cake\Event\Event;

class TestsTable extends \Sis\Core\Model\Table\Table
{
    public $stats = [
        'total' => 0,
        'new' => 0,
        'updated' => 0,
    ];

    /// other code

    public function testStatsListener(): void
    {
        // trigger the onBefore()
        $event = $this->getEventManager()->dispatch(new Event('Stats.Stats.Test.before', $this));

        // do stuff that updates the stats that match the same keys as your defined in your
        // Listener::onBefore() method.
        $this->stats['total'] = 10;
        $this->stats['new'] = 5;
        $this->stats['updated'] = 3;

        // trigger the Sis\Stats\Event\TestListener::onAfter();
        $this->getEventManager()->dispatch(new Event('Stats.Stats.Test.after', $this));
    }

    public function cronCounts(): array
    {
        return [
            'total' => $this->stats['total'],
            'new' => $this->stats['new'],
            'updated' => $this->stats['updated'],
        ];
    }
}

```

You can either include the `StatsBehavior` directly, like you would any other Behavior, but it's not needed as the `StatsListener` will add the behavior when it needs it. If you're using it within a Controller you can use the [`DbLineTrait`](src/Controller/DbLineTrait.php) which includes the `dbLineCommon()` method. This is a helper function for creating the view that displays the counts in a graph.

See: [`TestsController`](src/Controller/TestsController.php).
```php
<?php
declare(strict_types=1);

namespace Sis\Stats\Controller;

class TestsController extends AppController
{
    use DbLineTrait;

    // other code

    public function dbLineTrait($range = null, ?string $timeperiod = null): ?\Cake\Http\Response
    {
        $keys = [
            'Stats.Stats.Test.total',
            'Stats.Stats.Test.new',
            'Stats.Stats.Test.updated',
            'Stats.Stats.Test.active,',
        ];

        // $this->Tests is the model that the behavior will be attached to.
        return $this->dbLineCommon($this->Tests, $keys, $range, $timeperiod);
    }

    // other code
}

```
