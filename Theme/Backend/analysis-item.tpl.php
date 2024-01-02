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

/**
 * @var \phpOMS\Views\View $this
 */

echo $this->data['nav']->render();
?>
<div class="tabview tab-2">
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('Segment'); ?></label>
            <li><label for="c-tab-2"><?= $this->getHtml('Section'); ?></label>
            <li><label for="c-tab-3"><?= $this->getHtml('Group'); ?></label>
            <li><label for="c-tab-4"><?= $this->getHtml('Type'); ?></label>
            <!--<li><label for="c-tab-5"><?= $this->getHtml('Filter'); ?></label>-->
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Segment'); ?></div>
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
                                <?php
                                    foreach ($this->data['ytdAItemAttribute'] as $type => $values) :
                                        if ($type !== 'segment') {
                                            continue;
                                        }
                                ?>
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
        </div>

        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Section'); ?></div>
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
                                <?php
                                    foreach ($this->data['ytdAItemAttribute'] as $type => $values) :
                                        if ($type !== 'section') {
                                            continue;
                                        }
                                ?>
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
        </div>

        <input type="radio" id="c-tab-3" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-3' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Group'); ?></div>
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
                                <?php
                                    foreach ($this->data['ytdAItemAttribute'] as $type => $values) :
                                        if ($type !== 'product_group') {
                                            continue;
                                        }
                                ?>
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
        </div>

        <input type="radio" id="c-tab-4" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-4' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Type'); ?></div>
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
                                <?php
                                    foreach ($this->data['ytdAItemAttribute'] as $type => $values) :
                                        if ($type !== 'product_type') {
                                            continue;
                                        }
                                ?>
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
        </div>
    </div>
</div>