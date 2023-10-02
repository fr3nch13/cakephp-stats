<?php
declare(strict_types=1);

/**
 * Test
 */

namespace Sis\Stats\Model\Entity;

/**
 * Test Entity
 *
 * @property int $id
 * @property null|string $name
 */
class Test extends \Sis\Core\Model\Entity\Entity
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
        'id' => true,
        'name' => true,
    ];
}
