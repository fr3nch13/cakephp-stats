# Stats

Used to track and display statistics, and Trends.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```bash
composer require fr3nch13/cakephp-stats
```

## Usage

This all revolves around the event listener [`StatsListener`](src/Event/StatsListener.php).

To use this plugin, you need to extend the `StatsListener`, define your `StatsObject` `keys` in it, and register your listener.
As an example of how to extend the `StatsListener` and define your `objects`.

See [CakePHP's Event System](https://book.cakephp.org/5/en/core-libraries/events.html#events-system)

src/Event/TestListener.php:
```php
<?php
declare(strict_types=1);

namespace App\Event;

use Cake\Event\Event;

class ArticleListener extends StatsListener
{
    // Define your events here
    public function implementedEvents(): array
    {
        return [
            'App.Article.hit' => 'onHit',
        ];
    }

    public function onHit(Event $event, int $articleId, int $count = 1): bool
    {
        // track if any articles were viewed
        parent::recordCount($event, 'Articles.hits'); // leave out count to just increment by one.

        // track the specific article
        parent::recordCount($event, 'Articles.hits.' . $articleId, 1);
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


src/Controller/ArticlesController.php
```php
<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\Event;

class ArticlesController extends AppController
{
    /**
     * Example of how to register a hit
     */
    public function view(int $id): ?Response
    {
        $article = $this->Articles->get($id);

        // do this reight before rendering the view incase your code above throws an error,
        // or redirects somewhere else.
        $this->getEventManager()->dispatch(new \Cake\Event\Event('App.Article.hit', $this, [
            'articleId' => $id,
            'count' => 1,
        ]));
    }
}

```

To use the controller trait, you can do so like:

See: [`TestsController`](src/Controller/TestsController.php).
```php
<?php
declare(strict_types=1);

namespace App\Controller;

use Fr3nch13\Stats\Controller\ChartJsTrait;

class TestsController extends AppController
{
    /**
     * Used to do the common tasks for chartjs graphs.
     */
    use ChartJsTrait;

    // other code

    public function line(?int $range = null, ?string $timeperiod = null): ?Response
    {
        $keys = [
            'Stats.Tests.open',
            'Stats.Tests.closed',
            'Stats.Tests.pending',
            'Stats.Tests.nocounts',
        ];

        return $this->chartJsLine($keys, $range, $timeperiod);
    }

    // other code
}

```
