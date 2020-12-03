<?php   defined('C5_EXECUTE') or die("Access Denied.");
?>

          <option value="0"><?php   echo t('None') ?></option>
        <optgroup label="Attention Seekers">
          <option value="bounce" <?php   echo $option == 'bounce' ? 'selected' : '' ?>><?php   echo t('bounce')?></option>
          <option value="flash" <?php   echo $option == 'flash' ? 'selected' : '' ?>><?php   echo t('flash')?></option>
          <option value="pulse" <?php   echo $option == 'pulse' ? 'selected' : '' ?>><?php   echo t('pulse')?></option>
          <option value="rubberBand" <?php   echo $option == 'rubberBand' ? 'selected' : '' ?>><?php   echo t('rubberBand')?></option>
          <option value="shake" <?php   echo $option == 'shake' ? 'selected' : '' ?>><?php   echo t('shake')?></option>
          <option value="swing" <?php   echo $option == 'swing' ? 'selected' : '' ?>><?php   echo t('swing')?></option>
          <option value="tada" <?php   echo $option == 'tada' ? 'selected' : '' ?>><?php   echo t('tada')?></option>
          <option value="wobble" <?php   echo $option == 'wobble' ? 'selected' : '' ?>><?php   echo t('wobble')?></option>
        </optgroup>

        <optgroup label="Bouncing Entrances">
          <option value="bounceIn" <?php   echo $option == 'bounceIn' ? 'selected' : '' ?>><?php   echo t('bounceIn')?></option>
          <option value="bounceInDown" <?php   echo $option == 'bounceInDown' ? 'selected' : '' ?>><?php   echo t('bounceInDown')?></option>
          <option value="bounceInLeft" <?php   echo $option == 'bounceInLeft' ? 'selected' : '' ?>><?php   echo t('bounceInLeft')?></option>
          <option value="bounceInRight" <?php   echo $option == 'bounceInRight' ? 'selected' : '' ?>><?php   echo t('bounceInRight')?></option>
          <option value="bounceInUp" <?php   echo $option == 'bounceInUp' ? 'selected' : '' ?>><?php   echo t('bounceInUp')?></option>
        </optgroup>

        <optgroup label="Bouncing Exits">
          <option value="bounceOut" <?php   echo $option == 'bounceOut' ? 'selected' : '' ?>><?php   echo t('bounceOut')?></option>
          <option value="bounceOutDown" <?php   echo $option == 'bounceOutDown' ? 'selected' : '' ?>><?php   echo t('bounceOutDown')?></option>
          <option value="bounceOutLeft" <?php   echo $option == 'bounceOutLeft' ? 'selected' : '' ?>><?php   echo t('bounceOutLeft')?></option>
          <option value="bounceOutRight" <?php   echo $option == 'bounceOutRight' ? 'selected' : '' ?>><?php   echo t('bounceOutRight')?></option>
          <option value="bounceOutUp" <?php   echo $option == 'bounceOutUp' ? 'selected' : '' ?>><?php   echo t('bounceOutUp')?></option>
        </optgroup>

        <optgroup label="Fading Entrances">
          <option value="fadeIn" <?php   echo $option == 'fadeIn' ? 'selected' : '' ?>><?php   echo t('fadeIn')?></option>
          <option value="fadeInDown" <?php   echo $option == 'fadeInDown' ? 'selected' : '' ?>><?php   echo t('fadeInDown')?></option>
          <option value="fadeInDownBig" <?php   echo $option == 'fadeInDownBig' ? 'selected' : '' ?>><?php   echo t('fadeInDownBig')?></option>
          <option value="fadeInLeft" <?php   echo $option == 'fadeInLeft' ? 'selected' : '' ?>><?php   echo t('fadeInLeft')?></option>
          <option value="fadeInLeftBig" <?php   echo $option == 'fadeInLeftBig' ? 'selected' : '' ?>><?php   echo t('fadeInLeftBig')?></option>
          <option value="fadeInRight" <?php   echo $option == 'fadeInRight' ? 'selected' : '' ?>><?php   echo t('fadeInRight')?></option>
          <option value="fadeInRightBig" <?php   echo $option == 'fadeInRightBig' ? 'selected' : '' ?>><?php   echo t('fadeInRightBig')?></option>
          <option value="fadeInUp" <?php   echo $option == 'fadeInUp' ? 'selected' : '' ?>><?php   echo t('fadeInUp')?></option>
          <option value="fadeInUpBig" <?php   echo $option == 'fadeInUpBig' ? 'selected' : '' ?>><?php   echo t('fadeInUpBig')?></option>
        </optgroup>

        <optgroup label="Fading Exits">
          <option value="fadeOut" <?php   echo $option == 'fadeOut' ? 'selected' : '' ?>><?php   echo t('fadeOut')?></option>
          <option value="fadeOutDown" <?php   echo $option == 'fadeOutDown' ? 'selected' : '' ?>><?php   echo t('fadeOutDown')?></option>
          <option value="fadeOutDownBig" <?php   echo $option == 'fadeOutDownBig' ? 'selected' : '' ?>><?php   echo t('fadeOutDownBig')?></option>
          <option value="fadeOutLeft" <?php   echo $option == 'fadeOutLeft' ? 'selected' : '' ?>><?php   echo t('fadeOutLeft')?></option>
          <option value="fadeOutLeftBig" <?php   echo $option == 'fadeOutLeftBig' ? 'selected' : '' ?>><?php   echo t('fadeOutLeftBig')?></option>
          <option value="fadeOutRight" <?php   echo $option == 'fadeOutRight' ? 'selected' : '' ?>><?php   echo t('fadeOutRight')?></option>
          <option value="fadeOutRightBig" <?php   echo $option == 'fadeOutRightBig' ? 'selected' : '' ?>><?php   echo t('fadeOutRightBig')?></option>
          <option value="fadeOutUp" <?php   echo $option == 'fadeOutUp' ? 'selected' : '' ?>><?php   echo t('fadeOutUp')?></option>
          <option value="fadeOutUpBig" <?php   echo $option == 'fadeOutUpBig' ? 'selected' : '' ?>><?php   echo t('fadeOutUpBig')?></option>
        </optgroup>

        <optgroup label="Flippers">
          <option value="flip" <?php   echo $option == 'flip' ? 'selected' : '' ?>><?php   echo t('flip')?></option>
          <option value="flipInX" <?php   echo $option == 'flipInX' ? 'selected' : '' ?>><?php   echo t('flipInX')?></option>
          <option value="flipInY" <?php   echo $option == 'flipInY' ? 'selected' : '' ?>><?php   echo t('flipInY')?></option>
          <option value="flipOutX" <?php   echo $option == 'flipOutX' ? 'selected' : '' ?>><?php   echo t('flipOutX')?></option>
          <option value="flipOutY" <?php   echo $option == 'flipOutY' ? 'selected' : '' ?>><?php   echo t('flipOutY')?></option>
        </optgroup>

        <optgroup label="Lightspeed">
          <option value="lightSpeedIn" <?php   echo $option == 'lightSpeedIn' ? 'selected' : '' ?>><?php   echo t('lightSpeedIn')?></option>
          <option value="lightSpeedOut" <?php   echo $option == 'lightSpeedOut' ? 'selected' : '' ?>><?php   echo t('lightSpeedOut')?></option>
        </optgroup>

        <optgroup label="Rotating Entrances">
          <option value="rotateIn" <?php   echo $option == 'rotateIn' ? 'selected' : '' ?>><?php   echo t('rotateIn')?></option>
          <option value="rotateInDownLeft" <?php   echo $option == 'rotateInDownLeft' ? 'selected' : '' ?>><?php   echo t('rotateInDownLeft')?></option>
          <option value="rotateInDownRight" <?php   echo $option == 'rotateInDownRight' ? 'selected' : '' ?>><?php   echo t('rotateInDownRight')?></option>
          <option value="rotateInUpLeft" <?php   echo $option == 'rotateInUpLeft' ? 'selected' : '' ?>><?php   echo t('rotateInUpLeft')?></option>
          <option value="rotateInUpRight" <?php   echo $option == 'rotateInUpRight' ? 'selected' : '' ?>><?php   echo t('rotateInUpRight')?></option>
        </optgroup>

        <optgroup label="Rotating Exits">
          <option value="rotateOut" <?php   echo $option == 'rotateOut' ? 'selected' : '' ?>><?php   echo t('rotateOut')?></option>
          <option value="rotateOutDownLeft" <?php   echo $option == 'rotateOutDownLeft' ? 'selected' : '' ?>><?php   echo t('rotateOutDownLeft')?></option>
          <option value="rotateOutDownRight" <?php   echo $option == 'rotateOutDownRight' ? 'selected' : '' ?>><?php   echo t('rotateOutDownRight')?></option>
          <option value="rotateOutUpLeft" <?php   echo $option == 'rotateOutUpLeft' ? 'selected' : '' ?>><?php   echo t('rotateOutUpLeft')?></option>
          <option value="rotateOutUpRight" <?php   echo $option == 'rotateOutUpRight' ? 'selected' : '' ?>><?php   echo t('rotateOutUpRight')?></option>
        </optgroup>

        <optgroup label="Sliding Entrances">
          <option value="slideInUp" <?php   echo $option == 'slideInUp' ? 'selected' : '' ?>><?php   echo t('slideInUp')?></option>
          <option value="slideInDown" <?php   echo $option == 'slideInDown' ? 'selected' : '' ?>><?php   echo t('slideInDown')?></option>
          <option value="slideInLeft" <?php   echo $option == 'slideInLeft' ? 'selected' : '' ?>><?php   echo t('slideInLeft')?></option>
          <option value="slideInRight" <?php   echo $option == 'slideInRight' ? 'selected' : '' ?>><?php   echo t('slideInRight')?></option>

        </optgroup>
        <optgroup label="Sliding Exits">
          <option value="slideOutUp" <?php   echo $option == 'slideOutUp' ? 'selected' : '' ?>><?php   echo t('slideOutUp')?></option>
          <option value="slideOutDown" <?php   echo $option == 'slideOutDown' ? 'selected' : '' ?>><?php   echo t('slideOutDown')?></option>
          <option value="slideOutLeft" <?php   echo $option == 'slideOutLeft' ? 'selected' : '' ?>><?php   echo t('slideOutLeft')?></option>
          <option value="slideOutRight" <?php   echo $option == 'slideOutRight' ? 'selected' : '' ?>><?php   echo t('slideOutRight')?></option>
          
        </optgroup>
        
        <optgroup label="Zoom Entrances">
          <option value="zoomIn" <?php   echo $option == 'zoomIn' ? 'selected' : '' ?>><?php   echo t('zoomIn')?></option>
          <option value="zoomInDown" <?php   echo $option == 'zoomInDown' ? 'selected' : '' ?>><?php   echo t('zoomInDown')?></option>
          <option value="zoomInLeft" <?php   echo $option == 'zoomInLeft' ? 'selected' : '' ?>><?php   echo t('zoomInLeft')?></option>
          <option value="zoomInRight" <?php   echo $option == 'zoomInRight' ? 'selected' : '' ?>><?php   echo t('zoomInRight')?></option>
          <option value="zoomInUp" <?php   echo $option == 'zoomInUp' ? 'selected' : '' ?>><?php   echo t('zoomInUp')?></option>
        </optgroup>
        
        <optgroup label="Zoom Exits">
          <option value="zoomOut" <?php   echo $option == 'zoomOut' ? 'selected' : '' ?>><?php   echo t('zoomOut')?></option>
          <option value="zoomOutDown" <?php   echo $option == 'zoomOutDown' ? 'selected' : '' ?>><?php   echo t('zoomOutDown')?></option>
          <option value="zoomOutLeft" <?php   echo $option == 'zoomOutLeft' ? 'selected' : '' ?>><?php   echo t('zoomOutLeft')?></option>
          <option value="zoomOutRight" <?php   echo $option == 'zoomOutRight' ? 'selected' : '' ?>><?php   echo t('zoomOutRight')?></option>
          <option value="zoomOutUp" <?php   echo $option == 'zoomOutUp' ? 'selected' : '' ?>><?php   echo t('zoomOutUp')?></option>
        </optgroup>

        <optgroup label="Specials">
          <option value="hinge" <?php   echo $option == 'hinge' ? 'selected' : '' ?>><?php   echo t('hinge')?></option>
          <option value="rollIn" <?php   echo $option == 'rollIn' ? 'selected' : '' ?>><?php   echo t('rollIn')?></option>
          <option value="rollOut" <?php   echo $option == 'rollOut' ? 'selected' : '' ?>><?php   echo t('rollOut')?></option>
        </optgroup>