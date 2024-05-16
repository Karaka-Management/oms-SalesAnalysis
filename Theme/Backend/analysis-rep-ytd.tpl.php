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
                <?= $this->getHtml('SalesRep', 'Sales', 'Backend'); ?>
            </div>
            <div class="slider more">
            <table class="default sticky">
                <thead>
                    <tr>
                        <td class="wf-100"><?= $this->getHtml('SalesRep', 'Sales', 'Backend'); ?>
                        <td><?= $this->getHtml('SalesPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('SalesA'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                <tbody>
                    <?php
                    \uasort($this->data['ytdAClientRep'], function (array $a, array $b) { return (($b['net_sales'] ?? 0) <=> $a['net_sales'] ?? 0); });
                    $total = ['py' => 0, 'a' => 0];
                    foreach ($this->data['ytdAClientRep'] as $type => $values) :
                        $total['py'] += $this->data['ytdPYClientRep'][$type]['net_sales'] ?? 0;
                        $total['a']  += $this->data['ytdAClientRep'][$type]['net_sales'] ?? 0;
                    ?>
                        <tr>
                            <td><?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main->name2); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdPYClientRep'][$type]['net_sales'] ?? 0), symbol: ''); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdAClientRep'][$type]['net_sales'] ?? 0), symbol: ''); ?>
                            <td><?= $this->getCurrency(
                                ((int) ($this->data['ytdAClientRep'][$type]['net_sales'] ?? 0)) -
                                ((int) ($this->data['ytdPYClientRep'][$type]['net_sales'] ?? 0)),
                                symbol: ''
                            ); ?>
                    <?php endforeach; ?>
                    <tr class="hl-3">
                        <td><?= $this->getHtml('Total', '0', '0'); ?>
                        <td><?= $this->getCurrency((int) $total['py'], symbol: ''); ?>
                        <td><?= $this->getCurrency((int) $total['a'], symbol: ''); ?>
                        <td><?= $this->getCurrency(
                                $total['a'] - $total['py'],
                                symbol: ''
                            ); ?>
            </table>
            </div>
        </section>
    </div>

    <div class="col-xs-12 col-lg-6">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('SalesRep', 'Sales', 'Backend'); ?>
            </div>
            <div class="slider more">
            <table class="default sticky">
                <thead>
                    <tr>
                        <td class="wf-100"><?= $this->getHtml('SalesRep', 'Sales', 'Backend'); ?>
                        <td><?= $this->getHtml('ProfitPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('ProfitA'); ?> (<?= $this->getHtml('YTD'); ?>)
                        <td><?= $this->getHtml('DiffPY'); ?> (<?= $this->getHtml('YTD'); ?>)
                <tbody>
                    <?php
                    \uasort($this->data['ytdAClientRep'], function (array $a, array $b) { return (($b['net_profit'] ?? 0) <=> $a['net_profit'] ?? 0); });
                    $total = ['py' => 0, 'a' => 0];
                    foreach ($this->data['ytdAClientRep'] as $type => $values) :
                        $total['py'] += $this->data['ytdPYClientRep'][$type]['net_profit'] ?? 0;
                        $total['a']  += $this->data['ytdAClientRep'][$type]['net_profit'] ?? 0;
                    ?>
                        <tr>
                            <td><?= $this->printHtml($this->data['salesRep'][$type]->code); ?> - <?= $this->printHtml($this->data['salesRep'][$type]->main->name1); ?> <?= $this->printHtml($this->data['salesRep'][$type]->main->name2); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdPYClientRep'][$type]['net_profit'] ?? 0), symbol: ''); ?>
                            <td><?= $this->getCurrency((int) ($this->data['ytdAClientRep'][$type]['net_profit'] ?? 0), symbol: ''); ?>
                            <td><?= $this->getCurrency(
                                ((int) ($this->data['ytdAClientRep'][$type]['net_profit'] ?? 0)) -
                                ((int) ($this->data['ytdPYClientRep'][$type]['net_profit'] ?? 0)),
                                symbol: ''
                            ); ?>
                    <?php endforeach; ?>
                    <tr class="hl-3">
                        <td><?= $this->getHtml('Total', '0', '0'); ?>
                        <td><?= $this->getCurrency((int) $total['py'], symbol: ''); ?>
                        <td><?= $this->getCurrency((int) $total['a'], symbol: ''); ?>
                        <td><?= $this->getCurrency(
                                $total['a'] - $total['py'],
                                symbol: ''
                            ); ?>
            </table>
            </div>
        </section>
    </div>
</div>