<?php
declare(strict_types=1);

/**
 * StatsCount
 */

namespace Sis\Stats\Model\Entity;

use Cake\I18n\FrozenTime;

/**
 * StatsCount Entity
 *
 * @property \Sis\Stats\Model\Entity\StatsEntity $stats_entity
 * @property int $id
 * @property int $stats_entity_id
 * @property null|string $time_period
 * @property int $time_stamp
 * @property int $time_count
 * @property null|\Cake\I18n\FrozenTime $timestamp
 * @property \Sis\Stats\Model\Entity\StatsEntity $stats_entity
 */
class StatsCount extends \Sis\Core\Model\Entity\Entity
{
    /**
     * Expose the timestamp virtual field.
     */
    protected $_virtual = ['timestamp'];

    /**
     * Virtual field that turns the makes a FrozenTime field from the time_stamp field.
     *
     * @return null|\Cake\I18n\FrozenTime The created timestamp or null if unknown timeperiod.
     */
    protected function _getTimestamp(): ?FrozenTime
    {
        $return = null;
        $time_stamp = $this->get('time_stamp');
        $time_stamp = strval($time_stamp);

        // don't cache the return in mormory, or anyone else.
        // because $time_stamp is mutable, and should be figured out each time.

        switch ($this->get('time_period')) {
            case 'hour':
                $return = FrozenTime::create(
                    (int)substr($time_stamp, 0, 4),
                    (int)substr($time_stamp, 4, 2),
                    (int)substr($time_stamp, 6, 2),
                    (int)substr($time_stamp, 8, 2)
                );
                break;
            case 'day':
                $return = FrozenTime::create(
                    (int)substr($time_stamp, 0, 4),
                    (int)substr($time_stamp, 4, 2),
                    (int)substr($time_stamp, 6, 2)
                );
                break;
            case 'week':
                // FrozenTime doesn't have a week() method.
                /** @var int $timeweek */
                $timeweek = strtotime(substr($time_stamp, 0, 4) . 'W' . substr($time_stamp, 4, 2));
                $date = date('Ymd', $timeweek);

                $return = FrozenTime::create(
                    (int)substr($date, 0, 4),
                    (int)substr($date, 4, 2),
                    (int)substr($date, 6, 2)
                );
                break;
            case 'month':
                $return = FrozenTime::create(
                    (int)substr($time_stamp, 0, 4),
                    (int)substr($time_stamp, 4, 2),
                    1
                );
                break;
            case 'year':
                $return = FrozenTime::create(
                    (int)substr($time_stamp, 0, 4),
                    1,
                    1
                );
                break;
        }

        return $return;
    }

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'id' => true,
        'stats_entity_id' => true,
        'time_period' => true,
        'time_stamp' => true,
        'time_count' => true,
        'stats_entity' => true,
        'timestamp' => true,
    ];
}
