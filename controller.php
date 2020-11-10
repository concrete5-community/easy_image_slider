<?php

namespace Concrete\Package\EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

use Asset;
use AssetList;
use Concrete\Core\Backup\ContentImporter;
use Package;
use Route;

class Controller extends Package
{
    protected $pkgHandle = 'easy_image_slider';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::$appVersionRequired
     */
    protected $appVersionRequired = '5.7.5.2';

    protected $pkgVersion = '1.2';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::$pkgAutoloaderRegistries
     */
    protected $pkgAutoloaderRegistries = array(
        'src' => 'EasyImageSlider',
    );

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageDescription()
     */
    public function getPackageDescription()
    {
        return t('Responsive & Touch enabled Slider & Carousel made Easy');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageName()
     */
    public function getPackageName()
    {
        return t('Easy Image Slider');
    }

    public function on_start()
    {
        $this->registerRoutes();
        $this->registerAssets();
    }

    public function registerAssets()
    {
        $al = AssetList::getInstance();
        $al->register('javascript', 'knob', 'blocks/easy_image_slider/javascript/build/jquery.knob.js', array('version' => '1.2.11', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('javascript', 'easy-slider-edit', 'blocks/easy_image_slider/javascript/build/block-edit.js', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('css', 'easy-slider-edit', 'blocks/easy_image_slider/stylesheet/block-edit.css', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('css', 'easy-slider-view', 'blocks/easy_image_slider/stylesheet/block-view.css', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);

        $al->register('css', 'owl-carousel', 'blocks/easy_image_slider/stylesheet/owl.carousel.css', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('css', 'owl-theme', 'blocks/easy_image_slider/stylesheet/owl.theme.css', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('css', 'animate', 'blocks/easy_image_slider/stylesheet/animate.css', array('version' => '1.0', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('javascript', 'owl-carousel', 'blocks/easy_image_slider/javascript/build/owl.carousel.min.js', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('javascript', 'imagesloaded', 'blocks/easy_image_slider/javascript/build/imagesloaded.pkgd.min.js', array('version' => '3.1.4', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);

        // View items
        $al->register('javascript', 'intense', 'blocks/easy_image_slider/javascript/build/intense.js', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('javascript', 'prettyPhoto', 'blocks/easy_image_slider/javascript/build/jquery.prettyPhoto.js', array('version' => '3.1.5', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('css', 'prettyPhoto', 'blocks/easy_image_slider/stylesheet/prettyPhoto.css', array('version' => '3.1.5', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
    }

    public function registerRoutes()
    {
        Route::register('/easyimageslider/tools/savefield', 'EasyImageSlider\Tools::save');
        Route::register('/easyimageslider/tools/getfilesetimages', 'EasyImageSlider\Tools::getFileSetImage');
        Route::register('/easyimageslider/tools/getfiledetailsjson', 'EasyImageSlider\Tools::getFileDetailsJson');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::install()
     */
    public function install()
    {
        parent::install();
        $this->installOrUpgrade();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::upgrade()
     */
    public function upgrade()
    {
        $this->installOrUpgrade();
        parent::upgrade();
    }

    private function installOrUpgrade()
    {
        if (method_exists($this, 'installContentFile')) {
            $this->installContentFile('config/install.xml');
        } else {
            $ci = new ContentImporter();
            $ci->importContentFile($this->getPackagePath() . '/config/install.xml');
        }
    }
}
