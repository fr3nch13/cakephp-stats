<?php

declare(strict_types=1);

/**
 * @var \Cake\View\View $this
 * @var null|string $color
 * @var null|string $hover
 * @var null|string $icon
 * @var null|string $more_info
 * @var null|string $name
 * @var null|int $stat
 * @var null|mixed $url
 * @var null|array<mixed> $url_options
 */

$color = $color ?? 'teal';
$hover = $hover ?? null;
$icon = $icon ?? null;
$more_info = $more_info ?? __('More Info');
$name = $name ?? __('Stat');
$stat = $stat ?? 0;
$url = $url ?? null;
$url_options = $url_options ?? [];
if (!isset($url_options['class'])) {
    $url_options['class'] = [];
}
$url_options['class']['small-box-footer'] = 'small-box-footer';
if (!isset($url_options['escape'])) {
    $url_options['escape'] = false;
}

if ($hover) {
    $url_options['title'] = $hover;
    $url_options['data-toggle'] = 'tooltip';
    $url_options['data-placement'] = 'top';
    $url_options['data-html'] = 'true';
}

?>

<!-- START: Sis/Core.element/dashboard/stat-box -->

<div class="small-box bg-<?= $color ?> db-block-stat">
    <div class="inner">
        <h3><?= $stat ?></h3>
        <p><?= $name ?></p>
    </div>
    <?php if ($icon) : ?>
    <div class="icon">
        <?= $this->AdminLte->icon($icon) ?>
    </div>
    <?php endif; ?>
    <?php if ($url) : ?>
        <?= $this->Html->link($more_info . ' ' . $this->AdminLte->icon('arrow-circle-right'), $url, $url_options) ?>
    <?php endif; ?>
</div>

<!-- END: Sis/Core.element/dashboard/stat-box -->
