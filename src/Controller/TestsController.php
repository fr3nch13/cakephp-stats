<?php
declare(strict_types=1);

/**
 * TestsController
 */

namespace Sis\Stats\Controller;

use Cake\I18n\FrozenTime;

/**
 * Tests Controller
 *
 * Used to help with unit testing, specifically the DbLineTrait
 *
 * @property \Sis\Users\Controller\Component\AuthComponent $Auth
 * @property \Sis\Stats\Model\Table\TestsTable $Tests
 * @method \Cake\ORM\Entity[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TestsController extends AppController
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
        $this->Auth->allow($authAllowedActions);
    }

    /**
     * Dashboard Test Block
     *
     * @param mixed|null $range Go back x number of stats.
     * @param null|string $timeperiod The Interval for the line graph
     * @return \Cake\Http\Response|null
     */
    public function dbLineTrait($range = null, ?string $timeperiod = null): ?\Cake\Http\Response
    {
        $keys = [
            'PenTest.Results.open',
            'PenTest.Results.pastDue',
            'PenTest.Results.needsReview',
            'PenTest.Results.__key__.open,',
        ];

        return $this->dbLineCommon($this->Tests, $keys, $range, $timeperiod);
    }

    /**
     * Dashboard Test Block
     *
     * @param mixed|null $range Go back x number of stats.
     * @param null|string $timeperiod The Interval for the line graph
     * @return \Cake\Http\Response|null
     */
    public function dbLineTraitIds($range = null, ?string $timeperiod = null): ?\Cake\Http\Response
    {
        $keys = [
            'PenTest.Results.__id__.open',
        ];

        return $this->dbLineCommon(
            $this->Tests,
            $keys,
            $range,
            $timeperiod,
            __('Test Title IDS'),
            new FrozenTime('2019-06-21'),
            [108, 11, 52]
        );
    }
}
