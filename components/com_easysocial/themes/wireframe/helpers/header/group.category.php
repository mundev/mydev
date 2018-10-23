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
<div data-header>
	<div class="es-cat-header">
		<div class="es-cat-header__hd">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<a href="<?php echo $category->getPermalink();?>" class="o-avatar es-cat-header__avatar">
						<img src="<?php echo $category->getAvatar();?>" alt="<?php echo $this->html('string.escape', $category->getTitle());?>">
					</a>    
				</div>
				<div class="o-flag__body">
					<div class="es-cat-header__hd-content-wrap">
						
						<div class="es-cat-header__hd-content">
							<a href="<?php echo $category->getPermalink();?>" class="es-cat-header__title-link"><?php echo $this->html('string.escape', $category->getTitle());?></a>
							<div class="es-mini-header__desc">
								<?php echo $this->html('string.truncate', $category->description, 200, '', false, false);?>
							</div>
						</div>

						<div class="es-cat-header__hd-action">
							<div class="btn-group pull-right" role="group">
								<button type="button" class="btn btn-es-default-o btn-xs dropdown-toggle_" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									 <i class="fa fa-ellipsis-h"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									 <li><a href="#">Unfriend</a></li>
									 <li><a href="#">action</a></li>
								</ul>
							 </div> 
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="es-cat-header__bd">
			<div class="es-cat-header__graph">
				[chart placeholder]
			</div>
		</div>
		
		<div class="es-cat-header__ft">
			<div class="pull-left">
				<ul class="g-list-inline g-list-inline--space-right">
					<li>
						<i class="fa fa-users"></i> <b>4</b> Group
					</li>
					<li>
						<i class="fa fa-photo"></i> <b>47</b> Photos
					</li>
					<li>
						<i class="fa fa-eye"></i> <b>47</b> Hits
					</li>
				</ul>
			</div>
			
			<a class="btn btn-es-primary btn-sm pull-right" href="">
				+ Create new group in Automobile
			</a>
		</div>
	</div>
</div>