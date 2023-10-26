<?php
declare(strict_types=1);

/**
 * TestsController
 */

namespace Fr3nch13\Stats\Controller;

use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\Controller\Controller;
use Fr3nch13\Stats\Controller\ChartJsTrait;

/**
 * Tests Controller
 *
 * Used to help with unit testing, specifically the ChartJsTrait
 */
class TestsController extends Controller
{
    /**
     * Used to do the common tasks for dbLine blocks.
     */
    use ChartJsTrait;

    /**
     * Dashboard Test Block
     *
     * @param mixed|null $range Go back x number of stats.
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