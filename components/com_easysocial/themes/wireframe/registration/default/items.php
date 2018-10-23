<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<li>
	<div class="es-card">
		<div class="es-card__bd">
			<div class="o-flag" data-behavior="sample_code">
				<div class="o-flag__image o-flag--top t-lg-pr--lg">
					<?php if ($this->config->get('registrations.layout.avatar')) { ?>
					<span class="o-avatar o-avatar--lg">
						<a href="<?php echo ESR::registration(array('controller' => 'registration', 'task' => 'selectType' , 'profile_id' => $profile->id));?>">
							<img src="<?php echo $profile->getAvatar(SOCIAL_AVATAR_LARGE);?>" title="<?php echo $this->html('string.escape', $profile->getTitle());?>" />
						</a>
					</span>
					<?php } ?>
				</div>
				<div class="o-flag__body">
					<b class=" t-mb--sm"><a href="<?php echo ESR::registration(array('controller' => 'registration', 'task' => 'selectType', 'profile_id' => $profile->id));?>"><?php echo $profile->get('title');?></a></b>
					<div class=" t-mb--sm"><?php echo $profile->get('description');?></div>

					<?php if ($profile->getRegistrationType() == 'approvals' || $profile->getRegistrationType() == 'verify' || $profile->getRegistrationType() == 'confirmation_approval') { ?>
						<div>* <?php echo $profile->getRegistrationType(SOCIAL_TRANSLATE_REGISTRATION); ?></div>
					<?php } ?>

					<?php if ($profile->getMembersCount() && $this->config->get('registrations.layout.users')) { ?>
					<div class="profile-members">
						<div>
							<hr />
							<div class="list-profile-type-peep t-fs--sm">
								<?php echo JText::sprintf('COM_EASYSOCIAL_REGISTRATIONS_OTHER_PROFILE_MEMBERS', $profile->getMembersCount());?>
							</div>

							<?php if ($profile->users) { ?>
							<ul class="g-list-inline profile-users">
								<?php foreach ($profile->users as $user ){ ?>
								<li data-es-provide="tooltip" data-original-title="<?php echo $this->html( 'string.escape' , $user->getName() );?>" class="t-lg-mb--md t-lg-mr--md">
									<?php echo $this->html('avatar.user', $user, 'sm', true, false); ?>
								</li>
								<?php } ?>
							</ul>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="es-card__ft es-card--border">
			<a href="<?php echo FRoute::registration( array( 'controller' => 'registration' , 'task' => 'selectType' , 'profile_id' => $profile->id ) );?>" class="btn btn-es-primary pull-right">
				<?php echo JText::_( 'COM_EASYSOCIAL_JOIN_NOW_BUTTON' ); ?>
			</a>
		</div>
	</div>
</li>