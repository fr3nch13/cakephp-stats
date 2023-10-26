<?php

declare(strict_types=1);

/**
 * @var \Cake\View\View $this
 */

$this->extend('Fr3nch13/Stats./db_line_base');

$this->set('title', __('Test - Testing the trait {0} {1}', [
    $this->get('range', ''),
    $this->get('timeperiodPlural', ''),
]));
