<?php
declare(strict_types=1);

/**
 * TicketsController
 */

namespace Fr3nch13\TestApp\Controller;

use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\Controller\Controller;
use Fr3nch13\Stats\Controller\DbLineTrait;

/**
 * Tickets Controller
 *
 * Used to help with unit testing, specifically the DbLineTrait
 *
 * @property \Fr3nch13\TestApp\Model\Table\TicketsTable $Tickets
 * @method \Cake\ORM\Entity[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TicketsController extends Controller
{
    /**
     * Used to do the common tasks for dbLine blocks.
     */
    use DbLineTrait;

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize(): void
    {
        // Existing code
        parent::initialize();
        $authAllowedActions = ['dbLineTrait', 'dbLineTraitIds'];
    }

    /**
     * Dashboard Test Block
     *
     * @param mixed|null $range Go back x number of stats.
     * @param string|null $timeperiod The Interval for the line graph
     * @return \Cake\Http\Response|null
     */
    public function dbLineTrait(mixed $range = null, ?string $timeperiod = null): ?Response
    {
        $keys = [
            'Tickets.open',
            'Tickets.pastDue',
            'Tickets.needsReview',
        ];

        return $this->dbLineCommon($this->Tickets, $keys, $range, $timeperiod);
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
            $this->Tickets,
            $keys,
            $range,
            $timeperiod,
            __('Test Title IDS'),
            new DateTime('2023-10-21'),
            [108, 11, 52]
        );
    }
}
