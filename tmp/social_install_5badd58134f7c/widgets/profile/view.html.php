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

require_once(SOCIAL_APPS . '/user/twitter/helper.php');

class TwitterWidgetsProfile extends SocialAppsWidgets
{
    public function sidebarBottom($user)
    {
        $params = $this->getParams();

        if (!$params->get('profile_widget', true)) {
            return;
        }

        $client = TwitterAppHelper::getClient($user->id);
        $table = TwitterAppHelper::getTwitterTable($user->id);

        if (!$table->id) {
            return;
        }

        $tableParams = $table->getParams();
        $screenName = $tableParams->get('screen_name', '');

        if (!$screenName) {
            return;
        }

        // Set the widget height
        $height = $params->get('widget_height', 400);

        $theme = ES::themes();
        $theme->set('height', $height);
        $theme->set('screenName', $screenName);
        
        echo $theme->output('themes:/apps/user/twitter/widget');
    }
}
