<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_TITLE', 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_NAME_FORMAT', 'layout_nameformat'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option' , 'name' , JText::_('COM_EASYBLOG_REAL_NAME_OPTION'));
							$listLength[] = JHTML::_('select.option', 'nickname', JText::_('COM_EASYBLOG_NICKNAME_OPTION'));
							$listLength[] = JHTML::_('select.option', 'username', JText::_('COM_EASYBLOG_USERNAME_OPTION'));
							echo JHTML::_('select.genericlist', $listLength, 'layout_nameformat', 'class="form-control"', 'value', 'text', $this->config->get('layout_nameformat' , 'name'));
						?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'layout_blogger_breadcrumb', 'COM_EASYBLOG_LAYOUT_BREADCRUMB_BLOGGER'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_DEFAULT_LIST_LIMIT', 'list_limit'); ?>

					<div class="col-md-7">
						<div class="checkbox" style="margin-top: 0;" data-list-length-wrapper>
							<input type="checkbox" id="inherit-joomla" name="listlength_inherit" value="1" <?php echo $this->config->get('layout_listlength') == 0 ? ' checked="checked"' : '';?> data-list-length-inherit />
							<label for="inherit-joomla">
								<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_USE_JOOMLA_LIST_LENGTH');?>
							</label>
						</div>

						<div class="row <?php echo $this->config->get('layout_listlength') == 0 ? 'hide' : '';?>" data-list-length-input>
							<div class="col-md-7">
								<div class="input-group">
									<input type="text" name="layout_listlength" value="<?php echo $this->config->get('layout_listlength');?>" class="form-control text-center" />
									<span class="input-group-addon">
										<?php echo JText::_('Items Per Page'); ?>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_categories_hideempty', 'COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_EMPTY_CATEGORIES'); ?>

				<?php echo $this->html('settings.toggle', 'layout_zero_as_plural', 'COM_EASYBLOG_LAYOUT_ZERO_AS_PLURAL'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE', 'layout_tagstyle'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option' , '1' , JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_STYLE1'));
							$listLength[] = JHTML::_('select.option', '2', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_STYLE2'));
							$listLength[] = JHTML::_('select.option', '3', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_STYLE3'));
							$listLength[] = JHTML::_('select.option', '4', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_STYLE4'));
							echo JHTML::_('select.genericlist', $listLength, 'layout_tagstyle', 'class="form-control"', 'value', 'text', $this->config->get('layout_tagstyle' , '1'));
						?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_CUSTOM_FIELDS', 'COM_EASYBLOG_CUSTOM_FIELDS_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_FIELD_DATE_FORMAT', 'custom_field_date_format'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option', 'l, d F Y' , JFactory::getDate()->format(JText::_('DATE_FORMAT_LC1')));
							$listLength[] = JHTML::_('select.option', 'l, d F Y H:i', JFactory::getDate()->format(JText::_('DATE_FORMAT_LC2')));
							$listLength[] = JHTML::_('select.option', 'd F Y', JFactory::getDate()->format(JText::_('DATE_FORMAT_LC3')));
							echo JHTML::_('select.genericlist', $listLength, 'custom_field_date_format', 'class="form-control"', 'value', 'text', $this->config->get('custom_field_date_format' , JText::_('DATE_FORMAT_LC1')));
						?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EB_SETTINGS_TYPOGRAPHY'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'enable_typography', 'COM_EB_SETTINGS_ENABLE_TYPOGRAPHY'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EB_SETTINGS_SCHEMA'); ?>

			<div class="panel-body">
				<div class="form-group" data-schema-logo-wrapper>
					<?php echo $this->html('panel.label', 'COM_EB_SETTINGS_SCHEMA_LOGO'); ?>

					<div class="col-md-7" data-schema-logo data-id="" data-default-schema-logo="<?php echo EB::getLogo('schema', true); ?>">
						<div class="mb-20">
							<div class="eb-img-holder">
								<div class="eb-img-holder__remove" data-schema-logo-restore-default-wrap <?php echo EB::hasOverrideLogo('schema') ? '' : 'style="display: none;'; ?>>
									<a href="javascript:void(0);" class="" data-schema-logo-restore-default-button>
										<i class="fa fa-times"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_REMOVE'); ?>
									</a>
								</div>
								<img src="<?php echo EB::getLogo('schema'); ?>" width="60" data-schema-logo-image />
							</div>
						</div>
						<div>
							<input type="file" name="schema_logo" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATARS_TITLE'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'layout_avatar', 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_AVATARS', '', 'data-avatars-author'); ?>
				<div class="form-group <?php echo $this->config->get('layout_avatar') ? '' : 'hide';?>" data-avatars-author-settings>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_LINK_AUTHOR_NAME', 'layout_avatar_link_name'); ?>
					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'layout_avatar_link_name', $this->config->get('layout_avatar_link_name')); ?>
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('layout_avatar') ? '' : 'hide';?>" data-avatars-author-settings>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS', 'layout_avatarIntegration'); ?>

					<div class="col-md-7">
						<?php
							$nameFormat = array();
							$avatarIntegration[] = JHTML::_('select.option', 'default', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_DEFAULT'));
							$avatarIntegration[] = JHTML::_('select.option', 'easysocial', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_EASYSOCIAL'));
							$avatarIntegration[] = JHTML::_('select.option', 'jfbconnect', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JFBCONNECT'));
							$avatarIntegration[] = JHTML::_('select.option', 'communitybuilder', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_CB'));
							$avatarIntegration[] = JHTML::_('select.option', 'gravatar', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_GRAVATAR'));
							$avatarIntegration[] = JHTML::_('select.option', 'jomsocial', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JOMSOCIAL'));
							$avatarIntegration[] = JHTML::_('select.option', 'kunena', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_KUNENA'));
							$avatarIntegration[] = JHTML::_('select.option', 'k2', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_K2'));
							$avatarIntegration[] = JHTML::_('select.option', 'phpbb', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_PHPBB'));
							$avatarIntegration[] = JHTML::_('select.option', 'mightytouch', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_MIGHTYREGISTRATION'));
							$avatarIntegration[] = JHTML::_('select.option', 'anahita', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_ANAHITA'));
							$avatarIntegration[] = JHTML::_('select.option', 'jomwall', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JOMWALL'));
							$avatarIntegration[] = JHTML::_('select.option', 'easydiscuss', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_EASYDISCUSS'));
							echo JHTML::_('select.genericlist', $avatarIntegration, 'layout_avatarIntegration', 'class="form-control" data-avatar-source', 'value', 'text', $this->config->get('layout_avatarIntegration' , 'default'));
						?>
					</div>
				</div>

				<div class="form-group hidden" data-phpbb-path>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_PHPBB_PATH', 'layout_phpbb_path'); ?>
					<div class="col-md-7">
						<?php echo $this->html('form.text', 'layout_phpbb_path', $this->config->get('layout_phpbb_path')); ?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'layout_categoryavatar', 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORY_AVATARS'); ?>

				<?php echo $this->html('settings.toggle', 'layout_teamavatar', 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_AVATARS'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_ORDERING', 'COM_EASYBLOG_ORDERING_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING', 'layout_postorder'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option' , 'modified' , JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LAST_MODIFIED'));
							$listLength[] = JHTML::_('select.option', 'latest', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LATEST'));
							$listLength[] = JHTML::_('select.option', 'alphabet', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_ALPHABET'));
							$listLength[] = JHTML::_('select.option', 'popular', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_HITS'));
							$listLength[] = JHTML::_('select.option', 'published', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_PUBLISHED'));
							echo JHTML::_('select.genericlist', $listLength, 'layout_postorder', 'class="form-control"', 'value', 'text', $this->config->get('layout_postorder' , 'latest'));
						?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING', 'layout_postsort'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option', 'desc', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_OPTIONS_DESCENDING'));
							$listLength[] = JHTML::_('select.option', 'asc', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_OPTIONS_ASCENDING'));
							echo JHTML::_('select.genericlist', $listLength, 'layout_postsort', 'class="form-control input-box"', 'value', 'text', $this->config->get('layout_postsort' , 'desc'));
						?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORY_ORDERING', 'layout_categorypostorder'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option' , 'modified' , JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LAST_MODIFIED'));
							$listLength[] = JHTML::_('select.option', 'created', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LATEST'));
							$listLength[] = JHTML::_('select.option', 'title', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_ALPHABET'));
							$listLength[] = JHTML::_('select.option', 'published', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_PUBLISHED'));
							echo JHTML::_('select.genericlist', $listLength, 'layout_categorypostorder', 'class="form-control"', 'value', 'text', $this->config->get('layout_categorypostorder' , 'created'));
						?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING', 'layout_sorting_category'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option', 'alphabet', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING_OPTIONS_TITLE'));
							$listLength[] = JHTML::_('select.option', 'latest', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING_OPTIONS_LATEST'));
							$listLength[] = JHTML::_('select.option', 'ordering', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING_OPTIONS_ORDERING'));
							$listLength[] = JHTML::_('select.option', 'popular', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING_OPTIONS_POPULAR'));
							echo JHTML::_('select.genericlist', $listLength, 'layout_sorting_category', 'class="form-control"', 'value', 'text', $this->config->get('layout_sorting_category' , 'ordering'));
						?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING', 'layout_bloggerorder'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option', 'featured', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_FEATURED'));
							$listLength[] = JHTML::_('select.option', 'latest', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_LATEST'));
							$listLength[] = JHTML::_('select.option', 'alphabet', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_ALPHABET'));
							$listLength[] = JHTML::_('select.option', 'latestpost', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_LATESTPOST'));
							$listLength[] = JHTML::_('select.option', 'active', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_ACTIVE'));
							$listLength[] = JHTML::_('select.option', 'ordering', JText::_('COM_EB_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_ORDERING'));
							echo JHTML::_('select.genericlist', $listLength, 'layout_bloggerorder', 'class="form-control"', 'value', 'text', $this->config->get('layout_bloggerorder', 'latest'));
						?>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
