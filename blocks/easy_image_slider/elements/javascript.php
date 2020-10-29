<?php
defined('C5_EXECUTE') or die('Access Denied.');

?><script>

$(document).ready(function(){

    containerBg = $('#easy-slider-wrapper-<?php   echo $bID?>').data('colorbg');

<?php   if($options->lightbox == 'lightbox') { ?>
    $("#easy-slider-<?php   echo $bID?> a[rel^='prettyPhoto']").prettyPhoto({
      theme: 'pp_default',
      changepicturecallback: function(){
          // 1024px is presumed here to be the widest mobile device. Adjust at will.
          if ($('body').innerWidth() < 1025) {
              $(".pp_pic_holder.pp_default").css("top",window.pageYOffset+"px");
          }
      }      
    });
<?php   } elseif ($options->lightbox == 'intense') { ?>
    $('#easy-slider-<?php   echo $bID?> a').click(function(e){e.preventDefault()});
    Intense($('#easy-slider-<?php   echo $bID?> a'));
<?php   } ?>

/* -- OWL Carousel -- */
    var easyOWL = $("#easy-slider-<?php   echo $bID?>");
    easyOWL.owlCarousel({   
        autoPlay: <?php    echo $options->autoPlay ? $options->autoPlayTime : 'false'  ?>,
        lazyLoad:  <?php    echo $options->lazy ? 'true' : 'false'  ?>,
        items : <?php   echo $options->items ?>,
        rewindNav : <?php    echo $options->loop ? 'true' : 'false'  ?>,
        // -- Single Item
<?php   if($options->isSingleItemSlide) { ?>
        singleItem:true,        
<?php   } ?>
        autoHeight:true,
        afterAction : sliderOnChange,
        slideSpeed : <?php   echo $options->slideSpeed ?>,
        stopOnHover : true,
        pagination : <?php    echo $options->dots ? 'true' : 'false'  ?>,
        navigation: <?php    echo $options->nav ? 'true' : 'false'  ?>,
        navContainer : "#owl-navigation-<?php   echo $bID?>",
        navigationText : ['<?php   echo $options->navigationPrev ?>','<?php   echo $options->navigationNext ?>']
      });
        
    function sliderOnChange () {
      $('#easy-slider-<?php   echo $bID?> .item').removeClass('active');
      var visible = this.owl.visibleItems;
      var index = this.owl.currentItem;

      for(var i = index; i < visible.length + index ; i++){
          $('#easy-slider-<?php   echo $bID?> #item-' + i).addClass('active');
      }

<?php   if($options->isTransparent) { // If transparent option is selected, we keep color of the visible item to put on the container, to see a transition?>      
      var colorBg =  $('#easy-slider-<?php   echo $bID?> #item-' + visible).data('color');      
      if (colorBg) $('#easy-slider-wrapper-<?php   echo $bID?>').css('background-color', colorBg );
      else $('#easy-slider-wrapper-<?php   echo $bID?>').css('background-color', $('#easy-slider-wrapper-<?php   echo $bID?>').data('colorbg') );
<?php   } ?>      
    }
});

</script>