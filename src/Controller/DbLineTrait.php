<?php
declare(strict_types=1);

/**
 * Used by controllers for the common methods.
 */
namespace Sis\Stats\Controller;

use Cake\I18n\FrozenTime;
use Cake\Utility\Inflector;

/**
 * DbLineTrait
 */
trait DbLineTrait
{
    /**
     * Performs the common DB Linegraph functions.
     *
     * @param \Sis\Core\Model\Table\Table $Table The Model Table.
     * @param array<string> $keys The keys to lookup.
     * @param mixed|null $range Go back x number of stats.
     * @param null|string $timeperiod The Interval for the line graph
     * @param null|string $title The title to use, if needed.
     * @param \Cake\I18n\FrozenTime|null $start The time that we want to start. Defaults to now.
     * @param array<int> $ids The specific ids that we want to combine.
     * @return \Cake\Http\Response|null
     */
    public function dbLineCommon(
        \Sis\Core\Model\Table\Table $Table,
        array $keys,
        $range = null,
        ?string $timeperiod = null,
        ?string $title = null,
        ?FrozenTime $start = null,
        array $ids = []
    ): ?\Cake\Http\Response {
        // redirect to the frontend url is correct.
        if (!$range || !$timeperiod) {
            return $this->redirect(['action' => $this->getRequest()->getParam('action'), 7, 'day']);
        }

        // make phpstan happy
        /** @var \Sis\Stats\Model\Table\TestsTable $Table */
        if (!$Table->behaviors()->has('Stats')) {
            $Table->addBehavior('Sis/Stats.Stats');
        }

        $timeperiods = $Table->statsGetTimeperiods();
        if (!in_array($timeperiod, $timeperiods)) {
            $timeperiod = 'day';
        }

        if (!$start) {
            $start = new FrozenTime();
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

        $stats = $Table->statsGetEntitiesCounts($keys, $start, (int)$range, $timeperiod);

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
                $myStats = [];
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
