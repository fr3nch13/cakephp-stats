# CakePHP Stats Plugin

[![Build Status](https://github.com/fr3nch13/cakephp-stats/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/fr3nch13/cakephp-stats/actions/workflows/ci.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/fr3nch13/cakephp-stats.svg?style=flat-square)](https://packagist.org/packages/fr3nch13/cakephp-stats)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)
[![codecov](https://codecov.io/gh/fr3nch13/cakephp-stats/graph/badge.svg?token=xHC0xjLXxq)](https://codecov.io/gh/fr3nch13/cakephp-stats)

Used to track and display statistics, and Trends.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```bash
composer require fr3nch13/cakephp-stats
```

## Usage

This all revolves around the event listener [`StatsListener`](src/Event/StatsListener.php).

To use this plugin, you need to extend the `StatsListener`, and define your `StatsObject` `keys` in it.
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
        // Article.hits is a StatsObject key.
        parent::recordCount($event, 'Articles.hits'); // leave out count to just increment by one.

        // track the specific article
        // Article.hits.[id] is a seperate StatsObject key from above.
        parent::recordCount($event, 'Articles.hits.' . $articleId, $count);
    }
}

```

Once you've created your Listener that has been extended from the `StatsListener`, you need to register it. In either your `Application.php` (if you're directly using it within an app), or your `Plugin.php` (if your using this within another plugin), you need to Use the EventManager to register your Listener in the `bootstrap()` method. For an example, See: [`StatsPlugin.php`](src/StatsPlugin.php)'s `bootstrap()`.

src/BlogPlugin.php

```php
<?php
declare(strict_types=1);

namespace Fr3nch13\Blog;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Fr3nch13\Stats\Event\TestListener;


class BlogPlugin extends BasePlugin
{
    // other code

    public function bootstrap(PluginApplicationInterface $app): void
    {
        // Register your listener with the Event Manager
        EventManager::instance()->on(new ArticleListener());

        parent::bootstrap($app);
    }

    /// other code
}

```

src/Controller/ArticlesController.php

```php
<?php
declare(strict_types=1);

namespace Fr3nch13\Blog\Controller;

use Cake\Event\Event;
use Fr3nch13\Blog\AppController;

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
        $this->getEventManager()->dispatch(new Event('App.Article.hit', $this, [
            'articleId' => $id,
            'count' => 1,
        ]));
    }
}

```

To use the controller trait, you can do so like:

See: [`TestsController`](src/Controller/TestsController.php).

src/Controller/Admin/ArticlesController.php

```php
<?php
declare(strict_types=1);

namespace Fr3nch13\Blog\Admin\Controller;

use Fr3nch13\Stats\Controller\ChartJsTrait;
use Fr3nch13\Blog\Admin\AppController;

class ArticlesController extends AppController
{
    /**
     * Used to do the common tasks for chartjs graphs.
     */
    use ChartJsTrait;

    // other code

    public function line(?int $range = null, ?string $timeperiod = null): ?Response
    {
        $keys = [
            'Articles.hits',
            'Articles.hits.1',
            'Articles.hits.2',
            'Articles.hits.3',
        ];

        return $this->chartJsLine($keys, $range, $timeperiod);
    }

    // other code

    /**
     * To get the stats in a dashboard
     *
     * @return ?\Cake\Http\Response Renders view
     */
    public function dashboard(): ?Response
    {
        /** @var \Fr3nch13\Stats\Model\Table\StatsCountsTable $StatsCounts */
        $StatsCounts = $this->getTableLocator()->get('Fr3nch13/Stats.StatsCounts');

        $stats = $this->StatsCounts->getObjectStats('Articles.hits');

        /*
        $stats will look like:
        $stats = [
            'year' => 12001, <-- counts
            'month' => 3001,
            'week' => 701,
            'day' => 101,
            'hour' => 11,
        ];
        */

        $this->set(compact('stats'));
        $this->viewBuilder()->setOption('serialize', ['stats']);

        return null;
    }
}

```
