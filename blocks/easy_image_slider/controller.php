<?php  
namespace Concrete\Package\EasyImageSlider\Block\EasyImageSlider;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Block\BlockController;
use Loader;
// use Asset;
// use AssetList;
// use \Concrete\Core\Http\ResponseAssetGroup;
// use Permissions;

use File;
use FileSet;
use StdClass;
use \Concrete\Core\File\Set\SetList as FileSetList;
use FileAttributeKey;

use Concrete\Package\EasyImageSlider\Controller\Tools\EasyImageSliderTools;


class Controller extends BlockController
{
    protected $btTable = 'btEasyImageSlider';
    protected $btInterfaceWidth = "600";
    protected $btWrapperClass = 'ccm-ui';
    protected $btInterfaceHeight = "465";
    protected $btCacheBlockRecord = false;
    protected $btExportFileColumns = array('fID');
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btSupportsInlineEdit = true;
    protected $btSupportsInlineAdd = true;
    protected $btDefaultSet = 'multimedia';

    protected $optionTabs = array();

    public function getBlockTypeDescription()
    {
        return t("A OWL Carousel made easy for concrete5");
    }

    public function getBlockTypeName()
    {
        return t("Easy Images Slider");
    }


    public function add() {
        $this->setAssetEdit();

        $this->set('fileSets', $this->getFileSetList());
        $this->set('options', $this->getOptionsJson());        
    }

    public function edit()
    {
        $this->setAssetEdit();

        $fIDs =  $this->getFilesIds();

        $this->set('fileSets', $this->getFileSetList());
        $this->set('options', $this->getOptionsJson());
        $this->set('fIDs', $fIDs);
        $this->set('fDetails',$this->getFilesDetails($fIDs));
    }

    function getFilesIds () { return explode(',', $this->fIDs); }
    
    function getOptionsJson ()  { 
        // Cette fonction retourne un objet option
        // SI le block n'existe pas encore, ces options sont préréglées
        // Si il existe on transfome la chaine de charactère en json
        if ($this->isValueEmpty()) :
            $options = new StdClass();
            $options->lightbox = 0;
            $options->items = 4;
            $options->ItemsTitle = 1;
            $options->ItemsDescription = 0;
            $options->lightboxTitle = 1;
            $options->lightboxDescription = 0;
            $options->fancyOverlay = '#f0f0f0';
            $options->infoBg = '#0C99D5';
            $options->fadingColor = '';
            $options->fancyOverlayAlpha = .9;
            $options->imageLink = 0;
            $options->navigation = 1;
            $options->navigationPrev = '<i class="fa fa-arrow-circle-left"></i> ' . t('Prev');
            $options->navigationNext = t('Next') . ' <i class="fa fa-arrow-circle-right"></i>';
            $options->slideSpeed = 300;
            $options->autoPlay = 0;
            $options->autoPlayTime = 5000;
            $options->itemsScaleUp = 0;
            $options->navRewind = 1;
            $options->margin = 5;
            $options->isTransparent = 0;
            $options->responsiveContainer = 0;
            $options->loop = 0;
            $options->center = 0;
            $options->dots = 1;
            $options->nav = 0;
            $options->lazy = 0;
            return $options;
        else:
            $options = json_decode($this->options);
            $options->isSingleItemSlide = $options->items == 1;
            // legacy
            // if(!isset($options->fancyOverlay)) $options->fancyOverlay = '#f0f0f0';
            // end legacy
            return $options ; 
        endif;

    }

    function getFilesDetails ($fIDs) {
        $tools = new EasyImageSliderTools();        
        $fDetails = array();
        foreach ($fIDs as $key => $fID) {
            $f = File::getByID($fID);
            if (is_object($f)) $fDetails[] = $tools->getFileDetails($f);            
        }
        return $fDetails;
    }

    function getFileFromFileID ($fID) {
        if ($fID) return File::getByID($fID);    
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('css','easy-slider-view');        
        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('css', 'font-awesome');        

        $this->requireAsset('javascript', 'owl-carousel');
        $this->requireAsset('css','owl-theme');        
        $this->requireAsset('css','owl-carousel');        
//      $this->requireAsset('css','animate');
    }

    public function view() {
        $time_start = microtime(true);         
        $options =  $this->getOptionsJson();

        // Files
        $files = array_map(array($this,'getFileFromFileID') , explode(',', $this->fIDs));
        $this->set('fIDs', explode(',', $this->fIDs));
        $this->set('files',$files );
        $this->set('options', $options );

        $this->generatePlaceHolderFromArray($files);

        // Lightbox
        if($options->lightbox == 'lightbox'):
            $this->requireAsset('javascript', 'prettyPhoto');
            $this->requireAsset('css', 'prettyPhoto');
            
        elseif($options->lightbox == 'intense'):
            $this->requireAsset('javascript', 'intense');
        endif;

    }


    public function getFileSetList () {
        $fs = new FileSetList();
        return $fs->get();
    }

    public function composer() {
        $this->setAssetEdit();
    }

    public function isValueEmpty() {
        if ($this->fIDs)
            return false;
        else 
            return true;
    }

    public function setAssetEdit () {

        $this->requireAsset('core/file-manager');
        $this->requireAsset('css', 'core/file-manager');
        $this->requireAsset('css', 'jquery/ui');

        $this->requireAsset('javascript', 'bootstrap/dropdown');
        $this->requireAsset('javascript', 'bootstrap/tooltip');
        $this->requireAsset('javascript', 'bootstrap/popover');
        $this->requireAsset('javascript', 'jquery/ui');
        $this->requireAsset('javascript', 'core/events');
        $this->requireAsset('javascript', 'underscore');
        $this->requireAsset('javascript', 'core/app');
        $this->requireAsset('javascript', 'bootstrap-editable');
        $this->requireAsset('css', 'core/app/editable-fields');

        $this->requireAsset('javascript','knob');
        $this->requireAsset('javascript','easy-slider-edit');
        $this->requireAsset('css','easy-slider-edit');
        $this->set('optionTabs', $this->getOptionTabs);
    }

    public function getOptionTabs () {
        return array( 
            array('handle' => 'settings', 'name' => t('Settings'), 'icon' => 'fa-cogs'),
            array('handle' => 'lightbox', 'name' => t('Lightbox'), 'icon' => 'fa-picture-o'),
            array('handle' => 'advanced', 'name' => t('Advanced'), 'icon' => 'fa-flash'),
        );         
    }

    public function save($args)
    {
        $options = $args;
        unset($options['fID']);
        // Numeric checking
        if (!is_numeric($options['fancyOverlayAlpha']) || $options['fancyOverlayAlpha'] > 1 || $options['fancyOverlayAlpha'] < 0) $options['fancyOverlayAlpha'] = .9;
        if (!is_numeric($options['slideSpeed']) || $options['slideSpeed'] > 3000 || $options['slideSpeed'] < 0) $options['slideSpeed'] = 200;
        if (!is_numeric($options['autoPlayTime']) || $options['autoPlayTime'] > 50000 || $options['autoPlayTime'] < 0) $options['autoPlayTime'] = 5000;
        if (!is_numeric($options['items']) || $options['items'] > 20 || $options['items'] < 0) $options['items'] = 4;
        if (!is_numeric($options['margin']) || $options['margin'] > 20 || $options['margin'] < 0) $options['margin'] = 5;
        // Text inputs too long
        // if (count_chars($options['navigationPrev']) > 20 ) $options['navigationPrev'] = t('Prev');
        // if (count_chars($options['navigationNext']) > 20 ) $options['navigationNext'] = t('Next');
        // Checkboxes
        $options['nav'] = isset($args['nav']) ? 1 : 0;
        $options['autoPlay'] = isset($args['autoPlay']) ? 1 : 0;

        // Encoding
        $args['options'] = json_encode($options);
        
        if(is_array($args['fID'])) : 
            $args['fIDs'] = implode(',', $args['fID']);
            $this->generatePlaceHolderFromArray ($args['fID']);
        endif;
        
        parent::save($args);
    }

    function hex2rgb($hex) {
       $hex = str_replace("#", "", $hex);

       if(strlen($hex) == 3) {
          $r = hexdec(substr($hex,0,1).substr($hex,0,1));
          $g = hexdec(substr($hex,1,1).substr($hex,1,1));
          $b = hexdec(substr($hex,2,1).substr($hex,2,1));
       } else {
          $r = hexdec(substr($hex,0,2));
          $g = hexdec(substr($hex,2,2));
          $b = hexdec(substr($hex,4,2));
       }
       $rgb = array($r, $g, $b);
       return implode(",", $rgb); // returns the rgb values separated by commas
       // return $rgb; // returns an array with the rgb values
    } 

    function generatePlaceHolderFromArray ($array) {

        $placeholderMaxSize = 600;

        if (!is_object($array[0])) :
            $files = array_map(array($this,'getFileFromFileID') ,$array);
        else :
            $files = $array;
        endif; 

        foreach ($files as $key => $f) :
            if(!is_object($f)) continue;
            $w = $f->getAttribute('width');
            $h = $f->getAttribute('height');
            $new_width = $placeholderMaxSize;
            $new_height = floor( $h * ( $placeholderMaxSize / $w ) );

            $placeholderFile =  __DIR__ . "/images/placeholders/placeholder-$w-$h.png";
            if (file_exists($placeholderFile)) continue;
            $img = imagecreatetruecolor($new_width,$new_height); 
            imagesavealpha($img, true); 

            // Fill the image with transparent color 
            $color = imagecolorallocatealpha($img,0x00,0x00,0x00,110); 
            imagefill($img, 0, 0, $color); 

            // Save the image to file.png 
            imagepng($img,$placeholderFile); 

            // Destroy image 
            imagedestroy($img);
        endforeach;
    }
}