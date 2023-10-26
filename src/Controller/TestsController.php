<?php
declare(strict_types=1);

/**
 * TestsController
 */

namespace Fr3nch13\Stats\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;

/**
 * Tests Controller
 *
 * Used to help with unit testing, specifically the ChartJsTrait
 */
class TestsController extends Controller
{
    /**
     * Used to do the common tasks for chartjs graphs.
     */
    use ChartJsTrait;

    /**
     * Dashboard Test Block
     *
     * @param int|null $range Go back x number of stats.
     * @param string|null $timeperiod The Interval for the line graph
     * @return \Cake\Http\Response|null
     */
    public function line(?int $range = null, ?string $timeperiod = null): ?Response
    {
        $keys = [
            'Stats.Tests.open',
            'Stats.Tests.closed',
            'Stats.Tests.pending',
            'Stats.Tests.nocounts',
        ];

        return $this->chartJsLine($keys, $range, $timeperiod);
    }
}
