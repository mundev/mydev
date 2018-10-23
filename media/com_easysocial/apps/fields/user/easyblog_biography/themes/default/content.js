<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
EasySocial
	.require()
	.app('fields/user/easyblog_biography/content')
	.done(function($) {
		$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Easyblog_Biography', {
			required: <?php echo $field->required ? 1 : 0; ?>
		});
	});
