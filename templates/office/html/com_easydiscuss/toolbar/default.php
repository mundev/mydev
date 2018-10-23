<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="t-lg-pl--lg t-lg-pr--lg">
	<div class="si-components-header">
		<?php if ($this->acl->allowed('add_question') && !$post->isUserBanned()) { ?>
		<div class="si-components-header__action">
			<a href="<?php echo EDR::_('view=ask');?>" class="btn btn-primary"><?php echo JText::_('OFFICE_ED_NEW_DISCUSSION'); ?></a>
		</div>
		<?php } ?>

		<?php if ($this->my->id) { ?>
		<div class="si-components-header__settings">
			<?php if ($this->config->get('main_rss')) { ?>
			<div class="">
				<a href="<?php echo ED::feeds()->getFeedUrl('view=index');?>" target="_blank"
					data-ed-provide="tooltip"
					data-original-title="<?php echo JText::_("COM_EASYDISCUSS_TOOLBAR_SUBSCRIBE_RSS");?>">
					<i class="fa fa-rss-square"></i>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_sitesubscription')) { ?>
			<?php echo ED::subscription()->html($this->my->id, '0', 'site'); ?>
			<?php } ?>

			<div class="">
				<a href="javascript:void(0);" class="dropdown_ <?php echo $notificationsCount ? 'has-new' : '';?>"
					data-ed-notifications-wrapper
					data-ed-popbox="ajax://site/views/notifications/popbox"
					data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
					data-ed-popbox-toggle="click"
					data-ed-popbox-offset="4"
					data-ed-popbox-type="navbar-notifications"
					data-ed-popbox-component="popbox--navbar"
					data-ed-popbox-cache="0"

					data-ed-provide="tooltip"
					data-original-title="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS');?>"
				>
					<i class="fa fa-bell"></i> <span class="ed-navbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS');?></span>
					<span class="si-components-header__link-bubble" data-ed-conversations-counter></span>
				</a>
			</div>

			<div class="">
				<div class="dropdown_">
					<a href="javascript:void(0);" class=" dropdown-toggle_" 
						data-toggle="dropdown" 
						data-placement="top" 
						role="button" 
						aria-expanded="false"

						data-ed-provide="tooltip"
						data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MORE_SETTINGS');?>"
					>
						<i class="fa fa-cog"></i>
					</a>
					<div id="more-settings" role="menu" class="si-components-header__dropdown-menu dropdown-menu bottom-right">
						
						<div class="popbox-dropdown">
							<div class="popbox-dropdown__hd">
								<div class="popbox-dropdown__hd-flag">
									<div class="popbox-dropdown__hd-body">
										<a href="<?php echo $this->profile->getPermalink();?>" class="eb-user-name"><?php echo $this->profile->getName();?></a>
									</div>
									<div class="popbox-dropdown__hd-image">
										<?php echo $this->html('user.avatar', $this->profile, array('rank' => false, 'popbox' => false)); ?>
									</div>
								</div>
							</div>
							<div class="popbox-dropdown__bd">
								<div class="popbox-dropdown-nav">
									<div class="popbox-dropdown-nav__item ">
										<span class="popbox-dropdown-nav__link">

											<div class="popbox-dropdown-nav__name">
												<?php echo JText::_('OFFICE_ED_MANAGE'); ?>
											</div>

											<ol class="popbox-dropdown-nav__meta-lists">
												<li>
													<a href="<?php echo EDR::_('view=mypost');?>">
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_POSTS');?>
													</a>
												</li>
												<li>
													<a href="<?php echo EDR::_('view=assigned');?>">
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS');?>
													</a>
												</li>
												<li>
													<a href="<?php echo EDR::_('view=favourites');?>">
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES');?>
													</a>
												</li>
												<li>
													<a href="<?php echo EDR::_('view=dashboard');?>">
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_DASHBOARD');?>
													</a>
												</li>
											</ol>
										</span>
									</div>
									<div class="popbox-dropdown-nav__item ">
										<span class="popbox-dropdown-nav__link">
											<div class="popbox-dropdown-nav__name">
												<?php echo JText::_('OFFICE_ED_ACCOUNT'); ?>
											</div>
											<ol class="popbox-dropdown-nav__meta-lists">
												<li>
													<a href="<?php echo EDR::_('view=subscription');?>">
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION'); ?>
													</a>
												</li>
												<li>
													<a href="<?php echo $this->profile->getEditProfileLink();?>">
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_EDIT_PROFILE'); ?>
													</a>
												</li>
											</ol>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	
	<div class="si-components-nav">
		<div class="si-components-nav__item <?php echo $active == 'forums' ? 'is-active' : '';?>">
			<a href="<?php echo EDR::_('view=forums');?>" class="si-components-nav__link">
				<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_FORUMS');?></span>
			</a>
		</div>

		<?php if ($showRecent) { ?>
		<div class="si-components-nav__item <?php echo $active == 'index' ? 'is-active' : '';?>">
			<a href="<?php echo EDR::_('view=index');?>" class="si-components-nav__link">
				<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_RECENT');?></span>
			</a>
		</div>
		<?php } ?>

		<?php if ($showCategories) { ?>
		<div class="si-components-nav__item <?php echo $active == 'categories' ? 'is-active' : '';?>">
			<a href="<?php echo EDR::_('view=categories');?>" class="si-components-nav__link">
				<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES');?></span>
			</a>
		</div>
		<?php } ?>

		<!-- Temporarily use ES responsive because currently ED doesn't have responsive class. -->
		<?php if (!ES::responsive()->isMobile()) { ?>
			<?php if ($showTags) { ?>
			<div class="si-components-nav__item <?php echo $active == 'tags' ? 'is-active' : '';?>">
				<a href="<?php echo EDR::_('view=tags');?>" class="si-components-nav__link">
					<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->config->get('integration_easysocial_members') && ED::easysocial()->exists()) { ?>
			<div class="si-components-nav__item <?php echo $active == 'users' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::users();?>" class="si-components-nav__link">
					<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?></span>
				</a>
			</div>
			<?php } else { ?>
			<div class="si-components-nav__item <?php echo $active == 'users' ? 'is-active' : '';?>">
				<a href="<?php echo EDR::_('view=users');?>" class="si-components-nav__link">
					<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showBadges && $this->config->get('main_badges')) { ?>
			<div class="si-components-nav__item <?php echo $active == 'badges' ? 'is-active' : '';?>">
				<a href="<?php echo EDR::_('view=badges');?>" class="si-components-nav__link">
					<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($group) { ?>
			<div class="si-components-nav__item <?php echo $active == 'groups' ? 'is-active' : '';?>">
				<a href="<?php echo EDR::_('view=groups');?>" class="si-components-nav__link">
					<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_GROUPS');?></span>
				</a>
			</div>
			<?php } ?>
		<?php } ?>

		<!-- Temporarily use ES responsive because currently ED doesn't have responsive class. -->
		<?php if (ES::responsive()->isMobile()) { ?>
		<div class="si-components-nav__item <?php echo ($active == 'tags' || $active == 'users' || $active == 'badges' || $active == 'groups') ? 'is-active' : ''; ?>">
			<div class="btn-group">
				<a href="javascript:void(0);" class="si-components-nav__link dropdown-toggle_" data-toggle="dropdown">
					<span>More</span> &nbsp;
					<i class="i-chevron i-chevron--down"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php if ($showTags) { ?>
					<li class="<?php echo $active == 'tags' ? 'is-active' : '';?>">
						<a href="<?php echo EDR::_('view=tags');?>" class="si-components-nav__dropdown-link">
							<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS');?></span>
						</a>
					</li>
					<?php } ?>

					<?php if ($this->config->get('integration_easysocial_members') && ED::easysocial()->exists()) { ?>
					<li class="<?php echo $active == 'users' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::users();?>" class="si-components-nav__dropdown-link">
							<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?></span>
						</a>
					</li>
					<?php } else { ?>
					<li class="<?php echo $active == 'users' ? 'is-active' : '';?>">
						<a href="<?php echo EDR::_('view=users');?>" class="si-components-nav__dropdown-link">
							<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?></span>
						</a>
					</li>
					<?php } ?>

					<?php if ($showBadges && $this->config->get('main_badges')) { ?>
					<li class="<?php echo $active == 'badges' ? 'is-active' : '';?>">
						<a href="<?php echo EDR::_('view=badges');?>" class="si-components-nav__dropdown-link">
							<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES');?></span>
						</a>
					</li>
					<?php } ?>

					<?php if ($group) { ?>
					<li class="<?php echo $active == 'groups' ? 'is-active' : '';?>">
						<a href="<?php echo EDR::_('view=groups');?>" class="si-components-nav__dropdown-link">
							<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_GROUPS');?></span>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>
	</div>
</div>

<?php if ($renderToolbarModule) { ?>
<?php echo ED::renderModule('easydiscuss-after-toolbar'); ?>
<?php } ?>

<?php if($messageObject) { ?>
	<div class="o-alert o-alert--<?php echo $messageObject->type;?>">
		<?php echo $messageObject->message; ?>
	</div>
<?php } ?>