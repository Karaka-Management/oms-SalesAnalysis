<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\SalesAnalysis
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

/**
 * @var \phpOMS\Views\View $this
 */
echo $this->data['nav']->render();
echo $this->data['nav-sub']->render();
?>
<div class="row">
    <div class="col-xs-12 col-lg-6">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('Sales'); ?> (<?= $this->getHtml('monthly'); ?>) - <?= $this->getHtml('SalesReps'); ?>
            </div>
            <?php $sales = [1 => $this->data['monthlyRepPY'], 2 => $this->data['monthlyRepCurrent']]; ?>
            <div class="portlet-body">
                <canvas id="sales-profit-domestic-monthly" data-chart='{
                    "type": "bar",
                    "data": {
                        "labels": [
                            <?php
                                $temp = [];
                                for ($i = 1; $i < 13; ++$i) {
                                    $temp[] = \sprintf('"%02d"', $i);
                                }
                                echo \implode(',', $temp);
                            ?>
                        ],
                        "datasets": [
                            <?php
                                $first = true;
                                foreach ($sales[2] as $type => $values) :
                                echo($first ? '' : ',');
                                $first = false;
                            ?>{
                                "label": "<?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main?->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main?->name2); ?>",
                                "type": "line",
                                "data": [
                                    <?php
                                        $temp = [];
                                        for ($i = 1; $i < 13; ++$i) {
                                            if ($i > $this->data['endCurrentIndex']) {
                                                $temp[] = 'null';

                                                continue;
                                            }

                                            if (!isset($sales[2][$type][$i]['net_sales']) || !isset($sales[2][$type][$i]['net_profit'])) {
                                                $temp[] = '0';

                                                continue;
                                            }

                                            $temp[] = $sales[2][$type][$i]['net_sales'] ?? 0;
                                        }
                                        echo \implode(',', $temp);
                                    ?>
                                ],
                                "fill": false,
                                "tension": 0.0,
                                "hidden": true
                            }
                            <?php endforeach; ?>
                            ]
                    },
                    "options": {
                        "responsive": true
                    }
                }'></canvas>
                <div class="more-container">
                    <input class="more" id="more-domestic-monthly-sales" type="checkbox" name="more-container">
                    <label class="more" for="more-domestic-monthly-sales">
                        <span><?= $this->getHtml('Data'); ?></span>
                        <i class="g-icon expand">chevron_right</i>
                    </label>
                    <div class="slider more">
                    <table class="default sticky">
                        <thead>
                            <tr>
                                <td><?= $this->getHtml('SalesRep'); ?>
                                <?php for ($i = 1; $i < 13; ++$i) : ?>
                                    <td><?= $i; ?>
                                <?php endfor; ?>
                        <tbody>
                            <?php
                            $sum = [];
                            foreach ($sales[2] as $type => $values) : ?>
                                    <tr>
                                        <td><?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main?->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main?->name2); ?>
                                    <?php for ($i = 1; $i < 13; ++$i) :
                                        $sum[$i] = ($sum[$i] ?? 0) + ($values[$i]['net_sales'] ?? 0);
                                    ?>
                                    <td><?= $this->getCurrency($values[$i]['net_sales'] ?? 0, symbol: '', format: 'short', divide: 1000); ?>
                            <?php endfor; endforeach; ?>
                            <tr>
                                <td><?= $this->getHtml('Total'); ?>
                                <?php foreach ($sum as $value) : ?>
                                    <td><?= $this->getCurrency($value, symbol: '', format: 'short', divide: 1000); ?>
                                <?php endforeach; ?>
                    </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xs-12 col-lg-6">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('Profit'); ?> (<?= $this->getHtml('monthly'); ?>) - <?= $this->getHtml('SalesReps'); ?>
            </div>
            <?php $sales = [1 => $this->data['monthlyRepPY'], 2 => $this->data['monthlyRepCurrent']]; ?>
            <div class="portlet-body">
                <canvas id="sales-profit-domestic-monthly" data-chart='{
                    "type": "bar",
                    "data": {
                        "labels": [
                            <?php
                                $temp = [];
                                for ($i = 1; $i < 13; ++$i) {
                                    $temp[] = \sprintf('"%02d"', $i);
                                }
                                echo \implode(',', $temp);
                            ?>
                        ],
                        "datasets": [
                            <?php
                                $first = true;
                                foreach ($sales[2] as $type => $values) :
                                echo($first ? '' : ',');
                                $first = false;
                            ?>{
                                "label": "<?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main?->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main?->name2); ?>",
                                "type": "line",
                                "data": [
                                    <?php
                                        $temp = [];
                                        for ($i = 1; $i < 13; ++$i) {
                                            if ($i > $this->data['endCurrentIndex']) {
                                                $temp[] = 'null';

                                                continue;
                                            }

                                            if (!isset($sales[2][$type][$i]['net_sales']) || !isset($sales[2][$type][$i]['net_profit'])) {
                                                $temp[] = '0';

                                                continue;
                                            }

                                            $temp[] = $sales[2][$type][$i]['net_profit'] ?? 0;
                                        }
                                        echo \implode(',', $temp);
                                    ?>
                                ],
                                "fill": false,
                                "tension": 0.0,
                                "hidden": true
                            }
                            <?php endforeach; ?>
                            ]
                    },
                    "options": {
                        "responsive": true
                    }
                }'></canvas>
                <div class="more-container">
                    <input class="more" id="more-domestic-monthly-profit" type="checkbox" name="more-container">
                    <label class="more" for="more-domestic-monthly-profit">
                        <span><?= $this->getHtml('Data'); ?></span>
                        <i class="g-icon expand">chevron_right</i>
                    </label>
                    <div class="slider more">
                    <table class="default sticky">
                        <thead>
                            <tr>
                                <td><?= $this->getHtml('SalesRep'); ?>
                                <?php for ($i = 1; $i < 13; ++$i) : ?>
                                    <td><?= $i; ?>
                                <?php endfor; ?>
                        <tbody>
                            <?php
                            $sum = [];
                            foreach ($sales[2] as $type => $values) : ?>
                                    <tr>
                                        <td><?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main?->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main?->name2); ?>
                                    <?php for ($i = 1; $i < 13; ++$i) :
                                        $sum[$i] = ($sum[$i] ?? 0) + ($values[$i]['net_profit'] ?? 0);
                                    ?>
                                    <td><?= $this->getCurrency($values[$i]['net_profit'] ?? 0, symbol: '', format: 'short', divide: 1000); ?>
                            <?php endfor; endforeach; ?>
                            <tr>
                                <td><?= $this->getHtml('Total'); ?>
                                <?php foreach ($sum as $value) : ?>
                                    <td><?= $this->getCurrency($value, symbol: '', format: 'short', divide: 1000); ?>
                                <?php endforeach; ?>
                    </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>