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
<?php if ($this->config->get('layout_toolbar') && $this->acl->get('access_toolbar')) { ?>
<div class="si-components-header">
	<?php if ($this->acl->get('add_entry')) { ?>
	<div class="si-components-header__action">
		<a href="<?php echo EB::composer()->getComposeUrl(); ?>" class="btn btn-primary"><?php echo JText::_('OFFICE_EB_NEW_BLOG'); ?></a>
	</div>
	<?php } ?>
	
	<div class="si-components-header__settings">
		<?php if ($this->config->get('main_rss') && $this->acl->get('allow_subscription_rss') && !$this->isMobile()) { ?>
		<div class="">
			<div class="">
				<a href="<?php echo EB::feeds()->getFeedURL('index.php?option=com_easyblog');?>" target="_blank"
					data-original-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS');?>"
					data-placement="top"
					data-eb-provide="tooltip"
				>
					<i class="fa fa-rss-square"></i>
				</a>
			</div>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_sitesubscription') && $this->acl->get('allow_subscription')) { ?>
		<div class="">
			<div class="<?php echo $subscription->id ? 'hide' : ''; ?>" data-blog-subscribe data-type="site">
				<a href="javascript:void(0);" class=""
					data-original-title="<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MOBILE_SUBSCRIBE');?>"
					data-placement="top"
					data-eb-provide="tooltip"
				>
					<i class="fa fa-envelope"></i>
				</a>

			</div>
			<div class="<?php echo $subscription->id ? '' : 'hide'; ?>" data-blog-unsubscribe data-subscription-id="<?php echo $subscription->id;?>" data-return="<?php echo base64_encode(JRequest::getUri());?>">
				<a href="javascript:void(0);" class=""
					data-original-title="<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MOBILE_UNSUBSCRIBE');?>"
					data-placement="top"
					data-eb-provide="tooltip"
				>
					<i class="fa fa-envelope"></i>
				</a>
			</div>
		</div>
		<?php } ?>

		<?php if ($this->config->get('layout_option_toolbar') && ($this->my->id)) { ?>
		<div class="">
			<div class=" dropdown_">
				<a href="javascript:void(0);" class=" dropdown-toggle_"
					data-bp-toggle="dropdown"
					data-original-title="<?php echo JText::_('COM_EASYBLOG_TOOLBAR_SETTINGS');?>"
					data-placement="top"
					data-eb-provide="tooltip"
					role="button" aria-expanded="false"
				>
					<i class="fa fa-cog"></i>
				</a>

				<div id="more-settings" role="menu" class="si-components-header__dropdown-menu dropdown-menu bottom-right">
					<div class="popbox-dropdown">
						<div class="popbox-dropdown__hd">
							<div class="popbox-dropdown__hd-flag">
								<div class="popbox-dropdown__hd-body">
									<?php if ($this->acl->get('add_entry')) { ?>
										<a href="<?php echo $this->profile->getPermalink();?>" class="eb-user-name"><?php echo $this->profile->getName();?></a>
									<?php } else { ?>
										<?php echo $this->profile->getName();?>
									<?php } ?>

									<?php if ($this->config->get('layout_dashboardmain') && $this->acl->get('add_entry')) { ?>
									<div class="mt-5">
										<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard');?>" class="text-muted">
											<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_OVERVIEW');?>
										</a>
									</div>
									<?php } ?>
								</div>
								<div class="popbox-dropdown__hd-image">
									<?php if ($this->acl->get('add_entry')) { ?>
									<a href="<?php echo $this->profile->getPermalink();?>" class="o-avatar o-avatar--sm">
										<img src="<?php echo $this->profile->getAvatar();?>" alt="<?php echo $this->html('string.escape', $this->profile->getName());?>" width="24" height="24" />
									</a>
									<?php } else { ?>
										<img src="<?php echo $this->profile->getAvatar();?>" alt="<?php echo $this->html('string.escape', $this->profile->getName());?>" width="24" height="24" />
									<?php } ?>
								</div>
							</div>
						</div>

						<div class="popbox-dropdown__bd">
							<div class="popbox-dropdown-nav">

								<?php if ($showManage) { ?>
								<div class="popbox-dropdown-nav__item ">
									<span class="popbox-dropdown-nav__link">

										<div class="popbox-dropdown-nav__name ">
											<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_MANAGE');?>
										</div>

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

											<?php if (EB::isSiteAdmin() || $this->acl->get('moderate_entry')) { ?>
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

											<?php if ($this->config->get('main_favourite_post')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=favourites');?>">
													<?php echo JText::_('COM_EB_FAVOURITE_POSTS');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($this->config->get('layout_teamblog') && $this->acl->get('create_team_blog')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs');?>">
													<?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAMBLOGS');?>
												</a>
											</li>
											<?php } ?>

											<?php if ((EB::isTeamAdmin() || EB::isSiteAdmin()) && $this->config->get('toolbar_teamrequest') && $this->acl->get('create_team_blog')){ ?>
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

								<div class="popbox-dropdown-nav__item ">
									<span class="popbox-dropdown-nav__link">
										<div class="popbox-dropdown-nav__name ">
											<?php echo JText::_('COM_EASYBLOG_YOUR_ACCOUNT'); ?>
										</div>
										<ol class="popbox-dropdown-nav__meta-lists">
											<?php if ($this->acl->get('allow_subscription')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=subscription');?>">
													<i class="fa fa-envelope"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_SUBSCRIPTIONS');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($this->config->get('toolbar_editprofile')){ ?>
											<li>
												<a href="<?php echo EB::getEditProfileLink();?>">
													<?php echo JText::_('COM_EASYBLOG_TOOLBAR_EDIT_PROFILE');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($this->config->get('gdpr_enabled') && $this->config->get('integrations_easysocial_editprofile') && EB::easysocial()->exists()) { ?>
											<li>
												<a href="javascript:void(0);" data-gdpr-download-link>
													<?php echo JText::_('COM_EB_GDPR_DOWNLOAD_INFORMATION');?>
												</a>
											</li>
											<?php } ?>

										</ol>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>

<div class="si-components-nav">
	<?php if ($this->config->get('layout_latest')) { ?>
	<div class="si-components-nav__item <?php echo ($views->latest) ? 'is-active' : ''; ?>">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog');?>" class="si-components-nav__link">
			<span><i class="fa fa-home"></i></span>
		</a>
	</div>
	<?php } ?>

	<?php if ($this->config->get('layout_categories')) { ?>
	<div class="si-components-nav__item <?php echo $views->categories ? 'is-active' : '';?>">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=categories');?>" class="si-components-nav__link">
			<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CATEGORIES');?></span>
		</a>
	</div>
	<?php } ?>

	<?php if ($this->config->get('layout_tags')) { ?>
	<div class="si-components-nav__item <?php echo $views->tags ? 'is-active' : '';?>">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=tags');?>" class="si-components-nav__link">
			<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TAGS');?></span>
		</a>
	</div>
	<?php } ?>
	
	<?php if (!$this->isMobile()) { ?>
		<?php if ($this->config->get('layout_bloggers') && !$bloggerMode) { ?>
		<div class="si-components-nav__item <?php echo $views->blogger ? 'is-active' : '';?>">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger');?>" class="si-components-nav__link">
				<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_BLOGGERS');?></span>
			</a>
		</div>
		<?php } ?>

		<?php if ($this->config->get('layout_teamblog')) { ?>
		<div class="si-components-nav__item <?php echo $views->teamblog ? 'is-active' : '';?>">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=teamblog');?>" class="si-components-nav__link">
				<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAMBLOGS');?></span>
			</a>
		</div>
		<?php } ?>

		<?php if ($this->config->get('layout_archives')) { ?>
		<div class="si-components-nav__item <?php echo $views->archive ? 'is-active' : '';?>">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=archive');?>" class="si-components-nav__link">
				<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_ARCHIVES');?></span>
			</a>
		</div>
		<?php } ?>

		<?php if ($this->config->get('layout_calendar')) { ?>
		<div class="si-components-nav__item <?php echo $views->calendar ? 'is-active' : '';?>">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=calendar');?>" class="si-components-nav__link">
				<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CALENDAR');?></span>
			</a>
		</div>
		<?php } ?>
	<?php } ?>

	<?php if ($this->isMobile() && ($this->config->get('layout_bloggers') || $this->config->get('layout_teamblog') || $this->config->get('layout_archives') || $this->config->get('layout_calendar'))) { ?>
	<div class="si-components-nav__item <?php echo ($views->blogger || $views->teamblog || $views->archive || $views->calendar) ? 'is-active' : ''; ?>">
		<div class="btn-group">
			<a href="javascript:void(0);" class="si-components-nav__link dropdown-toggle_" data-toggle="dropdown">
				<span>More</span> &nbsp;
				<i class="i-chevron i-chevron--down"></i>
			</a>
			<ul class="dropdown-menu dropdown-menu-right">
				<?php if ($this->config->get('layout_bloggers') && !$bloggerMode) { ?>
				<li class="<?php echo $views->blogger ? 'is-active' : '';?>">
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger');?>" class="si-components-nav__dropdown-link">
						<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_BLOGGERS');?></span>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('layout_teamblog')) { ?>
				<li class="<?php echo $views->teamblog ? 'is-active' : '';?>">
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=teamblog');?>" class="si-components-nav__dropdown-link">
						<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAMBLOGS');?></span>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('layout_archives')) { ?>
				<li class="<?php echo $views->archive ? 'is-active' : '';?>">
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=archive');?>" class="si-components-nav__dropdown-link">
						<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_ARCHIVES');?></span>
					</a>
				</li>
				<?php } ?>

				<?php if ($this->config->get('layout_calendar')) { ?>
				<li class="<?php echo $views->calendar ? 'is-active' : '';?>">
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=calendar');?>" class="si-components-nav__dropdown-link">
						<span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CALENDAR');?></span>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php } ?>
</div>