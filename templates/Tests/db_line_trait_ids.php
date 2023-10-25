<?php

declare(strict_types=1);

/**
 * @var \Fr3nch13\Stats\View\StatsView $this
 */

$this->extend('Fr3nch13/Stats./db_line_base');

$this->set('title', __('Test - Testing with IDS {0} {1}', [
    $this->get('range', ''),
    $this->get('timeperiodPlural', ''),
]));
