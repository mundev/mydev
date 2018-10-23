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
<div id="eb" class="eb-mod mod-easyblogshowcase-magazine st-2 mod-easyblogshowcase<?php echo $modules->getWrapperClass(); ?> <?php echo $modules->isMobile() ? 'is-mobile' : '';?>" data-eb-module-showcase>
	<div class="eb-gallery" data-autoplay="<?php echo $autoplay; ?>" data-interval="<?php echo $autoplayInterval; ?>">
		<div class="eb-gallery-stage">
			<div class="eb-gallery-viewport">
				<?php foreach ($posts as $post) { ?><div class="eb-gallery-item ">
					<div class="eb-gallery-box">
						<?php if ($params->get('photo_show', true)) { ?>
							<?php if ($post->postCover) { ?>
								<div class="eb-gallery-thumb eb-mod-thumb">
									<a href="<?php echo $post->getPermalink(); ?>" class="eb-gallery-cover__img"
									style="
										background-image: url('<?php echo $post->postCover;?>') !important;
										background-size: cover;
										background-repeat: no-repeat;
										background-position: 50% 50%;
										padding-bottom: 400px;"
									></a>
								</div>
							<?php } ?>
						<?php } ?>
						<div class="eb-gallery-content <?php echo $params->get('photo_show', true) ? '' : ' no-cover'; ?>">
							<?php if ($params->get('authoravatar', true)) { ?>
								<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="mod-avatar mb-10">
									<img src="<?php echo $post->getAuthor()->getAvatar(); ?>" alt="<?php echo $post->getAuthor()->getName(); ?>" class="mod-avatar--rounded">
								</a>
							<?php } ?>
							<a href="<?php echo $post->getPermalink();?>">
								<h2 class="eb-gallery-content__title"><?php echo $post->title;?></h2>
							</a>
							<div class="eb-gallery-content__article">
								<span>
									<?php echo $post->content; ?>
								</span>
							</div>
							<div class="eb-gallery-content__meta eb-gallery-content__meta--text">
								<?php if ($params->get('contentauthor', true)) { ?>
									<div class="eb-gallery-author">
										<span>
											<?php echo ucfirst(JText::_('MOD_SHOWCASE_BY')); ?><a href="<?php echo $post->getAuthor()->getProfileLink(); ?>"><?php echo $post->getAuthor()->getName(); ?></a>
										</span>
									</div>
								<?php } ?>

								<div class="eb-gallery-category">
									<span>
										<?php echo JText::_('MOD_SHOWCASE_POSTED_IN'); ?> <a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo JText::_($post->getPrimaryCategory()->title);?></a>

										<?php if ($params->get('contentdate' , true)) { ?>
											<?php echo JText::_('MOD_SHOWCASE_ON'); ?>
											<?php echo $post->getCreationDate()->format($params->get('dateformat', JText::_('DATE_FORMAT_LC3'))); ?>
										<?php } ?>
									</span>
								</div>

								<?php if ($params->get('showratings', true)) { ?>
								<div class="eb-gallery-rating">
									<span>
										<?php echo EB::ratings()->html($post, 'ebmostshowcase-' . $post->id . '-ratings', JText::_('MOD_SHOWCASE_RATE_BLOG_ENTRY'), $disabled); ?>
									</span>
								</div>
								<?php } ?>
							</div>
							<div class="eb-gallery-content__more">
								<?php if ($params->get('showreadmore', true)) { ?>
									<a href="<?php echo $post->getPermalink();?>" class="mod-btn mod-btn-more"><?php echo JText::_('MOD_SHOWCASE_READ_MORE');?></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div><?php } ?>
			</div>
			<?php if (count($posts) > 1) { ?>
				<div class="eb-gallery-indicators">
					<ol class="eb-gallery-buttons">
						<?php $i = 0; ?>
						<?php foreach ($posts as $post) { ?>
							<li class="eb-gallery-menu-item <?php echo $i ==0 ? 'active' : ''; ?>"></li>
							<?php $i++; ?>
						<?php } ?>
					</ol>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<?php include_once(__DIR__ . '/default_scripts.php'); ?>