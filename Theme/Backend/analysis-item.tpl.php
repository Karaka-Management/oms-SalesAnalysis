<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\SalesAnalysis
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
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
            <li><label for="c-tab-3"><?= $this->getHtml('SalesGroup'); ?></label>
            <li><label for="c-tab-4"><?= $this->getHtml('ProductGroup'); ?></label>
            <li><label for="c-tab-5"><?= $this->getHtml('Type'); ?></label>
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
                                    foreach ($this->data['ytdAItemAttribute']['segment'] as $values) :
                                ?>
                                    <tr>
                                        <td><?= $this->printHtml($values['value_l11n']); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdPYItemAttribute']['segment'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdAItemAttribute']['segment'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['ytdAItemAttribute']['segment'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['ytdPYItemAttribute']['segment'][$values['value_l11n']]['net_sales'] ?? 0))
                                        ); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdPYItemAttribute']['segment'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdAItemAttribute']['segment'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['mtdAItemAttribute']['segment'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['mtdPYItemAttribute']['segment'][$values['value_l11n']]['net_sales'] ?? 0))
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
                                    foreach ($this->data['ytdAItemAttribute']['section'] as $values) :
                                ?>
                                    <tr>
                                        <td><?= $this->printHtml($values['value_l11n']); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdPYItemAttribute']['section'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdAItemAttribute']['section'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['ytdAItemAttribute']['section'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['ytdPYItemAttribute']['section'][$values['value_l11n']]['net_sales'] ?? 0))
                                        ); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdPYItemAttribute']['section'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdAItemAttribute']['section'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['mtdAItemAttribute']['section'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['mtdPYItemAttribute']['section'][$values['value_l11n']]['net_sales'] ?? 0))
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
                        <div class="portlet-head"><?= $this->getHtml('SalesGroup'); ?></div>
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
                                    foreach ($this->data['ytdAItemAttribute']['sales_group'] as $values) :
                                ?>
                                    <tr>
                                        <td><?= $this->printHtml($values['value_l11n']); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdPYItemAttribute']['sales_group'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdAItemAttribute']['sales_group'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['ytdAItemAttribute']['sales_group'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['ytdPYItemAttribute']['sales_group'][$values['value_l11n']]['net_sales'] ?? 0))
                                        ); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdPYItemAttribute']['sales_group'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdAItemAttribute']['sales_group'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['mtdAItemAttribute']['sales_group'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['mtdPYItemAttribute']['sales_group'][$values['value_l11n']]['net_sales'] ?? 0))
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
                        <div class="portlet-head"><?= $this->getHtml('ProductGroup'); ?></div>
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
                                    foreach ($this->data['ytdAItemAttribute']['product_group'] as $values) :
                                ?>
                                    <tr>
                                        <td><?= $this->printHtml($values['value_l11n']); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdPYItemAttribute']['product_group'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdAItemAttribute']['product_group'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['ytdAItemAttribute']['product_group'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['ytdPYItemAttribute']['product_group'][$values['value_l11n']]['net_sales'] ?? 0))
                                        ); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdPYItemAttribute']['product_group'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdAItemAttribute']['product_group'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['mtdAItemAttribute']['product_group'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['mtdPYItemAttribute']['product_group'][$values['value_l11n']]['net_sales'] ?? 0))
                                        ); ?>
                                <?php endforeach; ?>
                        </table>
                        </div>
                </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-5" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-5' ? ' checked' : ''; ?>>
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
                                    foreach ($this->data['ytdAItemAttribute']['product_type'] as $values) :
                                ?>
                                    <tr>
                                        <td><?= $this->printHtml($values['value_l11n']); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdPYItemAttribute']['product_type'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['ytdAItemAttribute']['product_type'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['ytdAItemAttribute']['product_type'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['ytdPYItemAttribute']['product_type'][$values['value_l11n']]['net_sales'] ?? 0))
                                        ); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdPYItemAttribute']['product_type'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency((int) ($this->data['mtdAItemAttribute']['product_type'][$values['value_l11n']]['net_sales'] ?? 0)); ?>
                                        <td><?= $this->getCurrency(
                                            ((int) ($this->data['mtdAItemAttribute']['product_type'][$values['value_l11n']]['net_sales'] ?? 0)) -
                                            ((int) ($this->data['mtdPYItemAttribute']['product_type'][$values['value_l11n']]['net_sales'] ?? 0))
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