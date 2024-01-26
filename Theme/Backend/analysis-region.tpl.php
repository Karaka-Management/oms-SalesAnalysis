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

use phpOMS\Localization\ISO3166CharEnum;
use phpOMS\Localization\ISO3166NameEnum;

/**
 * @var \phpOMS\Views\View $this
 */
echo $this->data['nav']->render();
?>

<div class="tabview tab-2">
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('World'); ?></label>
            <li><label for="c-tab-2"><?= $this->getHtml('DomesticExport'); ?></label>
            <li><label for="c-tab-3"><?= $this->getHtml('Continents'); ?></label>
            <li><label for="c-tab-4"><?= $this->getHtml('Regions'); ?></label>
            <!--<li><label for="c-tab-5"><?= $this->getHtml('Filter'); ?></label>-->
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('World'); ?></div>
                        <div class="portlet-body">
                            <canvas id="sales-world" data-chart='{
                                "type": "choropleth",
                                "mapurl": "Resources/chartjs/plugins/maps/world.topo.json",
                                "data": {
                                    "labels": [],
                                    "datasets": [{
                                        "label": "Countries",
                                        "data": [
                                            <?php
                                            $temp = [];
                                            foreach ($this->data['ytdAClientCountry'] as $lang => $values) {
                                                $temp[] = '{"id": "' . ISO3166CharEnum::getBy2Code($lang) . '", "value": ' . ($values['net_sales'] / 10000) . '}';
                                            } ?>
                                            <?= \implode(',', $temp); ?>
                                        ]
                                    }]
                                },
                                "options": {
                                    "responsive": true,
                                    "showOutline": true,
                                    "showGraticule": false,
                                    "plugins": {
                                        "legend": {
                                            "display": false
                                        }
                                    },
                                    "scales": {
                                        "projection": {
                                            "axis": "x",
                                            "projection": "equirectangular"
                                        }
                                    }
                                }
                            }'></canvas>
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
        </div>

        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">

            <div class="row">
                <div class="col-xs-12 col-lg-6">
                    <section class="portlet">
                        <div class="portlet-head">
                            <?= $this->getHtml('SalesProfit'); ?> (<?= $this->getHtml('monthly'); ?>) - <?= $this->getHtml('Domestic'); ?>
                        </div>
                        <?php $sales = [1 => $this->data['monthlyDomesticExportPY'], 2 => $this->data['monthlyDomesticExportCurrent']]; ?>
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
                                        {
                                            "label": "<?= $this->getHtml('Profit'); ?> PY",
                                            "type": "line",
                                            "data": [
                                                <?php
                                                    $temp = [];
                                                    for ($i = 1; $i < 13; ++$i) {
                                                        if (!isset($sales[1][$this->data['domestic']][$i]['net_sales']) || !isset($sales[1][$this->data['domestic']][$i]['net_profit'])) {
                                                            $temp[] = 'null';

                                                            continue;
                                                        }

                                                        $temp[] = $sales[1][$this->data['domestic']][$i]['net_sales'] == 0
                                                            ? 0
                                                            : $sales[1][$this->data['domestic']][$i]['net_profit'] * 100 / $sales[1][$this->data['domestic']][$i]['net_sales'];
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
                                                    for ($i = 1; $i < 13; ++$i) {
                                                        if (!isset($sales[2][$this->data['domestic']][$i]['net_sales']) || !isset($sales[2][$this->data['domestic']][$i]['net_profit'])) {
                                                            $temp[] = 'null';

                                                            continue;
                                                        }

                                                        $temp[] = $sales[2][$this->data['domestic']][$i]['net_sales'] == 0
                                                            ? 0
                                                            : $sales[2][$this->data['domestic']][$i]['net_profit'] * 100 / $sales[2][$this->data['domestic']][$i]['net_sales'];
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
                                                    for ($i = 1; $i < 13; ++$i) {
                                                        $temp[] = ($sales[1][$this->data['domestic']][$i]['net_sales'] ?? 0) / 10000;
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
                                                    for ($i = 1; $i < 13; ++$i) {
                                                        $temp[] = ($sales[2][$this->data['domestic']][$i]['net_sales'] ?? 0) / 10000;
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
                                <input id="more-domestic-monthly-sales" type="checkbox" name="more-container">
                                <label for="more-domestic-monthly-sales">
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
                                        for ($i = 1; $i < 13; ++$i) :
                                            $sum1 += (int) ($sales[1][$this->data['domestic']][$i]['net_sales'] ?? 0);
                                            $sum2 += (int) ($sales[2][$this->data['domestic']][$i]['net_sales'] ?? 0);
                                            $sum3 += (int) ($sales[1][$this->data['domestic']][$i]['net_profit'] ?? 0);
                                            $sum4 += (int) ($sales[2][$this->data['domestic']][$i]['net_profit'] ?? 0);
                                        ?>
                                            <tr>
                                                <td><?= \sprintf('%02d', $i); ?>
                                                <td><?= $this->getCurrency((int) ($sales[1][$this->data['domestic']][$i]['net_sales'] ?? 0)); ?>
                                                <td><?= $this->getCurrency((int) ($sales[2][$this->data['domestic']][$i]['net_sales'] ?? 0)); ?>
                                                <td><?= \sprintf('%.2f', ($sales[1][$this->data['domestic']][$i]['net_sales'] ?? 0) == 0 ? 0 : $sales[1][$this->data['domestic']][$i]['net_profit'] * 100 / $sales[1][$this->data['domestic']][$i]['net_sales']); ?> %
                                                <td><?= \sprintf('%.2f', ($sales[2][$this->data['domestic']][$i]['net_sales'] ?? 0) == 0 ? 0 : $sales[2][$this->data['domestic']][$i]['net_profit'] * 100 / $sales[2][$this->data['domestic']][$i]['net_sales']); ?> %
                                        <?php endfor; ?>
                                            <tr>
                                                <td><?= $this->getHtml('Total'); ?>
                                                <td><?= $this->getCurrency($sum1); ?>
                                                <td><?= $this->getCurrency($sum2); ?>
                                                <td><?= \sprintf('%.2f', $sum3 == 0 ? 0 : $sum1 / $sum3); ?> %
                                                <td><?= \sprintf('%.2f', $sum3 == 0 ? 0 : $sum2 / $sum4); ?> %
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xs-12 col-lg-6">
                    <section class="portlet">
                        <div class="portlet-head">
                            <?= $this->getHtml('SalesProfit'); ?> (<?= $this->getHtml('monthly'); ?>) - <?= $this->getHtml('Export'); ?>
                        </div>
                        <?php $sales = [1 => $this->data['monthlyDomesticExportPY'], 2 => $this->data['monthlyDomesticExportCurrent']]; ?>
                        <div class="portlet-body">
                            <canvas id="sales-profit-export-monthly" data-chart='{
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
                                                    for ($i = 1; $i < 13; ++$i) {
                                                        if (!isset($sales[1]['Other'][$i]['net_sales']) || !isset($sales[1]['Other'][$i]['net_profit'])) {
                                                            $temp[] = 'null';

                                                            continue;
                                                        }

                                                        $temp[] = $sales[1]['Other'][$i]['net_sales'] == 0
                                                            ? 0
                                                            : $sales[1]['Other'][$i]['net_profit'] * 100 / $sales[1]['Other'][$i]['net_sales'];
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
                                                    for ($i = 1; $i < 13; ++$i) {
                                                        if (!isset($sales[2]['Other'][$i]['net_sales']) || !isset($sales[2]['Other'][$i]['net_profit'])) {
                                                            $temp[] = 'null';

                                                            continue;
                                                        }

                                                        $temp[] = $sales[2]['Other'][$i]['net_sales'] == 0
                                                            ? 0
                                                            : $sales[2]['Other'][$i]['net_profit'] * 100 / $sales[2]['Other'][$i]['net_sales'];
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
                                                    for ($i = 1; $i < 13; ++$i) {
                                                        $temp[] = ($sales[1]['Other'][$i]['net_sales'] ?? 0) / 10000;
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
                                                    for ($i = 1; $i < 13; ++$i) {
                                                        $temp[] = ($sales[2]['Other'][$i]['net_sales'] ?? 0) / 10000;
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
                                <input id="more-export-monthly-sales" type="checkbox" name="more-container">
                                <label for="more-export-monthly-sales">
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
                                        for ($i = 1; $i < 13; ++$i) :
                                            $sum1 += (int) ($sales[1]['Other'][$i]['net_sales'] ?? 0);
                                            $sum2 += (int) ($sales[2]['Other'][$i]['net_sales'] ?? 0);
                                            $sum3 += (int) ($sales[1]['Other'][$i]['net_profit'] ?? 0);
                                            $sum4 += (int) ($sales[2]['Other'][$i]['net_profit'] ?? 0);
                                        ?>
                                            <tr>
                                                <td><?= \sprintf('%02d', $i); ?>
                                                <td><?= $this->getCurrency((int) ($sales[1]['Other'][$i]['net_sales'] ?? 0)); ?>
                                                <td><?= $this->getCurrency((int) ($sales[2]['Other'][$i]['net_sales'] ?? 0)); ?>
                                                <td><?= \sprintf('%.2f', ($sales[1]['Other'][$i]['net_sales'] ?? 0) == 0 ? 0 : $sales[1]['Other'][$i]['net_profit'] * 100 / $sales[1]['Other'][$i]['net_sales']); ?> %
                                                <td><?= \sprintf('%.2f', ($sales[2]['Other'][$i]['net_sales'] ?? 0) == 0 ? 0 : $sales[2]['Other'][$i]['net_profit'] * 100 / $sales[2]['Other'][$i]['net_sales']); ?> %
                                        <?php endfor; ?>
                                            <tr>
                                                <td><?= $this->getHtml('Total'); ?>
                                                <td><?= $this->getCurrency($sum1); ?>
                                                <td><?= $this->getCurrency($sum2); ?>
                                                <td><?= \sprintf('%.2f', $sum3 == 0 ? 0 : $sum1 / $sum3); ?> %
                                                <td><?= \sprintf('%.2f', $sum3 == 0 ? 0 : $sum2 / $sum4); ?> %
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-lg-4">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Sales'); ?> (<?= $this->getHtml('YTD'); ?>) - <?= $this->getHtml('DomesticExport'); ?></div>
                        <div class="portlet-body">
                            <canvas id="sales-domestic-export" data-chart='{
                                "type": "pie",
                                "data": {
                                    "labels": [
                                            "<?= \implode('","', \array_keys($this->data['ytdADomesticExport'])); ?>"
                                        ],
                                    "datasets": [{
                                        "data": [
                                            <?php
                                            $temp = [];
                                            foreach ($this->data['ytdADomesticExport'] as $values) {
                                                $temp[] = $values['net_sales'] / 10000;
                                            }
                                            echo \implode(',', $temp);
                                            ?>
                                        ]
                                    }]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-domestic-export-region" type="checkbox" name="more-container">
                                <label for="more-domestic-export-region">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
                                <table class="default sticky">
                                    <thead>
                                        <tr>
                                            <td><?= $this->getHtml('Region'); ?>
                                            <td><?= $this->getHtml('Sales'); ?>
                                    <tbody>
                                        <?php
                                            $sum = 0;
                                        foreach ($this->data['ytdADomesticExport'] as $region => $values) : $sum += $values['net_sales']; ?>
                                            <tr>
                                                <td><?= $region; ?>
                                                <td><?= $this->getCurrency($values['net_sales']); ?>
                                        <?php endforeach; ?>
                                            <tr>
                                                <td><?= $this->getHtml('Total'); ?>
                                                <td><?= $this->getCurrency($sum); ?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xs-12 col-lg-8">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Sales'); ?> (<?= $this->getHtml('annually'); ?>) - <?= $this->getHtml('DomesticExport'); ?></div>
                        <div class="portlet-body">
                            <canvas id="sales-annually-domestic-export" data-chart='{
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
                                            foreach ($this->data['annualDomesticExport'] as $region => $values) :
                                            echo($first ? '' : ',');
                                            $first = false;
                                        ?>{
                                            "label": "<?= $this->printHtml($region); ?>",
                                            "type": "line",
                                            "data": [
                                                <?php
                                                    $temp = [];
                                                    foreach ($values as $annual) {
                                                        $temp[] = ((int) ($annual['net_sales'] ?? 0)) / 10000;
                                                    }
                                                    echo \implode(',', $temp);
                                                ?>
                                            ],
                                            "fill": false,
                                            "tension": 0.0
                                        }
                                        <?php endforeach; ?>
                                    ]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-sales-domestic-export-annual" type="checkbox" name="more-container">
                                <label for="more-sales-domestic-export-annual">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
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
                                        foreach ($this->data['annualDomesticExport'] as $region => $values) : ?>
                                                <tr>
                                                    <td><?= $this->printHtml($region); ?>
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
            </div>

            <div class="row">
                <div class="col-xs-12 col-lg-4">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Profit'); ?> (<?= $this->getHtml('YTD'); ?>) - <?= $this->getHtml('DomesticExport'); ?></div>
                        <div class="portlet-body">
                            <canvas id="profit-domestic-export-region" data-chart='{
                                "type": "pie",
                                "data": {
                                    "labels": [
                                            "<?= \implode('","', \array_keys($this->data['ytdADomesticExport'])); ?>"
                                        ],
                                    "datasets": [{
                                        "data": [
                                            <?php
                                            $temp = [];
                                            foreach ($this->data['ytdADomesticExport'] as $values) {
                                                $temp[] = $values['net_profit'] / 10000;
                                            }
                                            echo \implode(',', $temp);
                                            ?>
                                        ]
                                    }]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-domestic-export-profit" type="checkbox" name="more-container">
                                <label for="more-domestic-export-profit">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
                                <table class="default sticky">
                                    <thead>
                                        <tr>
                                            <td><?= $this->getHtml('Region'); ?>
                                            <td><?= $this->getHtml('Profit'); ?>
                                    <tbody>
                                        <?php
                                            $sum = 0;
                                        foreach ($this->data['ytdADomesticExport'] as $region => $values) : $sum += $values['net_profit']; ?>
                                            <tr>
                                                <td><?= $region; ?>
                                                <td><?= $this->getCurrency($values['net_profit']); ?>
                                        <?php endforeach; ?>
                                            <tr>
                                                <td><?= $this->getHtml('Total'); ?>
                                                <td><?= $this->getCurrency($sum); ?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xs-12 col-lg-8">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Profit'); ?> (<?= $this->getHtml('annually'); ?>) - <?= $this->getHtml('DomesticExport'); ?></div>
                        <div class="portlet-body">
                            <canvas id="profit-annually-domestic-export" data-chart='{
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
                                            foreach ($this->data['annualDomesticExport'] as $region => $values) :
                                            echo($first ? '' : ',');
                                            $first = false;
                                        ?>{
                                            "label": "<?= $this->printHtml($region); ?>",
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
                                            "tension": 0.0
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
                                <input id="more-profit-domestic-export-annual" type="checkbox" name="more-container">
                                <label for="more-profit-domestic-export-annual">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
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
                                        foreach ($this->data['annualDomesticExport'] as $region => $values) : ?>
                                                <tr>
                                                    <td><?= $this->printHtml($region); ?>
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

            <div class="row">
                <div class="col-xs-12 col-lg-4">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Clients'); ?> (<?= $this->getHtml('YTD'); ?>) - <?= $this->getHtml('DomesticExport'); ?></div>
                        <div class="portlet-body">
                            <canvas id="client-count-domestic-export" data-chart='{
                                "type": "pie",
                                "data": {
                                    "labels": [
                                            "<?= \implode('","', \array_keys($this->data['ytdADomesticExportCount'])); ?>"
                                        ],
                                    "datasets": [{
                                        "data": [
                                            <?php
                                            $temp = [];
                                            foreach ($this->data['ytdADomesticExportCount'] as $values) {
                                                $temp[] = $values['client_count'];
                                            }
                                            echo \implode(',', $temp);
                                            ?>
                                        ]
                                    }]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-client-count-domestic-export-region" type="checkbox" name="more-container">
                                <label for="more-client-count-domestic-export-region">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
                                <table class="default sticky">
                                    <thead>
                                        <tr>
                                            <td><?= $this->getHtml('Region'); ?>
                                            <td><?= $this->getHtml('Clients'); ?>
                                    <tbody>
                                        <?php
                                            $sum = 0;
                                        foreach ($this->data['ytdADomesticExportCount'] as $region => $values) : $sum += $values['client_count']; ?>
                                            <tr>
                                                <td><?= $region; ?>
                                                <td><?= $this->getNumeric($values['client_count'], format: 'very_short'); ?>
                                        <?php endforeach; ?>
                                            <tr>
                                                <td><?= $this->getHtml('Total'); ?>
                                                <td><?= $this->getNumeric($sum, format: 'very_short'); ?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xs-12 col-lg-8">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Clients'); ?> (<?= $this->getHtml('annually'); ?>) - <?= $this->getHtml('DomesticExport'); ?></div>
                        <div class="portlet-body">
                            <canvas id="client-count-annually-domestic-export" data-chart='{
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
                                            foreach ($this->data['annualDomesticExportCount'] as $region => $values) :
                                            echo($first ? '' : ',');
                                            $first = false;
                                        ?>{
                                            "label": "<?= $this->printHtml($region); ?>",
                                            "type": "line",
                                            "data": [
                                                <?php
                                                    $temp = [];
                                                    foreach ($values as $annual) {
                                                        $temp[] = ((int) ($annual['client_count'] ?? 0));
                                                    }
                                                    echo \implode(',', $temp);
                                                ?>
                                            ],
                                            "fill": false,
                                            "tension": 0.0
                                        }
                                        <?php endforeach; ?>
                                    ]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-client-count-domestic-export-annual" type="checkbox" name="more-container">
                                <label for="more-client-count-domestic-export-annual">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
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
                                        foreach ($this->data['annualDomesticExportCount'] as $region => $values) : ?>
                                                <tr>
                                                    <td><?= $this->printHtml($region); ?>
                                                <?php foreach ($values as $idx => $annual) :
                                                    $sum[$idx] = ($sum[$idx] ?? 0) + ($annual['client_count'] ?? 0);
                                                ?>
                                                    <td><?= $this->getNumeric($annual['client_count'] ?? 0, format: 'very_short'); ?>
                                        <?php endforeach; endforeach; ?>
                                        <tr>
                                            <td><?= $this->getHtml('Total'); ?>
                                            <?php foreach ($sum as $value) : ?>
                                                <td><?= $this->getNumeric($value, format: 'very_short'); ?>
                                            <?php endforeach; ?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-3" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-lg-4">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Sales'); ?> (<?= $this->getHtml('YTD'); ?>) - <?= $this->getHtml('Continent'); ?></div>
                        <div class="portlet-body">
                            <canvas id="sales-continent" data-chart='{
                                "type": "pie",
                                "data": {
                                    "labels": [
                                            "<?= \implode('","', \array_keys($this->data['ytdAContinent'])); ?>"
                                        ],
                                    "datasets": [{
                                        "data": [
                                            <?php
                                            $temp = [];
                                            foreach ($this->data['ytdAContinent'] as $values) {
                                                $temp[] = $values['net_sales'] / 10000;
                                            }
                                            echo \implode(',', $temp);
                                            ?>
                                        ]
                                    }]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-continent-region" type="checkbox" name="more-container">
                                <label for="more-continent-region">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
                                <table class="default sticky">
                                    <thead>
                                        <tr>
                                            <td><?= $this->getHtml('Region'); ?>
                                            <td><?= $this->getHtml('Sales'); ?>
                                    <tbody>
                                        <?php
                                            $sum = 0;
                                        foreach ($this->data['ytdAContinent'] as $region => $values) : $sum += $values['net_sales']; ?>
                                            <tr>
                                                <td><?= $region; ?>
                                                <td><?= $this->getCurrency($values['net_sales']); ?>
                                        <?php endforeach; ?>
                                            <tr>
                                                <td><?= $this->getHtml('Total'); ?>
                                                <td><?= $this->getCurrency($sum); ?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xs-12 col-lg-8">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Sales'); ?> (<?= $this->getHtml('annually'); ?>) - <?= $this->getHtml('Continent'); ?></div>
                        <div class="portlet-body">
                            <canvas id="sales-annually-continent" data-chart='{
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
                                            foreach ($this->data['annualContinent'] as $region => $values) :
                                            echo($first ? '' : ',');
                                            $first = false;
                                        ?>{
                                            "label": "<?= $this->getHtml($region); ?>",
                                            "type": "line",
                                            "data": [
                                                <?php
                                                    $temp = [];
                                                    foreach ($values as $annual) {
                                                        $temp[] = ((int) ($annual['net_sales'] ?? 0)) / 10000;
                                                    }
                                                    echo \implode(',', $temp);
                                                ?>
                                            ],
                                            "fill": false,
                                            "tension": 0.0
                                        }
                                        <?php endforeach; ?>
                                    ]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-sales-continent-annual" type="checkbox" name="more-container">
                                <label for="more-sales-continent-annual">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
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
                                        foreach ($this->data['annualContinent'] as $region => $values) : ?>
                                                <tr>
                                                    <td><?= $this->getHtml($region); ?>
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
            </div>

            <div class="row">
                <div class="col-xs-12 col-lg-4">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Profit'); ?> (<?= $this->getHtml('YTD'); ?>) - <?= $this->getHtml('Continent'); ?></div>
                        <div class="portlet-body">
                            <canvas id="profit-continent-region" data-chart='{
                                "type": "pie",
                                "data": {
                                    "labels": [
                                            "<?= \implode('","', \array_keys($this->data['ytdAContinent'])); ?>"
                                        ],
                                    "datasets": [{
                                        "data": [
                                            <?php
                                            $temp = [];
                                            foreach ($this->data['ytdAContinent'] as $values) {
                                                $temp[] = $values['net_profit'] / 10000;
                                            }
                                            echo \implode(',', $temp);
                                            ?>
                                        ]
                                    }]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-continent-profit" type="checkbox" name="more-container">
                                <label for="more-continent-profit">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
                                <table class="default sticky">
                                    <thead>
                                        <tr>
                                            <td><?= $this->getHtml('Region'); ?>
                                            <td><?= $this->getHtml('Profit'); ?>
                                    <tbody>
                                        <?php
                                            $sum = 0;
                                        foreach ($this->data['ytdAContinent'] as $region => $values) : $sum += $values['net_profit']; ?>
                                            <tr>
                                                <td><?= $region; ?>
                                                <td><?= $this->getCurrency($values['net_profit']); ?>
                                        <?php endforeach; ?>
                                            <tr>
                                                <td><?= $this->getHtml('Total'); ?>
                                                <td><?= $this->getCurrency($sum); ?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xs-12 col-lg-8">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Profit'); ?> (<?= $this->getHtml('annually'); ?>) - <?= $this->getHtml('Continent'); ?></div>
                        <div class="portlet-body">
                            <canvas id="profit-annually-continent" data-chart='{
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
                                            foreach ($this->data['annualContinent'] as $region => $values) :
                                            echo($first ? '' : ',');
                                            $first = false;
                                        ?>{
                                            "label": "<?= $this->getHtml($region); ?>",
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
                                            "tension": 0.0
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
                                <input id="more-profit-continent-annual" type="checkbox" name="more-container">
                                <label for="more-profit-continent-annual">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
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
                                        foreach ($this->data['annualContinent'] as $region => $values) : ?>
                                                <tr>
                                                    <td><?= $this->getHtml($region); ?>
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

            <div class="row">
                <div class="col-xs-12 col-lg-4">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Clients'); ?> (<?= $this->getHtml('YTD'); ?>) - <?= $this->getHtml('Continent'); ?></div>
                        <div class="portlet-body">
                            <canvas id="client-count-continent" data-chart='{
                                "type": "pie",
                                "data": {
                                    "labels": [
                                            "<?= \implode('","', \array_keys($this->data['ytdAContinentCount'])); ?>"
                                        ],
                                    "datasets": [{
                                        "data": [
                                            <?php
                                            $temp = [];
                                            foreach ($this->data['ytdAContinentCount'] as $values) {
                                                $temp[] = $values['client_count'];
                                            }
                                            echo \implode(',', $temp);
                                            ?>
                                        ]
                                    }]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-client-count-continent-region" type="checkbox" name="more-container">
                                <label for="more-client-count-continent-region">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
                                <table class="default sticky">
                                    <thead>
                                        <tr>
                                            <td><?= $this->getHtml('Region'); ?>
                                            <td><?= $this->getHtml('Clients'); ?>
                                    <tbody>
                                        <?php
                                            $sum = 0;
                                        foreach ($this->data['ytdAContinentCount'] as $region => $values) : $sum += $values['client_count']; ?>
                                            <tr>
                                                <td><?= $region; ?>
                                                <td><?= $this->getNumeric($values['client_count'], format: 'very_short'); ?>
                                        <?php endforeach; ?>
                                            <tr>
                                                <td><?= $this->getHtml('Total'); ?>
                                                <td><?= $this->getNumeric($sum, format: 'very_short'); ?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xs-12 col-lg-8">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Clients'); ?> (<?= $this->getHtml('annually'); ?>) - <?= $this->getHtml('Continent'); ?></div>
                        <div class="portlet-body">
                            <canvas id="client-count-annually-continent" data-chart='{
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
                                            foreach ($this->data['annualContinentCount'] as $region => $values) :
                                            echo($first ? '' : ',');
                                            $first = false;
                                        ?>{
                                            "label": "<?= $this->printHtml($region); ?>",
                                            "type": "line",
                                            "data": [
                                                <?php
                                                    $temp = [];
                                                    foreach ($values as $annual) {
                                                        $temp[] = ((int) ($annual['client_count'] ?? 0));
                                                    }
                                                    echo \implode(',', $temp);
                                                ?>
                                            ],
                                            "fill": false,
                                            "tension": 0.0
                                        }
                                        <?php endforeach; ?>
                                    ]
                                },
                                "options": {
                                    "responsive": true
                                }
                            }'></canvas>

                            <div class="more-container">
                                <input id="more-client-count-continent-annual" type="checkbox" name="more-container">
                                <label for="more-client-count-continent-annual">
                                    <span><?= $this->getHtml('Data'); ?></span>
                                    <i class="g-icon expand">chevron_right</i>
                                </label>
                                <div class="slider">
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
                                        foreach ($this->data['annualContinentCount'] as $region => $values) : ?>
                                                <tr>
                                                    <td><?= $this->printHtml($region); ?>
                                                <?php foreach ($values as $idx => $annual) :
                                                    $sum[$idx] = ($sum[$idx] ?? 0) + ($annual['client_count'] ?? 0);
                                                ?>
                                                    <td><?= $this->getNumeric($annual['client_count'] ?? 0, format: 'very_short'); ?>
                                        <?php endforeach; endforeach; ?>
                                        <tr>
                                            <td><?= $this->getHtml('Total'); ?>
                                            <?php foreach ($sum as $value) : ?>
                                                <td><?= $this->getNumeric($value, format: 'very_short'); ?>
                                            <?php endforeach; ?>
                                </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-4" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Sales'); ?></div>
                        <div class="slider">
                        <table class="default sticky">
                            <thead>
                                <tr>
                                    <td><?= $this->getHtml('Region'); ?>
                                    <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                                    <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('YTD'); ?>)
                                    <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                                    <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('MTD'); ?>)
                                    <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('MTD'); ?>)
                                    <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('MTD'); ?>)
                            <tbody>
                                <?php foreach ($this->data['ytdARegions'] as $type => $values) : ?>
                                    <tr>
                                        <td><?= $this->printHtml($type); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdPYRegions'][$type]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdARegions'][$type]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['ytdARegions'][$type]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['ytdPYRegions'][$type]['net_sales'] ?? 0))
                                        ); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdPYRegions'][$type]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdARegions'][$type]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['mtdARegions'][$type]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['mtdPYRegions'][$type]['net_sales'] ?? 0))
                                        ); ?>
                                <?php endforeach; ?>
                        </table>
                        </div>
                </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-5" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-3' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-lg-6">
                    <section class="portlet">
                        <form>
                           <div class="portlet-head"><?= $this->getHtml('Filter'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iId"><?= $this->getHtml('Client'); ?></label>
                                    <input type="text" id="iName1" name="name1">
                                </div>

                                <div class="form-group">
                                    <div class="input-control">
                                        <label for="iDecimalPoint"><?= $this->getHtml('BaseTime'); ?></label>
                                        <input id="iDecimalPoint" name="settings_decimal" type="text" value="" placeholder=".">
                                    </div>

                                    <div class="input-control">
                                        <label for="iThousandSep"><?= $this->getHtml('ComparisonTime'); ?></label>
                                        <input id="iThousandSep" name="settings_thousands" type="text" value="" placeholder=",">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-control">
                                        <label for="iDecimalPoint"><?= $this->getHtml('Attribute'); ?></label>
                                        <input id="iDecimalPoint" name="settings_decimal" type="text" value="" placeholder=".">
                                    </div>

                                    <div class="input-control">
                                        <label for="iThousandSep"><?= $this->getHtml('Value'); ?></label>
                                        <input id="iThousandSep" name="settings_thousands" type="text" value="" placeholder=",">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="iId"><?= $this->getHtml('Region'); ?></label>
                                    <input type="text" id="iName1" name="name1">
                                </div>

                                <div class="form-group">
                                    <label for="iId"><?= $this->getHtml('Country'); ?></label>
                                    <input type="text" id="iName1" name="name1">
                                </div>

                                <div class="form-group">
                                    <label for="iId"><?= $this->getHtml('Rep'); ?></label>
                                    <input type="text" id="iName1" name="name1">
                                </div>
                            </div>
                            <div class="portlet-foot"><input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>"></div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>