<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Package\EasyImageSlider\Block\EasyImageSlider\Controller $controller
 * @var Concrete\Core\Block\View\BlockView|Concrete\Core\Page\Type\Composer\Control\BlockControl $view
 * @var Concrete\Core\Block\View\BlockView|Concrete\Core\Page\Type\Composer\Control\BlockControl $this
 * @var Concrete\Core\Form\Service\Form $form
 */

// In Composer conditions, it seems that the 'options' variables are overide after the inc() function
// So i need to set some variable here... Don't know why ?
$options = $controller->getOptionsJson();

?>
<div class="row">
    <div id="tab-content-settings" class="ccm-ui col-md-12 options-content">
        <table class="grouping">
            <th><?php echo $form->label($view->field('items'), t('Items to show')) ?></th>
            <th><?php echo $form->label($view->field('margin'), t('margin between items')) ?></th>
            <th><?php echo $form->label($view->field('slideSpeed'), t('Slide Speed')) ?></th>
            <th><?php echo $form->label($view->field('autoPlay'), t('Auto Play')) ?></th>
            <tr>
                <td>
                    <div class="input-group form-group form-group-small">
                        <?php echo $form->text($view->field('items'), $options->items) ?>
                        <span class="input-group-addon"> <?php echo t('items') ?> </span>
                    </div>
                    <small><?php echo t('The maximum amount of items displayed<br />at a time with the widest browser width') ?></small>
                </td>
                <td>
                    <div class="input-group form-group form-group-small">
                        <?php echo $form->text($view->field('margin'), $options->margin, array('placeholder' => t('5'))) ?>
                        <span class="input-group-addon"> px </span>
                    </div>
                    <small><?php echo t('0 for no margins') ?></small>
                </td>
                <td>
                    <div class="input-group form-group form-group-small">
                        <?php echo $form->text($view->field('slideSpeed'), $options->slideSpeed) ?>
                        <span class="input-group-addon"> ms </span>
                    </div>
                </td>
                <td>
                    <div class="input-group form-group form-group-middle">
                        <span class="input-group-addon">
                            <?php echo $form->checkbox($view->field('autoPlay'), '1', $options->autoPlay) ?>
                        </span>
                        <?php echo $form->text($view->field('autoPlayTime'), $options->autoPlayTime, array('placeholder' => t('5000'))) ?>
                        <span class="input-group-addon"> ms </span>
                    </div>
                    <small><?php echo t('If activated, set the time <br/> between slides') ?></small>
                </td>
            </tr>
        </table>
        <!-- table.grouping>th*2+tr>td*2 -->
        <table class="grouping">
            <th><?php echo $form->label($view->field('ItemsTitle'), t('Items Title')) ?></th>
            <th><?php echo $form->label($view->field('ItemsDescription'), t('Items Description')) ?></th>
            <tr>
                <td>
                    <input type="radio" name="<?php echo $view->field('ItemsTitle') ?>" value="1" <?php echo $options->ItemsTitle == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('ItemsTitle') ?>" value="0" <?php echo $options->ItemsTitle == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                    <small><?php echo t('(On some Templates)') ?></small>
                </td>
                <td>
                    <input type="radio" name="<?php echo $view->field('ItemsDescription') ?>" value="1" <?php echo $options->ItemsDescription == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('ItemsDescription') ?>" value="0" <?php echo $options->ItemsDescription == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                    <small><?php echo t('(On some Templates)') ?></small>
                </td>
            </tr>
        </table>
        <table class="grouping">
            <th><?php echo $form->label($view->field('infoBg'), t('Info Box on Hover')) ?></th>
            <th><?php echo $form->label($view->field('fadingColor'), t('Gallery Background Color')) ?></th>
            <th><?php echo $form->label($view->field('isTransparent'), t('Color Transitions')) ?></th>
            <tr>
                <td>
                    <?php $col = new Concrete\Core\Form\Service\Widget\Color(); $col->output($view->field('infoBg'), $options->infoBg, array('preferredFormat' => 'rgba')) ?>
                    <small><?php echo t('The square with <br /> title and description') ?></small>
                </td>
                <td>
                    <?php $col = new Concrete\Core\Form\Service\Widget\Color(); $col->output($view->field('fadingColor'), $options->fadingColor, array('preferredFormat' => 'rgba')) ?>
                    <small><?php echo t('This settings is overrided by items colors') ?></small>
                </td>
                <td>
                    <input type="radio" name="<?php echo $view->field('isTransparent') ?>" value="1" <?php echo $options->isTransparent == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('isTransparent') ?>" value="0" <?php echo $options->isTransparent == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                    <small><?php echo t('Enable if you want cool colors <br /> transitions on single slides') ?></small>
                </td>
            </tr>
        </table>
        <!--
        <table class="grouping">
            <th><?php echo $form->label($view->field('animateIn'), t('Animation In')) ?></th>
            <th><?php echo $form->label($view->field('animateOut'), t('Animation Out')) ?></th>
            <tr>
                <td>
                    <select name="animateIn"><?php $this->inc('elements/animate.php', array('option' => $options->animateIn)) ?></select>
                </td>
                <td>
                    <select name="animateOut"><?php $this->inc('elements/animate.php', array('option' => $options->animateOut)) ?></select>
                </td>
            </tr>
        </table>
        -->
        <div class="footer">
            <button class="btn btn-primary easy_image_options_close" type="button" id=""><?php echo t('Close') ?></button>
        </div>
    </div>
</div>
<div class="row">
    <div id="tab-content-lightbox" class="ccm-ui col-md-12 options-content">
        <div class="form-group">
            <?php echo $form->label($view->field('lightbox'), t('Lightbox')) ?>
            <?php echo $form->select($view->field('lightbox'), array('0' => t('None'), 'intense' => t('Full Screen'), 'lightbox' => t('Simple Lightbox')), $options->lightbox, array('style' => 'width:180px')) ?>
        </div>
        <table class="grouping">
            <th><?php echo $form->label($view->field('fancyOverlay'), t('Lightbox overlay color')) ?></th>
            <th><?php echo $form->label($view->field('fancyOverlayAlpha'), t('Lightbox overlay opacity (from 0 to 1)')) ?></th>
            <tr>
                <td><?php $col = new Concrete\Core\Form\Service\Widget\Color(); $col->output($view->field('fancyOverlay'), $options->fancyOverlay, array('preferredFormat' => 'rgba')) ?></td>
                <td><?php echo $form->text($view->field('fancyOverlayAlpha'), $options->fancyOverlayAlpha) ?></td>
            </tr>
        </table>
        <table class="grouping">
            <th><?php echo $form->label($view->field('lightboxTitle'), t('Display Title in Lightbox')) ?></th>
            <th><?php echo $form->label($view->field('lightboxDescription'), t('Display Description in Lightbox')) ?></th>
            <tr>
                <td>
                    <input type="radio" name="<?php echo $view->field('lightboxTitle') ?>" value="1" <?php echo $options->lightboxTitle == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('lightboxTitle') ?>" value="0" <?php echo $options->lightboxTitle == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                </td>
                <td>
                    <input type="radio" name="<?php echo $view->field('lightboxDescription') ?>" value="1" <?php echo $options->lightboxDescription == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('lightboxDescription') ?>" value="0" <?php echo $options->lightboxDescription == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                    <small><?php echo t('Only with full screen') ?></small>
                </td>
            </tr>
        </table>
        <div class="footer">
            <button class="btn btn-primary easy_image_options_close" type="button" id=""><?php echo t('Close') ?></button>
        </div>
    </div>
</div>
<div class="row">
    <div id="tab-content-advanced" class="ccm-ui col-md-12 options-content">
        <table class="grouping">
            <th><?php echo $form->label($view->field('dots'), t('Dots Navigation')) ?></th>
            <th><?php echo $form->label($view->field('navigation'), t('Navigation + Navigation text')) ?></th>
            <th><?php echo $form->label($view->field('loop'), t('Loop Navigation')) ?></th>
            <tr>
                <td>
                    <input type="radio" name="<?php echo $view->field('dots') ?>" value="1" <?php echo $options->dots == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('dots') ?>" value="0" <?php echo $options->dots == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                </td>
                <td>
                    <div class="input-group form-group">
                        <span class="input-group-addon">
                            <?php echo $form->checkbox($view->field('nav'), '1', $options->nav) ?>
                        </span>
                        <?php echo $form->text($view->field('navigationPrev'), $options->navigationPrev, array('placeholder' => t('Prev'), 'style' => 'width:80px')) ?>
                        <span class="input-group-addon"> / </span>
                        <?php echo $form->text($view->field('navigationNext'), $options->navigationNext, array('placeholder' => t('Next'), 'style' => 'width:80px')) ?>
                    </div>
                </td>
                <td>
                    <input type="radio" name="<?php echo $view->field('loop') ?>" value="1" <?php echo $options->loop == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('loop') ?>" value="0" <?php echo $options->loop == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                </td>
            </tr>
        </table>
        <table class="grouping">
            <th><?php echo $form->label($view->field('responsiveContainer'), t('Responsive Container')) ?></th>
            <th><?php echo $form->label($view->field('lazy'), t('Lazy Loading')) ?></th>
            <tr>
                <td>
                    <input type="radio" name="<?php echo $view->field('responsiveContainer') ?>" value="1" <?php echo $options->responsiveContainer == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('responsiveContainer') ?>" value="0" <?php echo $options->responsiveContainer == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                    <small><?php echo t('Disable if you want to see <br /> a full width gallery. Otherwise it respect <br/> the width of bootstrap container ') ?></small>
                </td>
                <td>
                    <input type="radio" name="<?php echo $view->field('lazy') ?>" value="1" <?php echo $options->lazy == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('lazy') ?>" value="0" <?php echo $options->lazy == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                </td>
            </tr>
        </table>
        <!--
        <table class="grouping">
            <th><?php echo $form->label($view->field('center'), t('Center')) ?></th>
            <th><?php echo $form->label($view->field('itemsScaleUp'), t('Item Scale Up')) ?></th>
            <tr>
                <td>
                    <input type="radio" name="<?php echo $view->field('center') ?>" value="1" <?php echo $options->center == 1 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('center') ?>" value="0" <?php echo $options->center == 0 ? 'checked' : '' ?>> <?php echo t('No') ?>
                    <small><?php echo t('(Center item. Works well with even an odd number of items.)') ?></small>
                </td>
                <td>
                    <input type="radio" name="<?php echo $view->field('itemsScaleUp') ?>" value="0" <?php echo $options->itemsScaleUp == 0 ? 'checked' : '' ?>> <?php echo t('Yes') ?>
                    <input type="radio" name="<?php echo $view->field('itemsScaleUp') ?>" value="1" <?php echo $options->itemsScaleUp == 1 ? 'checked' : '' ?>> <?php echo t('Not') ?>
                    <small><?php echo t('Stretch items when it is less than the supplied items') ?></small>
                </td>
            </tr>
        </table>
        -->
        <div class="footer">
            <button class="btn btn-primary easy_image_options_close" type="button" id=""><?php echo t('Close') ?></button>
        </div>
    </div>
</div>

