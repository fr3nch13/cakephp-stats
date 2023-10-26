<?php
declare(strict_types=1);

/**
 * TestsController
 */

namespace Fr3nch13\Stats\Controller;

use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\Controller\Controller;
use Fr3nch13\Stats\Controller\DbLineTrait;

/**
 * Tests Controller
 *
 * Used to help with unit testing, specifically the DbLineTrait
 */
class TestsController extends Controller
{
    /**
     * Used to do the common tasks for dbLine blocks.
     */
    use DbLineTrait;

    /**
     * Dashboard Test Block
     *
     * @param mixed|null $range Go back x number of stats.
     * @param string|null $timeperiod The Interval for the line graph
     * @return \Cake\Http\Response|null
     */
    public function dbLineTrait(?int $range = null, ?string $timeperiod = null): ?Response
    {
        $keys = [
            'Tickets.open',
            'Tickets.pastDue',
            'Tickets.needsReview',
        ];

        return $this->dbLineCommon($keys, $range, $timeperiod);
    }

    /**
     * Dashboard Test Block
     *
     * @param mixed|null $range Go back x number of stats.
     * @param string|null $timeperiod The Interval for the line graph
     * @return \Cake\Http\Response|null
     */
    public function dbLineTraitIds(mixed $range = null, ?string $timeperiod = null): ?Response
    {
        $keys = [
            'Tickets.__id__.open',
        ];

        return $this->dbLineCommon(
            $keys,
            $range,
            $timeperiod,
            __('Test Title IDS'),
            new DateTime(),
            [1, 2, 3]
        );
    }
}
