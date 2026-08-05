# CakePHP Stats Plugin

Track event-driven statistics and render Chart.js line charts in CakePHP applications.

## Installation

Install the plugin with [Composer](https://getcomposer.org/):

```bash
composer require fr3nch13/cakephp-stats
```

## Usage

Statistics are recorded through the [`StatsListener`](src/Event/StatsListener.php). Create an application listener that extends it and maps your application's events to listener methods.

See [CakePHP's event system](https://book.cakephp.org/5/en/core-libraries/events.html) for more on dispatching and handling events.

### Create A Listener

`src/Event/ArticleListener.php`

```php
<?php
declare(strict_types=1);

namespace App\Event;

use Cake\Event\Event;
use Fr3nch13\Stats\Event\StatsListener;

class ArticleListener extends StatsListener
{
    public function implementedEvents(): array
    {
        return [
            'App.Article.hit' => 'onHit',
        ];
    }

    public function onHit(Event $event, int $articleId, int $count = 1): void
    {
        // Track all article views.
        parent::recordCount($event, 'Articles.hits', $count);

        // Track views for this specific article.
        parent::recordCount($event, 'Articles.hits.' . $articleId, $count);
    }
}
```

`recordCount()` stores the last registered `StatsObject` on the event result. It does not return the object directly; use `$event->getResult()` after dispatching when you need it.

### Register The Listener

Register the listener during your application or plugin bootstrap.

`src/Application.php`

```php
<?php
declare(strict_types=1);

namespace App;

use App\Event\ArticleListener;
use Cake\Event\EventManager;
use Cake\Http\BaseApplication;

class Application extends BaseApplication
{
    public function bootstrap(): void
    {
        parent::bootstrap();

        EventManager::instance()->on(new ArticleListener());
    }
}
```

### Record A Statistic

Dispatch the application event after the work that can fail or redirect has completed.

`src/Controller/ArticlesController.php`

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Response;

class ArticlesController extends AppController
{
    /**
     * Example of how to register a hit
     */
    public function view(int $id): ?Response
    {
        $article = $this->Articles->get($id);

        // Dispatch this immediately before rendering so an earlier error or redirect does not record a view.
        $this->getEventManager()->dispatch(new Event('App.Article.hit', $this, [
            'articleId' => $id,
            'count' => 1,
        ]));

        return null;
    }
}
```

### Render A Chart

Use [`ChartJsTrait`](src/Controller/ChartJsTrait.php) in a controller action. When `$range` or `$timeperiod` is omitted, the trait redirects to a URL with the defaults of seven days.

`src/Controller/Admin/ArticlesController.php`

```php
<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Response;
use Fr3nch13\Stats\Controller\ChartJsTrait;

class ArticlesController extends AppController
{
    use ChartJsTrait;

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
}
```

### Read Statistics

Retrieve aggregate counts directly from the plugin table when building an API response or dashboard.

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Response;

class ArticlesController extends AppController
{

    /**
     * To get the stats in a dashboard
     *
     * @return ?\Cake\Http\Response Renders view
     */
    public function dashboard(): ?Response
    {
        /** @var \Fr3nch13\Stats\Model\Table\StatsCountsTable $StatsCounts */
        $StatsCounts = $this->getTableLocator()->get('Fr3nch13/Stats.StatsCounts');

        $stats = $StatsCounts->getObjectStats('Articles.hits');

        $this->set(compact('stats'));
        $this->viewBuilder()->setOption('serialize', ['stats']);

        return null;
    }
}
```
