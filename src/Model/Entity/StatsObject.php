<?php
declare(strict_types=1);

/**
 * StatsObject
 */

namespace Fr3nch13\Stats\Model\Entity;

use Cake\ORM\Entity;

/**
 * StatsObject Entity
 *
 * @property int $id
 * @property string $okey
 * @property null|string $name
 * @property null|string $description
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $last_updated
 * @property bool $active
 * @property null|string $color
 *
 * @property \Fr3nch13\Stats\Model\Entity\StatsCount[] $stats_counts
 */
class StatsObject extends Entity
{
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
        'okey' => true,
        'name' => true,
        'description' => true,
        'created' => true,
        'modified' => true,
        'last_updated' => true,
        'active' => true,
        'color' => true,
        'stats_counts' => true,
    ];
}
