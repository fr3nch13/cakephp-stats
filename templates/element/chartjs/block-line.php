<?php

declare(strict_types=1);

/**
 * @var \Cake\View\View $this
 */

$id = $id ?? 'block-line-' . rand(1, 1000);
$timeperiod = $timeperiod ?? 'day';

$nice_time_periods = [
    'year' => 'yyyy',
    'month' => 'MM/yyyy',
    'week' => 'W/Y',
    'day' => 'MM/dd/yyyy',
    'hour' => 'HH:00',
    'minute' => 'ii',
];

$lineData = [
    'labels' => [],
    'datasets' => [],
];
$x = 0;
foreach ($stats as $line) {
    $object = $line['object'];
    $counts = $line['counts'];

    $color = '#' . substr(md5($object->get('name')), 0, 6);
    if ($object->hasValue('color')) {
        $color = $object->color;
    }

    $lineData['datasets'][$x] = [
        'label' => $object->name,
        'backgroundColor' => $color,
        'borderColor' => $color,
        'fill' => false,
        'data' => [],
    ];

    $data = [];
    foreach ($counts as $count) {
        if (!$x) {
            $lineData['labels'][] = $count->get('timestamp')->i18nFormat($nice_time_periods[$timeperiod]);
        }
        $data[] = $count->get('time_count');
    }
    $lineData['datasets'][$x]['data'] = $data;

    $x++;
}
?>

<!-- START: Fr3nch13/Stats.element/chartjs/block-line -->

<div class="chartjs-line">
  <canvas id="<?= $id ?>"></canvas>
</div>
<script>
    const ctx = document.getElementById('<?= $id ?>');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?=json_encode($lineData['labels']) ?>,
            datasets: <?=json_encode($lineData['datasets'], JSON_PRETTY_PRINT) ?>,
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: false,
                text: '<?= $title ?>'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: '<?= ucfirst($timeperiod) ?>'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: '<?= __('Counts') ?>'
                    }
                }]
            }
        }
    });
</script>

<!-- END: Fr3nch13/Stats.element/chartjs/block-line -->

