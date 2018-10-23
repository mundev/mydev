<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-sidemenu">
	<div class="popbox-dropdown">

		<?php if ($this->my->id) { ?>
		<div class="popbox-dropdown__hd">
			<div class="popbox-dropdown__hd-flag">
				<div class="popbox-dropdown__hd-body">
					<?php if ($this->acl->get('add_entry')) { ?>
						<a href="<?php echo $this->profile->getPermalink();?>" class="eb-user-name"><?php echo $this->profile->getName();?></a>
					<?php } else { ?>
						<?php echo $this->profile->getName();?>
					<?php } ?>

					<?php if ($this->acl->get('add_entry')) { ?>
					<div class="mt-5">
						<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard');?>" class="text-muted">
							<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_OVERVIEW');?>
						</a>
					</div>
					<?php } ?>
				</div>

				<div class="popbox-dropdown__hd-image">
					<?php if ($this->acl->get('add_entry')) { ?>
					<a href="<?php echo $this->profile->getPermalink();?>" class="eb-toolbar__avatar">
						<img src="<?php echo $this->profile->getAvatar();?>" alt="<?php echo $this->html('string.escape', $this->profile->getName());?>" width="24" height="24" />
					</a>
					<?php } else { ?>
					<div class="eb-toolbar__avatar">
						<img src="<?php echo $this->profile->getAvatar();?>" alt="<?php echo $this->html('string.escape', $this->profile->getName());?>" width="24" height="24" />
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>

		<div class="popbox-dropdown__bd">
			<div class="popbox-dropdown-nav">
				<div class="popbox-dropdown-nav__item">
					<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EB_MENU');?></div>

					<span class="popbox-dropdown-nav__link">
						<ol class="popbox-dropdown-nav__meta-lists">
							<?php if ($this->config->get('layout_categories')) { ?>
							<li>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=categories');?>">
									<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CATEGORIES');?></span>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->config->get('layout_tags')) { ?>
							<li>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=tags');?>">
									<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TAGS');?></span>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->config->get('layout_bloggers') && !$bloggerMode) { ?>
							<li>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger');?>">
									<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_BLOGGERS');?></span>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->config->get('layout_teamblog')) { ?>
							<li>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=teamblog');?>">
									<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAMBLOGS');?></span>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->config->get('layout_archives')) { ?>
							<li>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=archive');?>">
									<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_ARCHIVES');?></span>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->config->get('layout_calendar')) { ?>
							<li>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=calendar&layout=calendarView');?>">
									<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CALENDAR');?></span>
								</a>
							</li>
							<?php } ?>
						</ol>

					</span>
				</div>

				<?php if ($showManage) { ?>
				<div class="popbox-dropdown-nav__item">
					<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_MANAGE');?></div>

					<span class="popbox-dropdown-nav__link">

						<?php if (!EB::easysocial()->exists() && !EB::easydiscuss()->exists()) { ?>
						<!-- <div class="popbox-dropdown-nav__name mb-10">
							<?php //echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_MANAGE');?>
						</div> -->
						<?php } ?>

						<ol class="popbox-dropdown-nav__meta-lists">

							<?php if ($this->acl->get('add_entry')) { ?>
							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>">
									<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_POSTS');?>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->acl->get('create_post_templates')) { ?>
							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=templates');?>">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_POST_TEMPLATES');?>
								</a>
							</li>
							<?php } ?>

							<?php if (EB::isSiteAdmin() || ($this->acl->get('moderate_entry') || ($this->acl->get('manage_pending') && $this->acl->get('publish_entry')))) { ?>
							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');?>">
									<i class="fa fa-ticket"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_PENDING');?>
									<?php if ($totalPending) { ?>
									<span class="popbox-dropdown-nav__indicator ml-5"></span>
									<span class="popbox-dropdown-nav__counter"><?php echo $totalPending;?></span>
									<?php } ?>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->acl->get('manage_comment') && EB::comment()->isBuiltin()) { ?>
							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=comments');?>">
									<i class="fa fa-comments"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_COMMENTS');?>
									<?php if ($totalPendingComments) { ?>
									<span class="popbox-dropdown-nav__indicator ml-5"></span>
									<span class="popbox-dropdown-nav__counter"><?php echo $totalPendingComments; ?></span>
									<?php } ?>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->acl->get('create_category')) { ?>
							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=categories');?>">
									<i class="fa fa-folder-o"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_CATEGORIES');?>
								</a>
							</li>
							<?php } ?>

							<?php if ($this->acl->get('create_tag')) { ?>
							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>">
									<i class="fa fa-tags"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_TAGS');?>
								</a>
							</li>
							<?php } ?>

							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=favourites');?>">
									<?php echo JText::_('COM_EB_FAVOURITE_POSTS');?>
								</a>
							</li>

							<?php if ($this->config->get('layout_teamblog') && $this->acl->get('create_team_blog')) { ?>
							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs');?>">
									<?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAMBLOGS');?>
								</a>
							</li>
							<?php } ?>

							<?php if ((EB::isTeamAdmin() || EB::isSiteAdmin()) && $this->acl->get('create_team_blog')){ ?>
							<li>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=requests');?>">
									<i class="fa fa-users"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAM_REQUESTS');?>
									<?php if ($totalTeamRequests) { ?>
									<span class="popbox-dropdown-nav__indicator ml-5"></span>
									<span class="popbox-dropdown-nav__counter"><?php echo $totalTeamRequests;?></span>
									<?php } ?>
								</a>
							</li>
							<?php } ?>
						</ol>
					</span>
				</div>
				<?php } ?>

				<?php if ($this->my->id) { ?>
				<div class="popbox-dropdown-nav__item">
					<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYBLOG_YOUR_ACCOUNT');?></div>
					<span class="popbox-dropdown-nav__link">
						

						<ol class="popbox-dropdown-nav__meta-lists">
							<?php if ($this->acl->get('allow_subscription')) { ?>
							<li>
								<a href="<?php echo EB::_('index.php?option=com_easyblog&view=subscription');?>">
									<i class="fa fa-envelope"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_SUBSCRIPTIONS');?>
								</a>
							</li>
							<?php } ?>

							<li>
								<a href="<?php echo EB::getEditProfileLink();?>">
									<?php echo JText::_('COM_EASYBLOG_TOOLBAR_EDIT_PROFILE');?>
								</a>
							</li>


							<li>
								<a href="javascript:void(0);" data-blog-toolbar-logout>
									<i class="fa fa-power-off"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_SIGN_OUT');?>
								</a>
							</li>
						</ol>
					</span>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>

</div>