<?php

namespace Concrete\Package\EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;
use Route;

class Controller extends Package implements ProviderInterface
{
    protected $pkgHandle = 'easy_image_slider';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::$appVersionRequired
     */
    protected $appVersionRequired = '8.5.2';

    protected $pkgVersion = '1.6.1';

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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Database\EntityManager\Provider\ProviderInterface::getDrivers()
     */
    public function getDrivers()
    {
        return [];
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

    private function registerRoutes()
    {
        Route::register('/easyimageslider/tools/savefield', 'EasyImageSlider\Tools::save');
        Route::register('/easyimageslider/tools/getfilesetimages', 'EasyImageSlider\Tools::getFileSetImages');
        Route::register('/easyimageslider/tools/getfiledetails', 'EasyImageSlider\Tools::getFileDetails');
    }

    private function registerAssets()
    {
        $al = AssetList::getInstance();
        $al->register('javascript', 'knob', 'js/jquery.knob.js', array('version' => '1.2.12', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => true), $this);
        $al->register('javascript', 'easy-slider-edit', 'js/block-edit.js', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => true), $this);
        $al->register('css', 'easy-slider-edit', 'css/block-edit.css', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('css', 'easy-slider-view', 'css/block-view.css', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);

        $al->register('css', 'owl-carousel', 'css/owl.carousel.css', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('css', 'owl-theme', 'css/owl.theme.css', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
        $al->register('css', 'animate', 'css/animate.css', array('version' => '1.0', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => true), $this);
        $al->register('javascript', 'owl-carousel', 'js/owl.carousel.js', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => true), $this);
        $al->register('javascript', 'imagesloaded', 'js/imagesloaded.js', array('version' => '5.0.0', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => true), $this);

        // View items
        $al->register('javascript', 'intense', 'js/intense.js', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => true), $this);
        $al->register('javascript', 'prettyPhoto', 'js/jquery.prettyPhoto.js', array('version' => '3.1.6', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => true), $this);
        $al->register('css', 'prettyPhoto', 'css/prettyPhoto.css', array('version' => '3.1.6', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this);
    }
}
