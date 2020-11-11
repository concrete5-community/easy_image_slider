<?php

namespace EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

class FileDetails
{
    /**
     * @var string
     */
    public $urlInline;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $fID;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int|null
     */
    public $image_thumbnail_width;

    /**
     * @var string
     */
    public $image_link;

    /**
     * @var string
     */
    public $image_link_text;

    /**
     * @var string
     */
    public $image_bg_color;
}
