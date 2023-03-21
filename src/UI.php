<?php

namespace EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

class UI
{
    /**
     * @var \EasyImageSlider\UI|null
     */
    private static $instance;

    /**
     * @var int
     * @readonly
     */
    public $majorVersion;

    /**
     * @var string
     * @readonly
     */
    public $faLarge;

    /**
     * @var string
     * @readonly
     */
    public $faSpin;

    /**
     * @var string
     * @readonly
     */
    public $faGear;

    /**
     * @var string
     * @readonly
     */
    public $faGears;

    /**
     * @var string
     * @readonly
     */
    public $faPicture;

    /**
     * @var string
     * @readonly
     */
    public $faBolt;

    /**
     * @var string
     * @readonly
     */
    public $faExternalLink;

    /**
     * @var string
     * @readonly
     */
    public $faLink;

    /**
     * @var string
     * @readonly
     */
    public $faArrows;

    /**
     * @var string
     * @readonly
     */
    public $faArrowsLeftRight;

    /**
     * @var string
     * @readonly
     */
    public $faTimes;

    /**
     * @var string
     * @readonly
     */
    public $faUpload;

    /**
     * @var string
     * @readonly
     */
    public $faList;

    private function __construct()
    {
        $chunks = explode('.', APP_VERSION);
        $this->majorVersion = (int) $chunks[0];
        $this->faLarge = 'fa-lg';
        $this->faSpin = 'fa-spin';
        $this->faGear = $this->majorVersion >= 9 ? 'fas fa-cog' : 'fa fa-cog';
        $this->faGears = $this->majorVersion >= 9 ? 'fas fa-cogs' : 'fa fa-cogs';
        $this->faPicture = $this->majorVersion >= 9 ? 'far fa-image' : 'fa fa-picture-o';
        $this->faBolt = $this->majorVersion >= 9 ? 'fas fa-bolt' : 'fa fa-bolt';
        $this->faExternalLink = $this->majorVersion >= 9 ? 'fas fa-external-link-alt' : 'fa fa-external-link';
        $this->faLink = $this->majorVersion >= 9 ? 'fas fa-link' : 'fa fa-link';
        $this->faArrows = $this->majorVersion >= 9 ? 'fas fa-arrows-alt' : 'fa fa-arrows';
        $this->faArrowsLeftRight = $this->majorVersion >= 9 ? 'fas fa-arrows-alt-h' : 'fa fa-arrows-h';
        $this->faTimes = $this->majorVersion >= 9 ? 'fas fa-times' : 'fa fa-times';
        $this->faUpload = $this->majorVersion >= 9 ? 'fas fa-upload' : 'fa fa-upload';
        $this->faList = $this->majorVersion >= 9 ? 'fas fa-list' : 'fa fa-th-list';
    }

    /**
     * @return \EasyImageSlider\UI
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
