<?php

namespace Concrete\Package\EasyImageSlider\Block\EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\File\File;
use Concrete\Core\File\Set\SetList as FileSetList;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Xml;
use EasyImageSlider\FileDetails;
use EasyImageSlider\Options;
use EasyImageSlider\Tools;
use EasyImageSlider\UI;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use SimpleXMLElement;

class Controller extends BlockController implements FileTrackableInterface
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
        unset($args['fID']);
        $options = isset($args['options']) && $args['options'] instanceof Options ? $args['options'] : Options::fromUI($args);
        parent::save([
            'options' => json_encode($options),
            'fIDs' => $fIDs,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedFiles()
    {
        $files = $this->getFiles();
        $result = [];
        foreach ($files as $file) {
            $result[$file->getFileID()] = $file;
        }
        $inspector = $this->app->make('import/value_inspector');
        foreach ($files as $file) {
            $imageLink = $this->formatLinkForExport((string) $file->getAttribute('image_link'));
            foreach ($inspector->inspect($imageLink)->getMatchedItems() as $item) {
                if (!$item instanceof FileItem) {
                    continue;
                }
                $file = $item->getContentObject();
                if (!$file || $file->isError()) {
                    continue;
                }
                $result[$file->getFileID()] = $file;
                
            }
        }

        return array_values($result);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(SimpleXMLElement $blockNode)
    {
        $xml = $this->app->make(Xml::class);
        $data = $blockNode->addChild('data');
        $data->addAttribute('table', $this->getBlockTypeDatabaseTable());
        $record = $data->addChild('record');
        $files = $this->getFilesDetails($this->getFilesIds());
        $exportedFiles = array_map(
            static function (FileDetails $details) {
                return ContentExporter::replaceFileWithPlaceHolder($details->fID);
            },
            $files
        );
        $options = $this->getOptions()->export();
        if (method_exists($xml, 'createChildElement')) {
            foreach ($exportedFiles as $index => $exportedFile) {
                $xFile = $xml->createChildElement($record, 'file', $exportedFile);
                $this->exportFileDetails($xFile, $files[$index]);
            }
            $xml->createChildElement($record, 'options', $options);
        } else {
            foreach ($exportedFiles as $index => $exportedFile) {
                $xFile = $xml->createCDataNode($record, 'file', $exportedFile);
                $this->exportFileDetails($xFile, $files[$index]);
            }
            $xml->createCDataNode($record, 'options', $options);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $args = [];
        if (!isset($blockNode->data)) {
            return $args;
        }
        $inspector = $this->app->make('import/value_inspector');
        foreach ($blockNode->data as $xData) {
            if (!isset($xData['table']) || $this->getBlockTypeDatabaseTable() !== (string) $xData['table']) {
                continue;
            }
            if (!isset($xData->record)) {
                continue;
            }
            $xRecord = $xData->record;
            $fIDs = [];
            if (isset($xRecord->file)) {
                foreach ($xRecord->file as $xFile) {
                    foreach ($inspector->inspect((string) $xFile)->getMatchedItems() as $item) {
                        if (!$item instanceof FileItem) {
                            continue;
                        }
                        $file = $item->getContentObject();
                        if (!$file || $file->isError()) {
                            continue;
                        }
                        $this->importFileDetails($xFile, $file);
                        $fIDs[] = $file->getFileID();
                    }
                }
            }
            $args['fID'] = $fIDs;
            if (isset($xRecord->options)) {
                $args['options'] = Options::import((string) $xRecord->options);
            }
        }

        return $args;
    }

    /**
     * @param \Concrete\Core\File\File|\Concrete\Core\Entity\File\File $file
     */
    private function importFileDetails(SimpleXmlElement $xFile, $file)
    {
        $title = isset($xFile['title']) ? (string) $xFile['title'] : '';
        if ($title !== '' && $title !== $file->getTitle()) {
            $file->updateTitle($title);
        }
        $description = isset($xFile['description']) ? (string) $xFile['description'] : '';
        if ($description !== '' && $description !== $file->getDescription()) {
            $file->updateDescription($description);
        }
        $rawImageLink = isset($xFile['image_link']) ? (string) $xFile['image_link'] : '';
        if ($rawImageLink !== '') {
            $imageLink = LinkAbstractor::import($rawImageLink);
            if ($imageLink !== (string) $file->getAttribute('image_link')) {
                $file->setAttribute('image_link', $imageLink);
            }
        }
        $imageLinkText = isset($xFile['image_link_text']) ? (string) $xFile['image_link_text'] : '';
        if ($imageLinkText !== '' && $imageLinkText !== (string) $file->getAttribute('image_link_text')) {
            $file->setAttribute('image_link_text', $imageLinkText);
        }
        $imageBgColor = isset($xFile['image_bg_color']) ? (string) $xFile['image_bg_color'] : '';
        if ($imageBgColor !== '' && $imageBgColor !== (string) $file->getAttribute('image_bg_color')) {
            $file->setAttribute('image_bg_color', $imageBgColor);
        }
    }

    private function exportFileDetails(SimpleXmlElement $xFile, FileDetails $details)
    {
        if ((string) $details->title !== '') {
            $xFile['title'] = $details->title;
        }
        if ((string) $details->description !== '') {
            $xFile['description'] = $details->description;
        }
        if (($imageLink = $this->formatLinkForExport((string) $details->image_link)) !== '') {
            $xFile['image_link'] = $imageLink;
        }
        if ((string) $details->image_link_text !== '') {
            $xFile['image_link_text'] = $details->image_link_text;
        }
        if ((string) $details->image_bg_color !== '') {
            $xFile['image_bg_color'] = $details->image_bg_color;
        }
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function formatLinkForExport($str)
    {
        if ($str === '') {
            return '';
        }
        $resolver = $this->app->make(ResolverManagerInterface::class);
        if (filter_var($str, FILTER_VALIDATE_URL)) {
            $url = $str;
        } elseif ($str[0] === '/') {
            $str = preg_replace('{^/index\.php/?\b}', '/', $str);
            try {
                $str = (string) $resolver->resolve([$str]);
            } catch (\Exception $x) {
                return $str;
            } catch (\Throwable $x) {
                return $str;
            }
            if (!filter_var($str, FILTER_VALIDATE_URL)) {
                return $str;
            }
            $url = $str;
        } else {
            return $str;
        }
        $homeUrl = '';
        try {
            $homeUrl = (string) $resolver->resolve(['/']);
        } catch (\Exception $x) {
        } catch (\Throwable $x) {
        }
        $homeUrl = preg_replace('{/index\.php$}i', '/', $homeUrl);
        if ($homeUrl === '' || !filter_var($str, FILTER_VALIDATE_URL)) {
            return $url;
        }
        $homeUrl = rtrim($homeUrl, '/');
        if ($homeUrl === '' || stripos($url, $homeUrl) !== 0) {
            return $url;
        }
        $path = preg_replace('{[\?#].*$}', '', substr($url, strlen($homeUrl)));
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . $path;
        }
        $path = preg_replace('{^\/index\.php\b\/?}', '/', $path);
        $page = $path === '/' ? Page::getHomePageID(): Page::getByPath($path);
        if ($page && !$page->isError()) {
            return ContentExporter::replacePageWithPlaceHolder($page->getCollectionID());
        }
        $match = null;
        if (preg_match('{^/download_file(/view|/force|/view_inline)/?(?<fid>[^/\?#]+)}', $path, $match)) {
            $file = method_exists('File', 'getByUUIDOrID') ? File::getByUUIDOrID($match['fid']) : File::getByID($match['fid']);
            if ($file && !$file->isError()) {
                return ContentExporter::replaceFileWithPlaceHolder($file->getFileID());
            }
        }

        return $url;
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
            if (!$w) {
                continue;
            }
            $h = $f->getAttribute('height');
            if (!$h) {
                continue;
            }
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
