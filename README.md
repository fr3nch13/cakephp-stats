# Stats

Used to track and display statistics, and Trends.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```bash
composer require fr3nch13/cakephp-stats
```

## Usage

There are two main components of this plugin. The `StatsListener`, and the `StatsBehavior`. The `StatsListener` is designed to be triggered from within a `Model/Table` as it uses the `StatsBehavior` to save/update objects and their counts.

To use this plugin, you need to extend the `StatsListener`, define your `objects` in it, and register your listener.
As an example of how to extend the `StatsListener` and define your `objects`, see the: [`TestListener`](src/Event/TestListener.php). For more on how CakePHP 5 Events work, see [The Event System](https://book.cakephp.org/5/en/core-libraries/events.html)

src/Event/TestListener.php:
```php
<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Event;

class TestListener extends StatsListener
{
    public function implementedEvents(): array
    {
        $listeners = parent::implementedEvents();

        $listeners += [
            'Stats.Test.before' => 'checkAddBefore',
            'Stats.Test.after' => 'checkAddAfter',
        ];

        return $listeners;
    }

    public function checkAddBefore(\Cake\Event\Event $event): bool
    {
        $this->setObjectPrefix('Stats.Test');
        $objects = [];
        $objects['total'] = [
            'name' => __('Total'),
            'color' => '#000000',
        ];
        $objects['new'] = [
            'name' => __('New'),
            'color' => '#0000FF',
        ];
        $objects['updated'] = [
            'name' => __('Updated'),
            'color' => '#FFFF00',
        ];

        $this->setobjects($objects);

        return $this->onBefore($event);
    }

    public function checkAddAfter(\Cake\Event\Event $event): bool
    {
        $statsCounts = $event->getSubject()->statsCounts();
        foreach ($statsCounts as $key => $count) {
            $this->updateObjectCount($key, (int)$count);
        }

        return $this->onAfter($event);
    }
}

```

Once you've created your Listener that has been extended from the `StatsListener`, you need to register it. In either your `Application.php` (if you're directly using it within an app), or your `Plugin.php` (if your using this within another plugin), you need to Use the EventManager to register your Listener in the `bootstrap()` method. For an example, See: [`StatsPlugin.php`](src/StatsPlugin.php)'s `bootstrap()`.

src/StatsPlugin.php
```php
<?php
declare(strict_types=1);

namespace Fr3nch13\Stats;

use Cake\Core\BasePlugin;
use Cake\Event\EventManager;
use Fr3nch13\Stats\Event\TestListener;


class StatsPlugin extends BasePlugin
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

To trigger the onBefore and onAfter, you do it where ever you want to track the counts, like in a cron job, or something along those lines. The `StatsListener` also requires that you have a `statsCounts()` method as this is where the onAfter method will get the counts from. See: [`TestsTable`](src/Model/Table/TestsTable.php)'s `testStatsListener()` method on how to trigger the events.

src/Model/Table/ExampleTable.php
```php
<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Model\Table;

use Cake\Event\Event;

class TestsTable extends \Cake\ORM\Table
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
        $event = $this->getEventManager()->dispatch(new Event('Stats.Test.before', $this));

        // do stuff that updates the stats that match the same keys as your defined in your
        // Listener::onBefore() method.
        $this->stats['total'] = 10;
        $this->stats['new'] = 5;
        $this->stats['updated'] = 3;

        // trigger the Fr3nch13\Stats\Event\TestListener::onAfter();
        $this->getEventManager()->dispatch(new Event('Stats.Test.after', $this));
    }

    public function statsCounts(): array
    {
        // just return the stats
        return $this->stats;

        // or, if needed, do something to them before returniing them
    }
}

```

You can either include the `StatsBehavior` directly, like you would any other Behavior, but it's not needed as the `StatsListener` will add the behavior when it needs it. If you're using it within a Controller you can use the [`DbLineTrait`](src/Controller/DbLineTrait.php) which includes the `dbLineCommon()` method. This is a helper function for creating the view that displays the counts in a graph.

See: [`TestsController`](src/Controller/TestsController.php).
```php
<?php
declare(strict_types=1);

namespace Fr3nch13\Stats\Controller;

class TestsController extends AppController
{
    use DbLineTrait;

    // other code

    public function dbLineTrait($range = null, ?string $timeperiod = null): ?\Cake\Http\Response
    {
        $keys = [
            'Stats.Test.total',
            'Stats.Test.new',
            'Stats.Test.updated',
            'Stats.Test.active,',
        ];

        // $this->Tests is the model that the behavior will be attached to.
        return $this->dbLineCommon($this->Tests, $keys, $range, $timeperiod);
    }

    // other code
}

```
