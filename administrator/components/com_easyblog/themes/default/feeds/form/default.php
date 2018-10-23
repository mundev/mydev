<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_EASYBLOG_FEEDS_DETAILS', 'COM_EASYBLOG_FEEDS_DETAILS_INFO'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_TITLE', 'title'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.text', 'title', $feed->title, 'title'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_URL', 'url'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.text', 'url', $feed->url, 'url'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISHED', 'published'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'published', $feed->published); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_CRON', 'cron'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'cron', $feed->cron); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_CRON_INTERVAL', 'interval'); ?>

						<div class="col-md-7">
							<input class="input-mini text-center form-control" id="interval" name="interval" size="3" value="<?php echo $feed->get('interval');?>" /> <?php echo JText::_('COM_EASYBLOG_MINUTES');?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_SHOW_AUTHOR', 'author'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'author', $feed->author); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_COPYRIGHT_TEXT', 'copyrights'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.text', 'copyrights', $params->get('copyrights')); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_INCLUDE_ORIGINAL_LINK', 'sourceLinks'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'sourceLinks', $params->get('sourceLinks')); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_AMOUNT', 'feedamount'); ?>

						<div class="col-md-7">
							<input type="text" name="feedamount" id="" class="input-mini text-center form-control" value="<?php echo $params->get('feedamount', 10);?>" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_EASYBLOG_FEEDS_PUBLISHING_DETAILS', 'COM_EASYBLOG_FEEDS_PUBLISHING_DETAILS_INFO'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISH_ITEM', 'item_published'); ?>

						<div class="col-md-7">
							<select name="item_published" id="item_published" class="form-control">
								<option value="1" <?php echo ($feed->item_published == '1') ? 'selected' : '' ; ?> ><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></option>
								<option value="0" <?php echo ($feed->item_published == '0') ? 'selected' : '' ; ?>><?php echo JText::_( 'COM_EASYBLOG_UNPUBLISHED' ); ?></option>
								<option value="4" <?php echo ($feed->item_published == '4') ? 'selected' : '' ; ?>><?php echo JText::_( 'COM_EASYBLOG_PENDING' ); ?></option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_LANGUAGE', 'language'); ?>

						<div class="col-md-7">
							<select name="language" id="language" class="form-control">
								<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
								<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text' , $feed->language );?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISH_FRONTPAGE', 'item_frontpage'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'item_frontpage', $feed->item_frontpage); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EB_FEEDS_INSERT_CANONICAL_FEED_ITEM_URL', 'item_canonical'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'canonical', $params->get('canonical', false)); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_IMPORT_POST_COVER', 'cover'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'cover', $params->get('cover', false)); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISH_AUTOPOST', 'autopost'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'autopost' ,$params->get('autopost')); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISH_NOTIFY_USERS', 'notify'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'notify', $params->get('notify', true)); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_CATEGORY', 'item_category'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.browseCategory', 'item_category', $feed->item_category); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_AUTHOR', 'item_creator'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.author', 'item_creator', $feed->item_creator); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_TEAM', 'item_team'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.team', 'item_team', $feed->item_team); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_GET_FULL_TEXT', 'item_get_fulltext'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.toggler', 'item_get_fulltext',$feed->item_get_fulltext); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_STORE_CONTENT_TYPE', 'item_content'); ?>

						<div class="col-md-7">
							<select name="item_content" id="item_content" class="form-control">
								<option value="intro" <?php echo ($feed->item_content == 'intro') ? 'selected' : '' ; ?> ><?php echo JText::_( 'COM_EASYBLOG_FEEDS_INTROTEXT' ); ?></option>
								<option value="content" <?php echo ($feed->item_content == 'content') ? 'selected' : '' ; ?>><?php echo JText::_( 'COM_EASYBLOG_FEEDS_MAINTEXT' ); ?></option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_FEEDS_ALLOWED_TAGS', 'item_allowed_tags'); ?>

						<div class="col-md-7">
							<textarea name="item_allowed_tags" class="form-control"><?php echo $params->get( 'allowed' , '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>' ); ?></textarea>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EB_FEEDS_ROBOTS', 'robots'); ?>

						<div class="col-md-7">
							<select name="robots" id="robots" class="o-form-control">
								<option value="" selected="selected"><?php echo JText::_('Inherit From Site');?></option>
								<option value="INDEX, FOLLOW" <?php echo strtoupper($params->get('robots')) == 'INDEX, FOLLOW' ? 'selected="selected"' : '';?>><?php echo JText::_('Index, Follow');?></option>
								<option value="INDEX, NOFOLLOW" <?php echo strtoupper($params->get('robots')) == 'INDEX, NOFOLLOW' ? 'selected="selected"' : '';?>><?php echo JText::_('Index, No Follow');?></option>
								<option value="NOINDEX, FOLLOW" <?php echo strtoupper($params->get('robots')) == 'NOINDEX, FOLLOW' ? 'selected="selected"' : '';?>><?php echo JText::_('No Index, Follow');?></option>
								<option value="NOINDEX, NOFOLLOW" <?php echo strtoupper($params->get('robots')) == 'NOINDEX, NOFOLLOW' ? 'selected="selected"' : '';?>><?php echo JText::_('No Index, No Follow');?></option>
							</select>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>


	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $feed->id;?>" />
</form>
