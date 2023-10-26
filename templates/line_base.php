<?php

declare(strict_types=1);

/**
 * @var \Cake\View\View $this
 */

if (!$this->get('title')) {
    $this->set('title', __('For the last {0} {1}', [
        $this->get('range'),
        $this->get('timeperiodPlural'),
    ]));
}
if (!$this->get('stats')) {
    $this->set('stats', []);
}
if (!$this->get('timeperiod')) {
    $this->set('timeperiod', 'day');
}
if (!$this->get('timeperiodPlural')) {
    $this->set('timeperiodPlural', 'days');
}
if (!$this->get('range')) {
    $this->set('range', 30);
}
echo $this->element('Fr3nch13/Stats.chartjs/block-line', [
    'title' => $this->get('title'),
    'stats' => $this->get('stats'),
    'timeperiod' => $this->get('timeperiod'),
    'timeperiodPlural' => $this->get('timeperiodPlural'),
    'range' => $this->get('range'),
]);
