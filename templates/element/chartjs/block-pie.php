<?php

declare(strict_types=1);

/**
 * @var \Cake\View\View $this
 * @var string $title
 * @var \Cake\Collection\CollectionInterface<\Cake\Datasource\EntityInterface> $results
 */

$id = $id ?? 'block-pie-' . rand(1, 1000);
$clickable = $clickable ?? false;
?>

<!-- START: Sis/Core.element/dashboard/block-pie -->

<div class="box box-default db-block-pie" id="<?=$id ?>" data-url="<?= $this->Url->build(); ?>">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$title ?></h3>
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
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="chart-responsive">
                    <canvas id="<?=$id ?>-chart" height="50%" width="100%"></canvas>
                </div><!-- ./chart-responsive -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.box-body -->
</div>
<?php

$pieData = [
    'data' => [],
    'backgroundColor' => [],
    'labels' => [],
    'urls' => [],
];
/** @var \Cake\Datasource\EntityInterface $result */
foreach ($results as $result) {
    $color = '#' . substr(md5($result->get('name')), 0, 6);
    if (method_exists($result, 'has') && $result->has('color')) {
        $color = $result->get('color');
    }
    $pieData['data'][] = $result->get('count');
    $pieData['backgroundColor'][] = $color;
    $pieData['labels'][] = $result->get('name');
    if ($result->get('url')) {
        $pieData['urls'][] = $result->get('url');
    }
}
?>
<script type="text/javascript">
$( document ).ready(function() {
    var config = {
        type: 'doughnut',
        data: {
            datasets: [{
                data: <?=json_encode($pieData['data']) ?>,
                backgroundColor: <?=json_encode($pieData['backgroundColor']) ?>,
                label: 'Dataset 1',
                urls: <?=json_encode($pieData['urls']) ?>,
            }],
            labels: <?=json_encode($pieData['labels']) ?>,
        },
        options: {
            responsive: true,
            legend: {
                position: 'right',
            },
            title: {
                display: false,
                text: '<?=$title ?>'
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    };

    var canvas = $( "#<?=$id ?>-chart" ).get(0);
    var ctx = canvas.getContext('2d');
    var myDoughnut = new Chart(ctx, config);
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

    // watch the refresh button
    $('#<?= $id ?> button.block-refresh').on('click', function(event) {
        event.preventDefault();
        var updateEntity = $('#<?= $id ?>').parent();
        var url = $('#<?= $id ?>').data('url');
        updateDashBoardBlock(updateEntity, url, true);
    });
});
</script>

<!-- END: Sis/Core.element/dashboard/block-pie -->
