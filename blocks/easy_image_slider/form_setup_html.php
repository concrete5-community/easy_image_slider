<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Block\View\BlockView|Concrete\Core\Page\Type\Composer\Control\BlockControl $this
 * @var Concrete\Core\Block\View\BlockView|Concrete\Core\Page\Type\Composer\Control\BlockControl $view
 * @var Concrete\Package\EasyImageSlider\Block\EasyImageSlider\Controller $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\File\Set\Set[] $fileSets
 * @var EasyImageSlider\FileDetails[] $fDetails
 * @var EasyImageSlider\Options $options
 * @var EasyImageSlider\UI $ui
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManager $urlManager
 * @var bool|null $isComposer (may be unset)
 */

$optionTabs = array(
    array('handle' => 'settings', 'name' => t('Settings'), 'icon' => "{$ui->faGears} {$ui->faLarge}"),
    array('handle' => 'lightbox', 'name' => t('Lightbox'), 'icon' => "{$ui->faPicture} {$ui->faLarge}"),
    array('handle' => 'advanced', 'name' => t('Advanced'), 'icon' => "{$ui->faBolt} {$ui->faLarge}"),
);

?>

<ul class="ccm-inline-toolbar ccm-ui easy-image-toolbar">
    <li class="ccm-sub-toolbar-text-cell">
        <?php
        if (!empty($fileSets)) {
            ?>
            <label for="fsID"><?php echo t('Add a Fileset:') ?></label>
            <select name="fsID" id="fsID" style="width: auto !important">
                <option value="0"><?php echo t('Choose') ?></option>
                <?php
                foreach ($fileSets as $fs) {
                    ?>
                    <option value="<?php echo $fs->getFileSetID() ?>"><?php echo h($fs->getFileSetName()) ?></option>
                    <?php
                }
                ?>
            </select>
            <?php
        } else {
            ?>
            <label for="fsID"><?php echo t('No Fileset') ?></label>
            <?php
        }
        ?>
    </li>
    <?php
    foreach ($optionTabs as $value) {
        ?>
        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-options">
            <button id="<?php echo $value['handle'] ?>-button" type="button" class="btn btn-mini option-button" rel="tab-content-<?php echo $value['handle'] ?>">
                <i class="<?php echo $value['icon'] ?>"></i>
                <?php echo $value['name'] ?>
            </button>
        </li>
        <?php
    }
    ?>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
        <button id="easy_image_cancel" type="button" class="btn btn-mini">
            <?php echo t('Cancel') ?>
        </button>
    </li>
    <?php
    if (empty($isComposer)) {
        ?>
        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
            <button class="btn btn-primary" type="button" id="easy_image_save">
                <?php echo $controller->getTask() === 'add' ? t('Add Gallery') : t('Update Gallery')  ?>
            </button>
        </li>
        <?php
    }
    ?>
 </ul>
<?php
$this->inc('advanced_options.php', array('view' => $view, 'options' => $options, 'form' => $form));
?>
<div class="slides-form-wrapper ccm-ui">
    <div class="easy_slide-items"></div>
</div>
<script type="text/template" id="SlideTemplate">
    <div class="slide-item <% if (image_url.length > 0) { %>filled<% } %> ccm-ui">
        <div id="manage-file" class="manage-file">
            <% if (image_url.length > 0) { %>
                <!-- // An image is loaded -->
                <div class="img" style="background-image:url(<%= image_url %>)"></div>
                <div class="slide-item-toolbar" id="slide-item-toolbar-<%= fID %>">
                    <table>
                        <tr>
                            <td>
                                <h4 data-type="textarea" data-name="fvTitle"  class="title editable editable-click" title="<?php echo t('Title') ?>"><%= title %></h4>
                                <p class="description editable editable-click" data-placeholder="<?php echo t('Write your description') ?>" data-name="fvDescription" data-type="textarea" <% if (!description) { %> editable-empty <% } %>><%= description %></p>
                            </td>
                            <td>
                                <div class="icon-label" title="<?php echo t('Link URL') ?>">
                                    <span><i class="<?php echo $ui->faExternalLink ?>"></i></span>
                                    <p class="editable editable-click" data-placeholder="<?php echo t('http://') ?>" data-name="image_link" data-type="textarea" data-title="<?php echo t('Image link URL') ?>" <% if (!image_link) { %> editable-empty <% } %>><%= image_link %></p>
                                </div>
                                <div class="icon-label" title="<?php echo t('Link Text') ?>">
                                    <span><i class="<?php echo $ui->faLink ?>"></i></span>
                                    <p class="editable editable-click" data-placeholder="<?php echo t('View >') ?>" data-name="image_link_text" data-type="textarea" data-title="<?php echo t('Special Image link Text') ?>" <% if (!image_link_text) { %> editable-empty <% } %>><%= image_link_text %></p>
                                </div>
                                <!--<div class="icon-label" title="<?php echo t('Special width') ?>">
                                    <span><i class="<?php echo $ui->faArrowsLeftRight ?>"></i></span>
                                    <p class="editable editable-click" data-placeholder="<?php echo t('A value in pixels') ?>" data-name="image_thumbnail_width" data-type="text" data-title="<?php echo t('Special width') ?>" <% if (!image_thumbnail_width) { %> editable-empty <% } %>><%= image_thumbnail_width %></p>
                                </div>-->
                            </td>
                        </tr>
                    </table>
                    <a href="javascript:;" class="remove-item"><i class="<?php echo $ui->faTimes ?>"></i></a>
                    <div class="item-controls">
                        <a class="dialog-launch item-properties" dialog-modal="true" dialog-width="600" dialog-height="400" dialog-title="Properties" href="<?php echo h($urlManager->resolve(array('/ccm/system/dialogs/file/properties'))) ?>?fID=<%= fID %>"><i class="<?php echo $ui->faGear ?>"></i></a>
                        <a class="handle"><i class="<?php echo $ui->faArrows ?>"></i></a>
                        <input type="text" name="image_bg_color[]" value="<%= image_bg_color %>" id="ccm-colorpicker-bg-<%= fID %>" />
                    </div>
                </div>
                <input type="hidden" name="<?php echo $view->field('fID') ?>[]" class="image-fID" value="<%=fID%>" />
            <% } else { %>
                <!-- // A empty square -->
                <div class="add-file-control">
                    <a href="#" class="upload-file"><i class="<?php echo $ui->faUpload ?>"></i></a><a href="#" class="add-file"><i class="<?php echo $ui->faList ?>"></i></a>
                </div>
                <span class="process"><?php echo t('Processing') ?> <i class="<?php echo $ui->faGear ?> <?php echo $ui->faSpin ?>"></i></span>
                <input type="text" class="knob" value="0" data-width="150" data-height="150" data-fgColor="#555" data-readOnly="1" data-bgColor="#e1e1e1" data-thickness=".1" />
                <input type="file" name="files[]" class="browse-file" multiple />
            <% } %>
        </div>
    </div>
</script>
<script>
(function() {
window.CCM_EDITOR_SECURITY_TOKEN = <?php echo json_encode($token->generate('editor')) ?>;

var manager = new EasySlideManager(
    $('.easy_slide-items'),
    <?php echo json_encode(array(
        'getFileDetailDetailUrl' => (string) $urlManager->resolve(array('/easyimageslider/tools/getfiledetails')),
        'saveFieldURL' => (string) $urlManager->resolve(array('/easyimageslider/tools/savefield')),
        'getFilesetImagesURL' => (string) $urlManager->resolve(array('/easyimageslider/tools/getfilesetimages')),
        'i18n' => array(
            'filesetAlreadyPicked' => t('This Fileset have been already picked, are you sure to add images again ?'),
            'confirmDeleteImage' => t('Are you sure to delete this image?'),
            'image' => t('Image'),
            'imageOnly' => t('You must select an image file only'),
            'imageSize' => t('Please upload a smaller image, max size is 6 MB'),
            'none' => t('None'),
            'error' => t('Error'),
        ),
    )) ?>
);
<?php
if (!empty($fDetails)) {
    ?>
    $(document).ready(function() {
        <?php
        foreach ($fDetails as $f) {
            ?>
            manager.fillSlideTemplate(<?php echo json_encode($f) ?>);
            <?php
        }
        ?>
    });
    <?php
}
?>

})();
</script>
