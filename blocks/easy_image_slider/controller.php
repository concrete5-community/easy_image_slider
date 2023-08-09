<?php

namespace Concrete\Package\EasyImageSlider\Block\EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\File\File;
use Concrete\Core\File\Set\SetList as FileSetList;
use Concrete\Core\Support\Facade\Application;
use EasyImageSlider\Options;
use EasyImageSlider\Tools;
use EasyImageSlider\UI;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;

class Controller extends BlockController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btTable
     */
    protected $btTable = 'btEasyImageSlider';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btInterfaceWidth
     */
    protected $btInterfaceWidth = 600;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btInterfaceHeight
     */
    protected $btInterfaceHeight = 465;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btWrapperClass
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btCacheBlockRecord
     */
    protected $btCacheBlockRecord = false;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btExportFileColumns
     */
    protected $btExportFileColumns = array('fID');

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btCacheBlockOutput
     */
    protected $btCacheBlockOutput = false;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btCacheBlockOutputOnPost
     */
    protected $btCacheBlockOutputOnPost = false;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btCacheBlockOutputForRegisteredUsers
     */
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btSupportsInlineEdit
     */
    protected $btSupportsInlineEdit = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btSupportsInlineAdd
     */
    protected $btSupportsInlineAdd = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btDefaultSet
     */
    protected $btDefaultSet = 'multimedia';

    /**
     * @var string|null
     */
    protected $fIDs;

    /**
     * @var string|null
     */
    protected $options;

    /**
     * @var array|null
     */
    private $decodedOptions;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getBlockTypeDescription()
     */
    public function getBlockTypeDescription()
    {
        return t('A OWL Carousel made easy for concrete5');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getBlockTypeName()
     */
    public function getBlockTypeName()
    {
        return t('Easy Images Slider');
    }

    public function add()
    {
        $this->configureEdit();
        $this->set('fDetails', array());
    }

    public function edit()
    {
        $this->configureEdit();
        $this->set('fDetails', $this->getFilesDetails($this->getFilesIds()));
    }

    public function composer()
    {
        $this->configureEdit();
        $this->set('fDetails', $this->getFilesDetails($this->getFilesIds()));
        $this->addHeaderItem(
            <<<'EOT'
?>
<style>
.ccm-inline-toolbar.ccm-ui.easy-image-toolbar {
    opacity: 1;
}
.easy-image-toolbar .ccm-inline-toolbar-button-save, .easy-image-toolbar .ccm-inline-toolbar-button-cancel {
    display: none;
}
</style>
EOT
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::registerViewAssets()
     */
    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('css', 'easy-slider-view');
        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('css', 'font-awesome');
        $this->requireAsset('javascript', 'owl-carousel');
        $this->requireAsset('css', 'owl-theme');
        $this->requireAsset('css', 'owl-carousel');
        // $this->requireAsset('css','animate');
        $options = $this->getOptions();
        switch ($options->lightbox) {
            case 'lightbox':
                $this->requireAsset('javascript', 'prettyPhoto');
                $this->requireAsset('css', 'prettyPhoto');
                break;
            case 'intense':
                $this->requireAsset('javascript', 'intense');
                break;
        }
    }

    public function view()
    {
        $files = $this->getFiles();
        $this->set('files', $files);
        $this->set('options', $this->getOptions());
        $this->generatePlaceHolderFromArray($files);
    }

    public function save($args)
    {
        if (!is_array($args)) {
            $args = array();
        }
        $fIDs = empty($args['fID']) || !is_array($args['fID']) ? '' : implode(',', $args['fID']);
        if ($fIDs !== '') {
            $this->generatePlaceHolderFromArray($args['fID']);
        }
        parent::save(array(
            'options' => json_encode(Options::fromUI($args)),
            'fIDs' => $fIDs,
        ));
    }

    /**
     * @param int[]|\Concrete\Core\File\File[] $array
     */
    private function generatePlaceHolderFromArray($array)
    {
        $placeholderMaxSize = 600;
        if (empty($array)) {
            $files = array();
        } elseif (!is_object($array[0])) {
            $files = array_values(array_filter(array_map(array($this, 'getFileFromFileID'), $array)));
        } else {
            $files = $array;
        }
        if ($files === array()) {
            return;
        }
        $placeholderDir = __DIR__ . '/images/placeholders';
        if (!is_dir($placeholderDir)) {
            mkdir($placeholderDir, 0755);
        }
        $imagine = new Imagine();
        $rgb = new RGB();
        $backgroundColor = $rgb->color(array(0x00, 0x00, 0x00), 13);
        foreach ($files as $f) {
            if (!is_object($f)) {
                continue;
            }
            $w = $f->getAttribute('width');
            $h = $f->getAttribute('height');
            $placeholderFile = $placeholderDir . "/placeholder-{$w}-{$h}.png";
            if (file_exists($placeholderFile)) {
                continue;
            }
            $new_width = $placeholderMaxSize;
            $new_height = floor($h * ($placeholderMaxSize / $w));
            $image = $imagine->create(new Box($new_width, $new_height), $backgroundColor);
            $image->save($placeholderFile, array('format' => 'PNG'));
        }
    }

    /**
     * @return \Concrete\Core\File\Set\Set[]
     */
    private function getFileSetList()
    {
        $fs = new FileSetList();

        return $fs->get();
    }

    /**
     * @return \EasyImageSlider\Options
     */
    private function getOptions()
    {
        if ($this->decodedOptions === null || $this->decodedOptions[0] !== $this->options) {
            $this->decodedOptions = array(
                $this->options,
                Options::fromJSON($this->options),
            );
        }

        return $this->decodedOptions[1];
    }

    /**
     * @param int[] $fIDs
     *
     * @return \EasyImageSlider\FileDetails[]
     */
    private function getFilesDetails($fIDs)
    {
        $tools = new Tools();
        $fDetails = array();
        foreach ($fIDs as $fID) {
            $f = $this->getFileFromFileID($fID);
            if ($f !== null) {
                $fDetails[] = $tools->buildFileDetails($f);
            }
        }

        return $fDetails;
    }

    /**
     * @param int|mixed $fID
     *
     * @return \Concrete\Core\File\File|null
     */
    private function getFileFromFileID($fID)
    {
        return $fID ? File::getByID($fID) : null;
    }

    private function configureEdit()
    {
        $app = Application::getFacadeApplication();
        $ui = UI::getInstance();
        $this->setAssetEdit($ui);
        $this->set('fileSets', $this->getFileSetList());
        $this->set('options', $this->getOptions());
        $this->set('token', $app->make('token'));
        $this->set('urlManager', $app->make('url/manager'));
        $this->set('ui', $ui);
    }

    private function setAssetEdit(UI $ui)
    {
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
        if ($ui->majorVersion >= 9) {
            $assetList = AssetList::getInstance();
            if (!$assetList->getAsset('javascript', 'bootstrap-editable')) {
                $assetList->register('javascript', 'bootstrap-editable', 'js/bootstrap-editable.js', array('version' => '1.5.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => true), 'easy_image_slider');
            }
            $this->requireAsset('javascript', 'bootstrap-editable');
            if (!$assetList->getAsset('css', 'bootstrap-editable')) {
                $assetList->register('css', 'bootstrap-editable', 'css/bootstrap-editable.css', array('version' => '1.5.3', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => true), 'easy_image_slider');
            }
            $this->requireAsset('css', 'bootstrap-editable');
        } else {
            $this->requireAsset('core/app/editable-fields');
        }
        $this->requireAsset('javascript', 'knob');
        $this->requireAsset('javascript', 'easy-slider-edit');
        $this->requireAsset('css', 'easy-slider-edit');
    }

    /**
     * @return int[]
     */
    private function getFilesIds()
    {
        return array_values( // Reset array indexes
            array_filter( // Remove zeroes
                array_map('intval', explode(',', (string) $this->fIDs))
            )
        );
    }

    /**
     * @return \Concrete\Core\File\File[]
     */
    private function getFiles()
    {
        $files = array();
        foreach ($this->getFilesIds() as $fID) {
            $file = $this->getFileFromFileID($fID);
            if ($file !== null) {
                $files[] = $file;
            }
        }

        return $files;
    }
}
