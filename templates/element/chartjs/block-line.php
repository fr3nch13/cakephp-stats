<?php

declare(strict_types=1);

/**
 * @var \Cake\View\View $this
 */
?>


$title = $title ?? '';
$stats = $stats ?? [];
$timeperiod = $timeperiod ?? 'day';
$timeperiodPlural = $timeperiodPlural ?? 'days';
$range = $range ?? 30;
$id = $id ?? 'block-line-' . rand(1, 1000);
$clickable = $clickable ?? false;
?>

<!-- START: Sis/Core.element/dashboard/block-line -->

<div class="box box-default db-block-line" id="<?= $id ?>" data-url="<?= $this->Url->build(); ?>">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title ?></h3>
        <div class="box-tools pull-right">
            <button
                type="button"
                class="btn btn-box-tool block-refresh"
                data-widget="collapse"
                ><i class="fa fa-sync"></i></button>
            <button
                type="button"
                class="btn btn-box-tool"
                data-widget="collapse"
                ><i class="fa fa-minus"></i></button>
            <button
                type="button"
                class="btn btn-box-tool"
                data-widget="remove"
                ><i class="fa fa-times"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="chart-responsive">
                    <canvas id="<?= $id ?>-chart"></canvas>
                </div>
                <!-- ./chart-responsive -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <form action="#" method="post">
            <div class="input-group">
                <select id="<?= $id ?>-options">
                    <option value="<?= $this->Url->build([3, 'month']); ?>"><?= __('Past 3 Months') ?></option>
                    <option value="<?= $this->Url->build([30, 'day']); ?>"><?= __('Past 30 Days') ?></option>
                    <option value="<?= $this->Url->build([7, 'day']); ?>"><?= __('Past Week') ?></option>
                    <option value="<?= $this->Url->build([24, 'hour']); ?>"><?= __('Past Day') ?></option>
                </select>
            </div>
        </form>
    </div>
</div>
<?php

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
foreach ($stats as $key => $line) {
    /** @var \Cake\Datasource\EntityInterface $entity */
    $entity = $line['entity'];
    $counts = $line['counts'];

    $color = '#' . substr(md5($entity->get('name')), 0, 6);
    if (method_exists($entity, 'has') && $entity->has('color')) {
        $color = $entity->get('color');
    }

    $lineData['datasets'][$x] = [
        'label' => $entity->get('name'),
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
<script type="text/javascript">
$( document ).ready(function() {
    var config = {
        type: 'line',
        data: {
            labels: <?=json_encode($lineData['labels'], JSON_PRETTY_PRINT) ?>,
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
    };

    var canvas = $( "#<?= $id ?>-chart" ).get(0);
    var ctx = canvas.getContext('2d');
    var myLineChart = new Chart(ctx, config);
    /*
    canvas.onclick = function(evt) {
        var activePoints = myDoughnut.getElementsAtEvent(evt);
        if (activePoints[0]) {
            var chartData = activePoints[0]['_chart'].config.data;
            var idx = activePoints[0]['_index'];

            var label = chartData.labels[idx];
            var url = chartData.datasets[0].urls[idx];

            if (url) {
                window.location.href = url;
            }
        }
    };
    */

    // watch the refresh button
    $('#<?= $id ?> button.block-refresh').on('click', function(event) {
        event.preventDefault();
        var updateEntity = $('#<?= $id ?>').parent();
        var url = $('#<?= $id ?>').data('url');
        updateDashBoardBlock(updateEntity, url, true);
    });

    // select the dropdown option that is currently being shown.
    var url = $('#<?= $id ?>').data('url');
    console.log(url);
    $('#<?= $id ?>-options option').each(function() {
        if ($(this).val() == url) {
            $(this).attr('selected', 'selected');
        }
    });

    // watch the dropdown.
    $('#<?= $id ?>-options').on('change', function() {
        var updateEntity = $('#<?= $id ?>').parent();
        updateDashBoardBlock(updateEntity, $(this).val(), true);

    });
});
</script>

<!-- END: Sis/Core.element/dashboard/block-pie -->

