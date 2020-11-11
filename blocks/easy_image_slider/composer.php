<?php

use EasyImageSlider;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\Type\Composer\Control\BlockControl $view
 * @var EasyImageSlider\FileDetails[] $fDetails
 * @var Concrete\Core\File\Set\Set[] $fileSets
 * @var EasyImageSlider\Options $options
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManager $urlManager
 */

$view->inc(
    'form_setup_html.php',
    array(
        'fDetails' => $fDetails,
        'fileSets' => $fileSets,
        'options' => $options,
        'token' => $token,
        'urlManager' => $urlManager,
        'isComposer' => true,
    )
);
