<?php

declare(strict_types=1);

/**
 * @var \Cake\View\View $this
 */

$this->loadHelper('Html');

$cakeDescription = 'My Test App';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?= $this->Html->charset(); ?>
    <title><?= $cakeDescription ?>: <?= $this->fetch('title'); ?></title>
    <?php
        echo $this->Html->meta('icon');

        echo $this->Html->css('cake.generic');

        echo $this->fetch('script');
    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div id="container">
        <div id="header">
            <h1><?= $this->Html->link($cakeDescription, 'https://cakephp.org'); ?></h1>
        </div>
        <div id="content">

            <?= $this->fetch('content'); ?>

        </div>
        <div id="footer">
            <?= $this->Html->link(
                    $this->Html->image('cake.power.gif', ['alt' => $cakeDescription, 'border' => '0']),
                    'http://www.cakephp.org/',
                    ['target' => '_blank', 'escape' => false]
                );
            ?>
        </div>
    </div>
</body>
</html>
