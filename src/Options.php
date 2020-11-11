<?php

namespace EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

class Options
{
    /**
     * Allowed values: '', 'lightbox', 'intense'.
     *
     * @var string
     */
    public $lightbox = '';

    /**
     * @var string
     */
    public $animateIn;

    /**
     * @var string
     */
    public $animateOut;

    /**
     * @var int
     */
    public $items = 4;

    /**
     * @var bool
     */
    public $ItemsTitle = true;

    /**
     * @var bool
     */
    public $ItemsDescription = false;

    /**
     * @var bool
     */
    public $lightboxTitle = true;

    /**
     * @var bool
     */
    public $lightboxDescription = false;

    /**
     * @var string
     */
    public $fancyOverlay = '#f0f0f0';

    /**
     * @var float
     */
    public $fancyOverlayAlpha = .9;

    /**
     * @var string
     */
    public $infoBg = '#0C99D5';

    /**
     * @var string
     */
    public $fadingColor = '';

    /**
     * @var string
     */
    public $navigationPrev;

    /**
     * @var string
     */
    public $navigationNext;

    /**
     * @var float
     */
    public $slideSpeed = 300.;

    /**
     * @var bool
     */
    public $autoPlay = false;

    /**
     * @var float
     */
    public $autoPlayTime = 5000.0;

    /**
     * @var bool
     */
    public $itemsScaleUp = false;

    /**
     * @var int
     */
    public $margin = 5;

    /**
     * @var bool
     */
    public $isTransparent = false;

    /**
     * @var bool
     */
    public $showItemsOutside = false;

    /**
     * @var bool
     */
    public $responsiveContainer = false;

    /**
     * @var bool
     */
    public $loop = false;

    /**
     * @var bool
     */
    public $center = false;

    /**
     * @var bool
     */
    public $dots = true;

    /**
     * @var bool
     */
    public $nav = false;

    /**
     * @var bool
     */
    public $lazy = false;

    public function __construct()
    {
        $this->navigationPrev = '<i class="fa fa-arrow-circle-left"></i> ' . t('Prev');
        $this->navigationNext = t('Next') . ' <i class="fa fa-arrow-circle-right"></i>';
    }

    /**
     * @param string|mixed $string
     *
     * @return \EasyImageSlider\Options
     */
    public static function fromJSON($string)
    {
        $data = array();
        if (is_string($string) && $string !== '') {
            set_error_handler(function () {}, -1);
            $data = json_decode($string, true);
            restore_error_handler();
            if (!is_array($data)) {
                $data = array();
            }
        }

        return self::fromArray($data);
    }

    /**
     * @param array|mixed $args
     *
     * @return \EasyImageSlider\Options
     */
    public static function fromUI($args)
    {
        $args = is_array($args) ? $args : array();

        return self::fromArray($args);
    }

    /**
     * @return string
     *
     * @example 'rgba(1, 2, 3, 1)'
     */
    public function getFancyOverlayCSSColor()
    {
        $hex = ltrim($this->fancyOverlay, '#');
        switch (strlen($hex)) {
            case 3:
                $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
                break;
            case 6:
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                break;
            default:
                $r = 0;
                $g = 0;
                $b = 0;
                break;
        }

        return "rgba({$r}, {$g}, {$b}, {$this->fancyOverlayAlpha})";
    }

    /**
     * @return bool
     */
    public function isSingleItemSlide()
    {
        return $this->items === 1;
    }

    /**
     * @return \EasyImageSlider\Options
     */
    private static function fromArray(array $data)
    {
        $rxColor = '/^#[0-9A-Fa-f]{3}([0-9A-Fa-f]{3})?$/';
        $result = new self();
        foreach ($data as $key => $value) {
            switch ($key) {
                // Strings (may be empty)
                case 'navigationPrev':
                case 'navigationNext':
                    $result->$key = (string) $value;
                    break;
                // Colors (may be empty)
                case 'fadingColor':
                    $value = (string) $value;
                    if ($value === '' || preg_match($rxColor, $value)) {
                        $result->$key = $value;
                    }
                    break;
                // Colors (may not be empty)
                case 'fancyOverlay':
                case 'infoBg':
                    $value = (string) $value;
                    if ($value !== '' && preg_match($rxColor, $value)) {
                        $result->$key = $value;
                    }
                    break;
                // Non-nullable non-nevative integer numbers
                case 'items':
                case 'margin':
                    $value = empty($value) ? 0 : (int) $value;
                    if ($value >= 0) {
                        switch ($key) {
                            case 'items':
                                if ($value > 20) {
                                    $value = 20;
                                }
                                break;
                            case 'margin':
                                if ($value > 20) {
                                    $value = 5;
                                }
                                break;
                        }
                        $result->$key = $value;
                    }
                    break;
                // Non-nullable non-negative floating point numbers
                case 'fancyOverlayAlpha':
                case 'slideSpeed':
                case 'autoPlayTime':
                    if (is_numeric($value)) {
                        $value = (float) $value;
                        if ($value >= 0.0) {
                            switch ($key) {
                                case 'fancyOverlayAlpha':
                                    if ($value > 1.0) {
                                        $value = 0.9;
                                    }
                                    break;
                                case 'slideSpeed':
                                    if ($value > 3000.0) {
                                        $value = 200.0;
                                    }
                                    break;
                                case 'autoPlayTime':
                                    if ($value > 50000.0) {
                                        $value = 5000.0;
                                    }
                                    break;
                            }
                            $result->$key = (float) $value;
                        }
                    }
                    break;
                // Non-nullable booleans
                case 'isTransparent':
                case 'showItemsOutside':
                case 'autoPlay':
                case 'lazy':
                case 'nav':
                case 'loop':
                case 'responsiveContainer':
                case 'ItemsTitle':
                case 'ItemsDescription':
                case 'lightboxTitle':
                case 'lightboxDescription':
                case 'itemsScaleUp':
                case 'center':
                    $result->$key = !empty($value);
                    break;
                // Special cases
                case 'lightbox':
                case 'animateIn':
                case 'animateOut':
                    if (is_string($value) && $value !== '0') {
                        $result->lightbox = $value;
                    }
                    break;
                default:
                    $result->$key = $value;
                    break;
            }
        }

        return $result;
    }
}
