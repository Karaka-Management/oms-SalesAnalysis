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
?>

<div class="tabview tab-2">
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('All'); ?></label>
            <li><label for="c-tab-2"><?= $this->getHtml('New'); ?></label>
            <li><label for="c-tab-3"><?= $this->getHtml('Lost'); ?></label>
            <!--<li><label for="c-tab-1"><?= $this->getHtml('Filter'); ?></label>-->
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <img height="100%" src="Web/Backend/img/under_construction.svg">
        </div>

        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <img height="100%" src="Web/Backend/img/under_construction.svg">
        </div>

        <input type="radio" id="c-tab-3" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-3' ? ' checked' : ''; ?>>
        <div class="tab">
            <img height="100%" src="Web/Backend/img/under_construction.svg">
        </div>
    </div>
</div>