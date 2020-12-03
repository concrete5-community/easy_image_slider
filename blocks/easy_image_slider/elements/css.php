<?php  
defined('C5_EXECUTE') or die("Access Denied.")

?><style>
    body .fancybox-overlay{background-color: rgba(<?php   echo $this->controller->hex2rgb($options->fancyOverlay) ?>,<?php   echo $options->fancyOverlayAlpha ?>);}
    #easy-slider-wrapper-<?php   echo $bID?>.easy-slider-carousel .item {background-color:<?php   echo $options->isTransparent ? 'transparent' : $options->fadingColor ?>; margin: 0 <?php   echo $options->isSingleItemSlide ? '0' : $options->margin ?>px;}
    #easy-slider-wrapper-<?php   echo $bID?>.easy-slider-carousel .info > div  {background-color:<?php   echo $options->infoBg ?> }
    #easy-slider-wrapper-<?php   echo $bID?> { transition-duration:<?php   echo $options->slideSpeed * 1.5 ?>ms;}
    <?php   if($options->showItemsOutside) : ?> #easy-slider-<?php   echo $bID?> .owl-wrapper-outer {overflow: visible}<?php   endif ?>
</style>