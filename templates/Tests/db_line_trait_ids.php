<?php

declare(strict_types=1);

/**
 * @var \Sis\Core\View\BaseView $this
 */

$this->extend('Sis/Stats./db_line_base');

$this->set('title', __('Test - Testing with IDS {0} {1}', [
    $this->get('range', ''),
    $this->get('timeperiodPlural', ''),
]));
