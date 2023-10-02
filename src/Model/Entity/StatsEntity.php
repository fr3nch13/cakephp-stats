<?php
declare(strict_types=1);

/**
 * StatsEntity
 */

namespace Sis\Stats\Model\Entity;

/**
 * StatsEntity Entity
 *
 * @property \Sis\Orgs\Model\Entity\Ic|null $ic
 * @property \Sis\Stats\Model\Entity\StatsCount[] $stats_counts
 * @property bool $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property null|string $description
 * @property null|int $ic_id
 * @property int $id
 * @property null|string $key
 * @property \Cake\I18n\FrozenTime|null $last_updated
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property null|string $name
 * @property null|string $color
 */
class StatsEntity extends \Sis\Core\Model\Entity\Entity
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
    protected $_accessible = [
        'active' => true,
        'created' => true,
        'description' => true,
        'ic_id' => true,
        'key' => true,
        'last_updated' => true,
        'modified' => true,
        'name' => true,
        'color' => true,
        'stats_counts' => true,
    ];
}
