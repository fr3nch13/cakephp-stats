<?php
declare(strict_types=1);

/**
 * Used by controllers for chartjs graphs,
 * but can also be used just to get the data you need in a controller
 */
namespace Fr3nch13\Stats\Controller;

use Cake\Datasource\ModelAwareTrait;
use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\Utility\Inflector;
use Fr3nch13\Stats\Model\Table\StatsCountsTable;

/**
 * ChartJsTrait
 */
trait ChartJsTrait
{
    use ModelAwareTrait;

    /**
     * Performs the common chartJs Line graph.
     *
     * @param array<string> $keys The keys to lookup.
     * @param mixed|null $range Go back x number of stats.
     * @param string|null $timeperiod The Interval for the line graph
     * @param string|null $title The title to use, if needed.
     * @param \Cake\I18n\DateTime|null $start The time that we want to start. Defaults to now.
     * @return \Cake\Http\Response|null
     */
    public function chartJsLine(
        array $keys,
        ?int $range = null,
        ?string $timeperiod = null,
        ?string $title = null,
        ?DateTime $start = null,
        array $ids = []
    ): ?Response {
        // redirect so the frontend url is correct.
        if (!$range || !$timeperiod) {
            return $this->redirect(['action' => $this->getRequest()->getParam('action'), 7, 'day']);
        }

        /** @var \Fr3nch13\Stats\Model\Table\StatsCountsTable $StatsCounts */
        $StatsCounts = $this->fetchModel(StatsCountsTable::class);
        $StatsCounts->checkTimePeriod($timeperiod);

        if (!$start) {
            $start = new DateTime();
        }

        $stats = $StatsCounts->getobjectsCounts($keys, $start, (int)$range, $timeperiod);

        $this->set([
            'stats' => $stats,
            'title' => $title,
            'timeperiod' => $timeperiod,
            'timeperiodPlural' => Inflector::pluralize($timeperiod),
            'range' => $range,
            'timeperiods' => $StatsCounts->getTimePeriods(),
        ]);

        return null;
    }
}
