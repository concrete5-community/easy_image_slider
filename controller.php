<?php
namespace Concrete\Package\EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

use Asset;
use AssetList;
use Concrete\Core\Attribute\Type as AttributeType;
use BlockType;
use FileAttributeKey as FileKey;
use Package;
use Route;

use Concrete\Package\EasyImageSlider\Src\Helper\MclInstaller;

class Controller extends Package {

    protected $pkgHandle = 'easy_image_slider';
    protected $appVersionRequired = '5.7.5.2';
    protected $pkgVersion = '1.2';
    protected $pkg;

    public function getPackageDescription() {
        return t("Responsive & Touch enabled Slider & Carousel made Easy");
    }

    public function getPackageName() {
        return t("Easy Image Slider");
    }

    public function on_start() {

        $this->registerRoutes();
        $this->registerAssets();
    }

    public function registerAssets() {
        $al = AssetList::getInstance();
        $al->register( 'javascript', 'knob', 'blocks/easy_image_slider/javascript/build/jquery.knob.js', array('version' => '1.2.11', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'javascript', 'easy-slider-edit', 'blocks/easy_image_slider/javascript/build/block-edit.js', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'css', 'easy-slider-edit', 'blocks/easy_image_slider/stylesheet/block-edit.css', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'css', 'easy-slider-view', 'blocks/easy_image_slider/stylesheet/block-view.css', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );

        $al->register( 'css', 'owl-carousel', 'blocks/easy_image_slider/stylesheet/owl.carousel.css', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'css', 'owl-theme', 'blocks/easy_image_slider/stylesheet/owl.theme.css', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'css', 'animate', 'blocks/easy_image_slider/stylesheet/animate.css', array('version' => '1.0', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'javascript', 'owl-carousel', 'blocks/easy_image_slider/javascript/build/owl.carousel.min.js', array('version' => '1.3.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'javascript', 'imagesloaded', 'blocks/easy_image_slider/javascript/build/imagesloaded.pkgd.min.js', array('version' => '3.1.4', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );

        // View items
        $al->register( 'javascript', 'intense', 'blocks/easy_image_slider/javascript/build/intense.js', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'javascript', 'prettyPhoto', 'blocks/easy_image_slider/javascript/build/jquery.prettyPhoto.js', array('version' => '3.1.5', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );
        $al->register( 'css', 'prettyPhoto', 'blocks/easy_image_slider/stylesheet/prettyPhoto.css', array('version' => '3.1.5', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => true), $this );

    }
    public function registerRoutes() {
        Route::register('/easyimageslider/tools/savefield','\Concrete\Package\EasyImageSlider\Controller\Tools\EasyImageSliderTools::save');
        Route::register('/easyimageslider/tools/getfilesetimages','\Concrete\Package\EasyImageSlider\Controller\Tools\EasyImageSliderTools::getFileSetImage');
        Route::register('/easyimageslider/tools/getfiledetailsjson','\Concrete\Package\EasyImageSlider\Controller\Tools\EasyImageSliderTools::getFileDetailsJson');
    }

    public function install() {

    // Get the package object
        $this->pkg = parent::install();

    // Installing
        $this->installOrUpgrade();

    }


    private function installOrUpgrade() {
        $ci = new MclInstaller($this->pkg);
        $ci->importContentFile($this->getPackagePath() . '/config/install/base/blocktypes.xml');
        $ci->importContentFile($this->getPackagePath() . '/config/install/base/attributes.xml');

    }

    public function upgrade () {
        $this->pkg = $this;
        $this->installOrUpgrade();
        parent::upgrade();
    }


}
