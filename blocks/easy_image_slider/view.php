<?php

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Block\View\BlockView|Concrete\Core\Page\Type\Composer\Control\BlockControl $this
 * @var int|string $bID
 * @var Concrete\Core\File\File[] $files
 * @var stdClass $options
 */

$c = Page::getCurrentPage();
$galleryHasImage = false;
if ($c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item">
        <div style="padding: 40px 0px 40px 0px"><?php echo t('Easy Gallery disabled in edit mode.') ?></div>
    </div>
    <?php
} elseif (!empty($files)) {
    $type = Type::getByHandle('file_manager_detail');
    $firstFile = $files[0];
    $firstWrapperBg = $options->isTransparent ? ($firstFile->getAttribute('image_bg_color') ? $firstFile->getAttribute('image_bg_color') : $options->fadingColor) : $options->fadingColor;
    ?>
    <div class="easy-slider easy-slider-carousel <?php echo $options->isSingleItemSlide ? 'easy-slider-single' : '' ?>" id="easy-slider-wrapper-<?php echo $bID ?>" style="background-color:<?php echo $firstWrapperBg ?>" data-colorbg="<?php echo $options->fadingColor ?>">
        <div class="easy-slider-carousel-inner <?php if($options->responsiveContainer) { ?>responsive-container <?php } ?>" id="easy-slider-<?php echo $bID ?>">
            <?php
            foreach ($files as $key => $f) {
                $galleryHasImage = true;
                // Different Thumbnails sizes
                $thumbnailUrl = $options->isSingleItemSlide ? $f->getRelativePath() : $f->getThumbnailURL($type->getBaseVersion());
                // Due to a bug in OWL2 Lazy work only with loop activated.
                $placeHolderUrl = $options->lazy ? ($this->getBlockURL() . "/images/placeholders/placeholder-{$f->getAttribute('width')}-{$f->getAttribute('height')}.png") : $thumbnailUrl;
                $retinaThumbnailUrl = $options->isSingleItemSlide ? $f->getRelativePath() : $f->getThumbnailURL($type->getDoubledVersion());
                // Styles for color on hover
                $thumbnailBackground = $options->isTransparent ? 'background-color:transparent' : ('background-color:' . ($f->getAttribute('image_bg_color') ? $f->getAttribute('image_bg_color') : $options->fadingColor) . ';');
                $thumbnailInfoBackground = 'background-color:' . ($f->getAttribute('image_bg_color') ? $f->getAttribute('image_bg_color') : $options->infoBg) . ';';
                // Full image infos
                $fullUrl = $f->getRelativePath();
                // Images Links, title and description
                $linkUrl = $f->getAttribute('image_link');
                $linkUrlText = $f->getAttribute('image_link_text');
                $displayInfos = $options->ItemsTitle || $options->ItemsDescription || $linkUrl && $linkUrlText;
                ?>
                <div class="item" id="item-<?php echo $key ?>" style="<?php echo $thumbnailBackground ?>" <?php if($f->getAttribute('image_bg_color')) { ?>data-color="<?php echo $f->getAttribute('image_bg_color') ?><?php } ?>" >
                    <?php
                    if($options->lightbox && !$linkUrl) {
                        ?>
                        <a href="<?php echo $fullUrl ?>" data-image="<?php echo $fullUrl ?>" rel="prettyPhoto[pp_gal_<?php echo $bID ?>]" <?php if($options->lightboxTitle) { ?> title="<b><?php echo $f->getTitle() ?></b><?php if($options->lightboxDescription) { ?><br /><?php echo $f->getDescription() ?><?php } ?>"<?php } ?> >
                        <?php
                    }
                    ?>
                    <img src="<?php echo $placeHolderUrl ?>" data-src="<?php echo $retinaThumbnailUrl ?>" alt="<?php echo $f->getTitle() ?>" <?php if ($options->lazy) { ?>class="lazyOwl" <?php } ?> />
                    <?php
                    if ($displayInfos) {
                        ?>
                        <div class="info-wrap">
                            <div class="info">
                                <div style="<?php echo $thumbnailInfoBackground ?>">
                                    <?php
                                    if($options->ItemsTitle) {
                                        ?>
                                        <p class="title"><?php echo $f->getTitle() ?></p>
                                        <?php
                                    }
                                    if($options->ItemsDescription) {
                                        ?>
                                        <p class="description"><small><?php echo $f->getDescription() ?></small></p>
                                        <?php
                                    }
                                    if($linkUrl && $linkUrlText) {
                                        ?>
                                        <p class="link"><a href="<?php echo $linkUrl ?>"><?php echo $linkUrlText ?></a></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    if($options->lightbox && !$linkUrl) {
                        ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div><!-- .easy-slider -->
    <div class="owl-nav owl-controls" id="owl-navigation-<?php echo $bID ?>"></div>
    <?php
    if ($galleryHasImage) {
        $this->inc('elements/javascript.php');
        $this->inc('elements/css.php');
    }
}
