<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\SalesAnalysis
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Localization\ISO3166NameEnum;
use phpOMS\Stdlib\Base\FloatInt;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */
echo $this->data['nav']->render();
?>
<div class="row">
    <div class="col-xs-12 col-lg-4">
        <section class="portlet">
        <form id="sales-dashboard-analysis" action="<?= UriFactory::build('{/backend}sales/analysis'); ?>" method="get">
            <div class="portlet-body">
                <div><?= $this->getHtml('Current'); ?></div>
                <div class="form-group">
                    <div class="input-control">
                        <label for="iStartCurrent"><?= $this->getHtml('Start'); ?></label>
                        <input id="iStartCurrent" name="startcurrent" type="date" value="<?= $this->data['startCurrent']->format('Y-m-d'); ?>">
                    </div>

                    <div class="input-control">
                        <label for="iEndCurrent"><?= $this->getHtml('End'); ?></label>
                        <input id="iEndCurrent" name="endcurrent" type="date" value="<?= $this->data['endCurrent']->format('Y-m-d'); ?>">
                    </div>
                </div>

                <div><?= $this->getHtml('Comparison'); ?></div>

                <div class="form-group">
                    <div class="input-control">
                        <label for="iStartComparison"><?= $this->getHtml('Start'); ?></label>
                        <input id="iStartComparison" name="startcomparison" type="date" value="<?= $this->data['startComparison']->format('Y-m-d'); ?>">
                    </div>

                    <div class="input-control">
                        <label for="iEndComparison"><?= $this->getHtml('End'); ?></label>
                        <input id="iEndComparison" name="endcomparison" type="date" value="<?= $this->data['endComparison']->format('Y-m-d'); ?>">
                    </div>
                </div>
            </div>
            <div class="portlet-foot">
                <input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Analyze'); ?>">
            </div>
        </form>
        </section>
    </div>

    <div class="col-xs-12 col-lg-4">
        <section class="portlet hl-3">
            <div class="portlet-head"><?= $this->getHtml('Actual'); ?></div>
            <div class="portlet-body">
                <div class="form-group">
                    <div><?= $this->getHtml('Sales'); ?> (<?= $this->getHtml('MTD'); ?>):</div>
                    <div>&nbsp;<?= \sprintf('%+.2f', $this->data['mtdPY']['net_sales'] == 0 ? 0 : $this->data['mtdA']['net_sales'] * 100 / $this->data['mtdPY']['net_sales'] - 100); ?> %</div>
                </div>

                <div class="form-group">
                    <div><?= $this->getHtml('Sales'); ?> (<?= $this->getHtml('YTD'); ?>):</div>
                    <div>&nbsp;<?= \sprintf('%+.2f', $this->data['ytdPY']['net_sales'] == 0 ? 0 : $this->data['ytdA']['net_sales'] * 100 / $this->data['ytdPY']['net_sales'] - 100); ?> %</div>
                </div>

                <div class="form-group">
                    <div><?= $this->getHtml('GrossProfit'); ?> (<?= $this->getHtml('MTD'); ?>):</div>
                    <div>&nbsp;<?= \sprintf('%+.2f', $this->data['mtdPY']['net_profit'] == 0 ? 0 : $this->data['mtdA']['net_profit'] * 100 / $this->data['mtdPY']['net_profit'] - 100); ?> %</div>
                </div>

                <div class="form-group">
                    <div><?= $this->getHtml('GrossProfit'); ?> (<?= $this->getHtml('YTD'); ?>):</div>
                    <div>&nbsp;<?= \sprintf('%+.2f', $this->data['ytdPY']['net_profit'] == 0 ? 0 : $this->data['ytdA']['net_profit'] * 100 / $this->data['ytdPY']['net_profit'] - 100); ?> %</div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-lg-6">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('SalesProfit'); ?> (<?= $this->getHtml('monthly'); ?>)
            </div>
            <?php $sales = $this->data['monthlySales']; ?>
            <div class="portlet-body">
                <canvas id="sales-profit-monthly" data-chart='{
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
                            {
                                "label": "<?= $this->getHtml('Profit'); ?> PY",
                                "type": "line",
                                "data": [
                                    <?php
                                        $temp = [];
                                        for ($i = 0; $i < 12; ++$i) {
                                            if (!isset($sales[1][$i]['net_sales']) || !isset($sales[1][$i]['net_profit'])) {
                                                $temp[] = 'null';

                                                continue;
                                            }

                                            $temp[] = $sales[1][$i]['net_sales'] == 0
                                                ? 0
                                                : $sales[1][$i]['net_profit'] * 100 / $sales[1][$i]['net_sales'];
                                        }
                                        echo \implode(',', $temp);
                                    ?>
                                ],
                                "yAxisID": "y1",
                                "fill": false,
                                "tension": 0.0,
                                "borderColor": "rgb(166, 193, 178)",
                                "backgroundColor": "rgb(166, 193, 178)"
                            },
                            {
                                "label": "<?= $this->getHtml('Profit'); ?> A",
                                "type": "line",
                                "data": [
                                    <?php
                                        $temp = [];
                                        for ($i = 0; $i < 12; ++$i) {
                                            if (!isset($sales[2][$i]['net_sales']) || !isset($sales[2][$i]['net_profit'])) {
                                                $temp[] = 'null';

                                                continue;
                                            }

                                            $temp[] = $sales[2][$i]['net_sales'] == 0
                                                ? 0
                                                : $sales[2][$i]['net_profit'] * 100 / $sales[2][$i]['net_sales'];
                                        }
                                        echo \implode(',', $temp);
                                    ?>
                                ],
                                "yAxisID": "y1",
                                "fill": false,
                                "tension": 0.0,
                                "borderColor": "rgb(46, 204, 113)",
                                "backgroundColor": "rgb(46, 204, 113)"
                            },
                            {
                                "label": "<?= $this->getHtml('Sales'); ?> PY",
                                "type": "bar",
                                "data": [
                                    <?php
                                        $temp = [];
                                        for ($i = 0; $i < 12; ++$i) {
                                            $temp[] = ($sales[1][$i]['net_sales'] ?? 0) / FloatInt::DIVISOR;
                                        }
                                        echo \implode(',', $temp);
                                    ?>
                                ],
                                "yAxisID": "y",
                                "fill": false,
                                "tension": 0.0,
                                "backgroundColor": "rgb(177, 195, 206)"
                            },
                            {
                                "label": "<?= $this->getHtml('Sales'); ?> A",
                                "type": "bar",
                                "data": [
                                    <?php
                                        $temp = [];
                                        for ($i = 0; $i < 12; ++$i) {
                                            $temp[] = ($sales[2][$i]['net_sales'] ?? 0) / FloatInt::DIVISOR;
                                        }
                                        echo \implode(',', $temp);
                                    ?>
                                ],
                                "yAxisID": "y",
                                "fill": false,
                                "tension": 0.0,
                                "backgroundColor": "rgb(54, 162, 235)"
                            }
                        ]
                    },
                    "options": {
                        "responsive": true,
                        "scales": {
                            "x": {
                                "title": {
                                    "display": true,
                                    "text": "<?= $this->getHtml('Months'); ?>"
                                }
                            },
                            "y": {
                                "title": {
                                    "display": true,
                                    "text": "<?= $this->getHtml('Sales'); ?>"
                                },
                                "display": true,
                                "position": "left"
                            },
                            "y1": {
                                "title": {
                                    "display": true,
                                    "text": "<?= $this->getHtml('Profit'); ?> %"
                                },
                                "display": true,
                                "position": "right",
                                "scaleLabel": {
                                    "display": true,
                                    "labelString": "<?= $this->getHtml('Profit'); ?>"
                                },
                                "grid": {
                                    "drawOnChartArea": false
                                }
                            }
                        }
                    }
                }'></canvas>
                <div class="more-container">
                    <input id="more-customer-sales" type="checkbox" name="more-container">
                    <label for="more-customer-sales">
                        <span><?= $this->getHtml('Data'); ?></span>
                        <i class="g-icon expand">chevron_right</i>
                    </label>
                    <div class="slider">
                    <table class="default sticky">
                        <thead>
                            <tr>
                                <td><?= $this->getHtml('Month'); ?>
                                <td><?= $this->getHtml('SalesPY'); ?>
                                <td><?= $this->getHtml('SalesA'); ?>
                                <td><?= $this->getHtml('ProfitPY'); ?>
                                <td><?= $this->getHtml('ProfitA'); ?>
                        <tbody>
                            <?php
                                $sum1 = 0;
                                $sum2 = 0;
                                $sum3 = 0;
                                $sum4 = 0;
                            for ($i = 0; $i < 12; ++$i) :
                                $sum1 += (int) ($sales[1][$i]['net_sales'] ?? 0);
                                $sum2 += (int) ($sales[2][$i]['net_sales'] ?? 0);
                                $sum3 += (int) ($sales[1][$i]['net_profit'] ?? 0);
                                $sum4 += (int) ($sales[2][$i]['net_profit'] ?? 0);
                            ?>
                                <tr>
                                    <td><?= \sprintf('%02d', $i + 1); ?>
                                    <td><?= $this->getCurrency((int) ($sales[1][$i]['net_sales'] ?? 0)); ?>
                                    <td><?= $this->getCurrency((int) ($sales[2][$i]['net_sales'] ?? 0)); ?>
                                    <td><?= \sprintf('%.2f', ($sales[1][$i]['net_sales'] ?? 0) == 0 ? 0 : $sales[1][$i]['net_profit'] * 100 / $sales[1][$i]['net_sales']); ?> %
                                    <td><?= \sprintf('%.2f', ($sales[2][$i]['net_sales'] ?? 0) == 0 ? 0 : $sales[2][$i]['net_profit'] * 100 / $sales[2][$i]['net_sales']); ?> %
                            <?php endfor; ?>
                                <tr>
                                    <td><?= $this->getHtml('Total'); ?>
                                    <td><?= $this->getCurrency($sum1); ?>
                                    <td><?= $this->getCurrency($sum2); ?>
                                    <td><?= \sprintf('%.2f', $sum3 == 0 ? 0 : $sum1 / $sum3); ?> %
                                    <td><?= \sprintf('%.2f', $sum4 == 0 ? 0 : $sum2 / $sum4); ?> %
                    </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xs-12 col-lg-6">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('SalesProfit'); ?> (<?= $this->getHtml('annually'); ?>)
            </div>
            <?php $sales = $this->data['annualSales']; ?>
            <div class="portlet-body">
                <canvas id="sales-profit-annually" data-chart='{
                    "type": "bar",
                    "data": {
                        "labels": [
                            <?php
                                $temp = [];
                                for ($i = 1; $i < 11; ++$i) {
                                    $temp[] = $sales[$i]['year'];
                                }
                                echo \implode(',', $temp);
                            ?>
                        ],
                        "datasets": [
                            {
                                "label": "<?= $this->getHtml('Profit'); ?>",
                                "type": "line",
                                "data": [
                                    <?php
                                        $temp = [];
                                        for ($i = 1; $i < 11; ++$i) {
                                            if ($sales[$i]['net_sales'] === null || $sales[$i]['net_profit'] === null) {
                                                $temp[] = 'null';

                                                continue;
                                            }

                                            $temp[] = $sales[$i]['net_sales'] == 0
                                                ? 0
                                                : $sales[$i]['net_profit'] * 100 / $sales[$i]['net_sales'];
                                        }
                                        echo \implode(',', $temp);
                                    ?>
                                ],
                                "yAxisID": "y1",
                                "fill": false,
                                "tension": 0.0,
                                "borderColor": "rgb(46, 204, 113)",
                                "backgroundColor": "rgb(46, 204, 113)"
                            },
                            {
                                "label": "<?= $this->getHtml('Sales'); ?>",
                                "type": "bar",
                                "data": [
                                    <?php
                                        $temp = [];
                                        for ($i = 1; $i < 11; ++$i) {
                                            $temp[] = $sales[$i]['net_sales'] / FloatInt::DIVISOR;
                                        }
                                        echo \implode(',', $temp);
                                    ?>
                                ],
                                "yAxisID": "y",
                                "fill": false,
                                "tension": 0.0,
                                "backgroundColor": "rgb(54, 162, 235)"
                            }
                        ]
                    },
                    "options": {
                        "responsive": true,
                        "scales": {
                            "x": {
                                "title": {
                                    "display": true,
                                    "text": "<?= $this->getHtml('Months'); ?>"
                                }
                            },
                            "y": {
                                "title": {
                                    "display": true,
                                    "text": "<?= $this->getHtml('Sales'); ?>"
                                },
                                "display": true,
                                "position": "left"
                            },
                            "y1": {
                                "title": {
                                    "display": true,
                                    "text": "<?= $this->getHtml('Profit'); ?> %"
                                },
                                "display": true,
                                "position": "right",
                                "scaleLabel": {
                                    "display": true,
                                    "labelString": "<?= $this->getHtml('Profit'); ?>"
                                },
                                "grid": {
                                    "drawOnChartArea": false
                                }
                            }
                        }
                    }
                }'></canvas>
                <div class="more-container">
                    <input id="more-customer-sales-annual" type="checkbox" name="more-container">
                    <label for="more-customer-sales-annual">
                        <span><?= $this->getHtml('Data'); ?></span>
                        <i class="g-icon expand">chevron_right</i>
                    </label>
                    <div class="slider">
                    <table class="default sticky">
                        <thead>
                            <tr>
                                <td><?= $this->getHtml('Year'); ?>
                                <td><?= $this->getHtml('Sales'); ?>
                                <td><?= $this->getHtml('Profit'); ?>
                        <tbody>
                            <?php
                            foreach ($sales as $values) :
                            ?>
                                <tr>
                                    <td>
                                    <td><?= $this->getCurrency(((int) $values['net_sales']) / FloatInt::DIVISOR); ?>
                                    <td><?= \sprintf('%.2f', $values['net_sales'] == 0 ? 0 : $values['net_profit'] * 100 / $values['net_sales']); ?> %
                            <?php endforeach; ?>
                    </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('ItemAttribute'); ?>
            </div>
            <div class="slider">
            <table class="default sticky">
                <thead>
                    <tr>
                        <td><?= $this->getHtml('Category'); ?>
                        <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('MTD'); ?>)
                        <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('MTD'); ?>)
                        <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('MTD'); ?>)
                <tbody>
                    <?php foreach ($this->data['ytdAItemAttribute'] as $type => $values) : ?>
                        <tr>
                            <td><?= $this->printHtml($this->data['ytdPYItemAttribute'][$type]['value_l11n']); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdPYItemAttribute'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdAItemAttribute'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency(
                                ((int) ($this->data['ytdAItemAttribute'][$type]['net_sales'] ?? 0)) -
                                ((int) ($this->data['ytdPYItemAttribute'][$type]['net_sales'] ?? 0))
                            ); ?>
                            <td><?= $this->getCurrency((int) ($this->data['mtdPYItemAttribute'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency((int) ($this->data['mtdAItemAttribute'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency(
                                ((int) ($this->data['mtdAItemAttribute'][$type]['net_sales'] ?? 0)) -
                                ((int) ($this->data['mtdPYItemAttribute'][$type]['net_sales'] ?? 0))
                            ); ?>
                    <?php endforeach; ?>
            </table>
            </div>
       </section>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('ClientAttribute'); ?>
            </div>
            <div class="slider">
            <table class="default sticky">
                <thead>
                    <tr>
                        <td><?= $this->getHtml('Category'); ?>
                        <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('MTD'); ?>)
                        <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('MTD'); ?>)
                        <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('MTD'); ?>)
                <tbody>
                    <?php foreach ($this->data['ytdAClientAttribute'] as $type => $values) : ?>
                        <tr>
                            <td><?= $this->printHtml($this->data['ytdPYClientAttribute'][$type]['value_l11n']); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdPYClientAttribute'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdAClientAttribute'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency(
                                ((int) ($this->data['ytdAClientAttribute'][$type]['net_sales'] ?? 0)) -
                                ((int) ($this->data['ytdPYClientAttribute'][$type]['net_sales'] ?? 0))
                            ); ?>
                            <td><?= $this->getCurrency((int) ($this->data['mtdPYClientAttribute'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency((int) ($this->data['mtdAClientAttribute'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency(
                                ((int) ($this->data['mtdAClientAttribute'][$type]['net_sales'] ?? 0)) -
                                ((int) ($this->data['mtdPYClientAttribute'][$type]['net_sales'] ?? 0))
                            ); ?>
                    <?php endforeach; ?>
            </table>
            </div>
       </section>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('Country'); ?>
            </div>
            <div class="slider">
            <table class="default sticky">
                <thead>
                    <tr>
                        <td><?= $this->getHtml('Country'); ?>
                        <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('MTD'); ?>)
                        <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('MTD'); ?>)
                        <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('MTD'); ?>)
                <tbody>
                    <?php foreach ($this->data['ytdAClientCountry'] as $type => $values) : ?>
                        <tr>
                            <td><?= $this->printHtml(ISO3166NameEnum::getBy2Code($type)); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdPYClientCountry'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdAClientCountry'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency(
                                ((int) ($this->data['ytdAClientCountry'][$type]['net_sales'] ?? 0)) -
                                ((int) ($this->data['ytdPYClientCountry'][$type]['net_sales'] ?? 0))
                            ); ?>
                            <td><?= $this->getCurrency((int) ($this->data['mtdPYClientCountry'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency((int) ($this->data['mtdAClientCountry'][$type]['net_sales'] ?? 0)); ?>
                            <td><?= $this->getCurrency(
                                ((int) ($this->data['mtdAClientCountry'][$type]['net_sales'] ?? 0)) -
                                ((int) ($this->data['mtdPYClientCountry'][$type]['net_sales'] ?? 0))
                            ); ?>
                    <?php endforeach; ?>
            </table>
            </div>
       </section>
    </div>
</div>