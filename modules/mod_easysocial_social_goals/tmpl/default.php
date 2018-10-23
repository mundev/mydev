<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="es" class="mod-es mod-es-profile-completeness">    
    <div class="o-box">
        <div class="t-lg-mt--sm">
            <div class="progress">
                <div data-progress="" style="width: <?php echo $percentage;?>%;" class="progress-bar progress-bar-success"></div>
            </div>
            <div class=""><?php echo JText::sprintf('MOD_EASYSOCIAL_SOCIAL_GOALS_PERCENTAGE', $percentage . '%');?></div>
        </div>
        <div class="o-box--border">
            <div class="es-completeness-check-list">
                <?php foreach ($goals as $goal) { ?>
                <div class="o-flag es-completeness-check-list__item <?php echo $goal->value ? 'is-completed' : '';?>">
                    <div class="o-flag__image">
                        <div class="es-completeness-check-list__icon">
                            <i class="fa fa-<?php echo $goal->value ? 'check' : 'question';?>"></i>     
                        </div>
                    </div>
                    <div class="o-flag__body">
                        <span><?php echo JText::_($goal->label);?></span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>