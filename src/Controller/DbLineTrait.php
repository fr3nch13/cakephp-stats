<?php
declare(strict_types=1);

/**
 * Used by controllers for the common methods.
 */
namespace Fr3nch13\Stats\Controller;

use Cake\Datasource\ModelAwareTrait;
use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\Utility\Inflector;
use Fr3nch13\Stats\Model\Table\StatsCountsTable;

/**
 * DbLineTrait
 */
trait DbLineTrait
{
    use ModelAwareTrait;
    /**
     * Performs the common DB Linegraph functions.
     *
     * @param array<string> $keys The keys to lookup.
     * @param mixed|null $range Go back x number of stats.
     * @param string|null $timeperiod The Interval for the line graph
     * @param string|null $title The title to use, if needed.
     * @param \Cake\I18n\DateTime|null $start The time that we want to start. Defaults to now.
     * @return \Cake\Http\Response|null
     */
    public function dbLineCommon(
        array $keys,
        mixed $range = null,
        ?string $timeperiod = null,
        ?string $title = null,
        ?DateTime $start = null,
        array $ids = []
    ): ?Response {
        // redirect so the frontend url is correct.
        if (!$range || !$timeperiod) {
            return $this->redirect(['action' => $this->getRequest()->getParam('action'), 7, 'day']);
        }
        $StatsCounts = $this->fetchModel(StatsCountsTable::class);

        $timeperiods = $StatsCounts->getTimePeriods();
        if (!in_array($timeperiod, $timeperiods)) {
            $timeperiod = 'day';
        }

        if (!$start) {
            $start = new DateTime();
        }

        $oldKeys = $keys;
        // multiple Ids that we need to combine.
        if ($ids) {
            $keys = [];
            foreach ($ids as $id) {
                foreach ($oldKeys as $key) {
                    if (strpos($key, '__id__') !== false) {
                        $newKey = str_replace('__id__', strval($id), $key);
                        $keys[$newKey] = $newKey;
                    }
                }
            }
        }

        $stats = $StatsCounts->getobjectsCounts($keys, $start, (int)$range, $timeperiod);

        // try to combine the counts.
        if ($ids) {
            $combinedStats = [];
            foreach ($oldKeys as $oldKey) {
                $newKey = str_replace('__id__', '0', $oldKey);
                $combinedStats[$newKey] = [];
            }
            foreach ($ids as $id) {
                $myKeys = [];
                foreach ($oldKeys as $oldKey) {
                    $myKey = str_replace('__id__', strval($id), $oldKey);
                    $combinedKey = str_replace('__id__', '0', $oldKey);
                    $myKeys[$myKey] = $combinedKey;
                }
                foreach ($myKeys as $myKey => $combinedKey) {
                    if (isset($stats[$myKey])) {
                        // add the entity to the combined stats
                        if (isset($stats[$myKey]['entity']) && !isset($combinedStats[$combinedKey]['entity'])) {
                            $entity = $stats[$myKey]['entity'];
                            $entity->set('key', $combinedKey);
                            $combinedStats[$combinedKey]['entity'] = $entity;
                        }
                        // if it has counts set
                        if (isset($stats[$myKey]['counts']) && $stats[$myKey]['counts']) {
                            if (!isset($combinedStats[$combinedKey]['counts'])) {
                                $combinedStats[$combinedKey]['counts'] = [];
                            }
                            foreach ($stats[$myKey]['counts'] as $countKey => $count) {
                                if (!isset($combinedStats[$combinedKey]['counts'][$countKey])) {
                                    $combinedStats[$combinedKey]['counts'][$countKey] = $count;
                                } else {
                                    // if the count exists and is over 0, combine the count.
                                    if ($count->get('time_count') && $count->get('time_count') > 0) {
                                        $time_count = $combinedStats[$combinedKey]['counts'][$countKey]
                                            ->get('time_count');
                                        $time_count = $time_count + $count->get('time_count');
                                        $combinedStats[$combinedKey]['counts'][$countKey]
                                            ->set('time_count', $time_count);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $stats = $combinedStats;
            unset($combinedStats);
        }

        $this->set([
            'stats' => $stats,
            'title' => $title,
            'timeperiod' => $timeperiod,
            'timeperiodPlural' => Inflector::pluralize($timeperiod),
            'range' => $range,
        ]);

        return null;
    }
}
