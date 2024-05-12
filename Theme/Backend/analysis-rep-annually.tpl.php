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

use phpOMS\Localization\ISO3166CharEnum;
use phpOMS\Localization\ISO3166NameEnum;
use phpOMS\Stdlib\Base\FloatInt;

/**
 * @var \phpOMS\Views\View $this
 */
echo $this->data['nav']->render();
echo $this->data['nav-sub']->render();
?>

<div class="row">
    <div class="col-xs-12 col-lg-6">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Sales'); ?> (<?= $this->getHtml('annually'); ?>) - <?= $this->getHtml('SalesReps'); ?></div>
            <div class="portlet-body">
                <canvas id="sales-annually-rep" data-chart='{
                    "type": "line",
                    "data": {
                        "labels": [
                            <?php
                                $temp = [];
                                for ($i = 0; $i < 10; ++$i) {
                                    $temp[] = ((int) $this->data['historyStart']->format('Y')) + $i;
                                }
                                echo \implode(',', $temp);
                            ?>
                        ],
                        "datasets": [
                            <?php
                                $first = true;
                                foreach ($this->data['annualRep'] as $type => $values) :
                                echo($first ? '' : ',');
                                $first = false;
                            ?>{
                                "label": "<?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main?->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main?->name2); ?>",
                                "type": "line",
                                "data": [
                                    <?php
                                        $temp = [];
                                        foreach ($values as $annual) {
                                            $temp[] = ((int) ($annual['net_sales'] ?? 0)) / FloatInt::DIVISOR;
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
                    <input class="more" id="more-sales-rep-annual" type="checkbox" name="more-container">
                    <label class="more" for="more-sales-rep-annual">
                        <span><?= $this->getHtml('Data'); ?></span>
                        <i class="g-icon expand">chevron_right</i>
                    </label>
                    <div class="slider more">
                    <table class="default sticky">
                        <thead>
                            <tr>
                                <td><?= $this->getHtml('Region'); ?>
                                <?php for ($i = 0; $i < 10; ++$i) : ?>
                                    <td><?= ((int) $this->data['historyStart']->format('Y')) + $i; ?>
                                <?php endfor; ?>
                        <tbody>
                            <?php
                            $sum = [];
                            foreach ($this->data['annualRep'] as $type => $values) : ?>
                                    <tr>
                                        <td><?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main?->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main?->name2); ?>
                                    <?php foreach ($values as $idx => $annual) :
                                        $sum[$idx] = ($sum[$idx] ?? 0) + ($annual['net_sales'] ?? 0);
                                    ?>
                                        <td><?= $this->getCurrency($annual['net_sales'] ?? 0, symbol: '', format: 'short', divide: 1000); ?>
                            <?php endforeach; endforeach; ?>
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
            <div class="portlet-head"><?= $this->getHtml('Profit'); ?> (<?= $this->getHtml('annually'); ?>) - <?= $this->getHtml('SalesReps'); ?></div>
            <div class="portlet-body">
                <canvas id="profit-annually-rep" data-chart='{
                    "type": "line",
                    "data": {
                        "labels": [
                            <?php
                                $temp = [];
                                for ($i = 0; $i < 10; ++$i) {
                                    $temp[] = ((int) $this->data['historyStart']->format('Y')) + $i;
                                }
                                echo \implode(',', $temp);
                            ?>
                        ],
                        "datasets": [
                            <?php
                                $first = true;
                                foreach ($this->data['annualRep'] as $type => $values) :
                                echo($first ? '' : ',');
                                $first = false;
                            ?>{
                                "label": "<?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main?->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main?->name2); ?>",
                                "type": "line",
                                "data": [
                                    <?php
                                        $temp = [];
                                        foreach ($values as $annual) {
                                            $temp[] = ($annual['net_sales'] ?? 0) == 0 ? 0 : ((int) ($annual['net_profit'] ?? 0)) * 100 / $annual['net_sales'];
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
                        "responsive": true,
                        "scales": {
                            "y": {
                                "title": {
                                    "display": true,
                                    "text": "<?= $this->getHtml('Profit'); ?> %"
                                },
                                "display": true,
                                "position": "left"
                            }
                        }
                    }
                }'></canvas>

                <div class="more-container">
                    <input class="more" id="more-profit-rep-annual" type="checkbox" name="more-container">
                    <label class="more" for="more-profit-rep-annual">
                        <span><?= $this->getHtml('Data'); ?></span>
                        <i class="g-icon expand">chevron_right</i>
                    </label>
                    <div class="slider more">
                    <table class="default sticky">
                        <thead>
                            <tr>
                                <td><?= $this->getHtml('Region'); ?>
                                <?php for ($i = 0; $i < 10; ++$i) : ?>
                                    <td><?= ((int) $this->data['historyStart']->format('Y')) + $i; ?>
                                <?php endfor; ?>
                        <tbody>
                            <?php
                            $sum = [];
                            foreach ($this->data['annualRep'] as $type => $values) : ?>
                                    <tr>
                                        <td><?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main?->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main?->name2); ?>
                                    <?php foreach ($values as $idx => $annual) :
                                        $sum[$idx] = ($sum[$idx] ?? 0) + ($annual['net_profit'] ?? 0);
                                    ?>
                                        <td><?= $this->getCurrency($annual['net_profit'] ?? 0, symbol: '', format: 'short', divide: 1000); ?>
                            <?php endforeach; endforeach; ?>
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
