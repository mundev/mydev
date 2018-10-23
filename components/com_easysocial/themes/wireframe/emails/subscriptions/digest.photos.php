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
<table role="presentation" aria-hidden="true" border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" align="left">
    <tr>
        <?php foreach ($photos as $photo) { ?>
            <td valign="middle" width="64">
                <img src="<?php echo $photo->getSource('thumbnail'); ?>" style="vertical-align:middle;" width="64"/>
            </td>
        <?php } ?>
    </tr>
</table>
