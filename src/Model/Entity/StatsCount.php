<?php
declare(strict_types=1);

/**
 * StatsCount
 */

namespace Fr3nch13\Stats\Model\Entity;

use Cake\I18n\DateTime;
use Cake\ORM\Entity;

/**
 * StatsCount Entity
 *
 * @property int $id
 * @property int $stats_object_id
 * @property null|string $time_period
 * @property int $time_stamp
 * @property int $time_count
 *
 * @property null|\Cake\I18n\DateTime $timestamp Virtual field that will return a formated timestamp
 *
 * @property \Fr3nch13\Stats\Model\Entity\StatsObject $stats_object
 */
class StatsCount extends Entity
{
    /**
     * Expose the timestamp virtual field.
     */
    protected array $_virtual = [
        'timestamp',
    ];

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'id' => true,
        'stats_object_id' => true,
        'time_period' => true,
        'time_stamp' => true,
        'time_count' => true,
        'stats_object' => true,
        'timestamp' => true,
    ];

    /**
     * Virtual field that turns the makes a DateTime field from the timestamp field.
     *
     * @return \Cake\I18n\DateTime|null The created timestamp or null if unknown timeperiod.
     */
    protected function _getTimestamp(): ?DateTime
    {
        $return = null;
        $time_stamp = $this->time_stamp;
        $time_stamp = strval($time_stamp);

        // don't cache the return in mormory, or anyone else.
        // because $time_stamp is mutable, and should be figured out each time.

        switch ($this->time_period) {
            case 'hour':
                $return = DateTime::create(
                    (int)substr($time_stamp, 0, 4),
                    (int)substr($time_stamp, 4, 2),
                    (int)substr($time_stamp, 6, 2),
                    (int)substr($time_stamp, 8, 2)
                );
                break;
            case 'day':
                $return = DateTime::create(
                    (int)substr($time_stamp, 0, 4),
                    (int)substr($time_stamp, 4, 2),
                    (int)substr($time_stamp, 6, 2),
                    0
                );
                break;
            case 'week':
                // DateTime doesn't have a week() method.
                /** @var int $timeweek */
                $timeweek = strtotime(substr($time_stamp, 0, 4) . 'W' . substr($time_stamp, 4, 2));
                $date = date('Ymd', $timeweek);

                $return = DateTime::create(
                    (int)substr($date, 0, 4),
                    (int)substr($date, 4, 2),
                    (int)substr($date, 6, 2),
                    0
                );
                break;
            case 'month':
                $return = DateTime::create(
                    (int)substr($time_stamp, 0, 4),
                    (int)substr($time_stamp, 4, 2),
                    1,
                    0
                );
                break;
            case 'year':
                $return = DateTime::create(
                    (int)substr($time_stamp, 0, 4),
                    1,
                    1,
                    0
                );
                break;
        }

        return $return;
    }
}
