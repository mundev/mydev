<?php
/**
* @package		EasySocial
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
<div class="row form-horizontal">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAP_FEATURES', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAP_FEATURES_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_locations', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_ENABLE_LOCATION'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER', 'location_service_provider'); ?>

					<div class="col-md-7">
						<select name="location_service_provider" id="location_service_provider" class="form-control" data-location-integration>
							<option value="maps"<?php echo $this->config->get('location_service_provider' ) == 'maps' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEMAPS' ); ?></option>
							<option value="places"<?php echo $this->config->get('location_service_provider' ) == 'places' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEPLACES' ); ?></option>]
							<option value="foursquare"<?php echo $this->config->get('location_service_provider' ) == 'foursquare' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_FOURSQUARE' ); ?></option>
						</select>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_locations_static_maps', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_USE_STATIC_MAPS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_LANGUAGE_CODE', 'main_locations_blog_language'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<input type="text" name="main_locations_blog_language" id="main_locations_blog_language" class="form-control text-center" value="<?php echo $this->config->get('main_locations_blog_language' );?>" size="3" style="width: auto" />
							<a class="btn btn-default" href="https://developers.google.com/maps/faq#languagesupport" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LANGUAGE_CODE_REFERENCE');?></a>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_SIZE_HEIGHT', 'main_locations_blog_map_height'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="main_locations_blog_map_height" id="main_locations_blog_map_height" class="form-control text-center" value="<?php echo $this->config->get('main_locations_blog_map_height');?>" />
									<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_TYPE', 'main_locations_map_type'); ?>

					<div class="col-md-7">
						<select name="main_locations_map_type" id="main_locations_map_type" class="form-control">
							<option value="ROADMAP"<?php echo $this->config->get( 'main_locations_map_type' ) == 'ROADMAP' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_ROADMAP' ); ?></option>
							<option value="SATELLITE"<?php echo $this->config->get( 'main_locations_map_type' ) == 'SATELLITE' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_SATELLITE' ); ?></option>
							<option value="HYBRID"<?php echo $this->config->get( 'main_locations_map_type' ) == 'HYBRID' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_HYBRID' ); ?></option>
							<option value="TERRAIN"<?php echo $this->config->get( 'main_locations_map_type' ) == 'TERRAIN' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_TERRAIN' ); ?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_DEFAULT_ZOOM_LEVEL', 'main_locations_default_zoom_level'); ?>

					<div class="col-md-7">
						<input type="text" name="main_locations_default_zoom_level" class="form-control input-mini text-center" value="<?php echo $this->config->get('main_locations_default_zoom_level');?>" />
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAX_ZOOM_LEVEL', 'main_locations_max_zoom_level'); ?>

					<div class="col-md-7">
						<input type="text" name="main_locations_max_zoom_level" id="main_locations_max_zoom_level" class="form-control input-mini text-center" value="<?php echo $this->config->get('main_locations_max_zoom_level');?>" />
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MIN_ZOOM_LEVEL', 'main_locations_min_zoom_level'); ?>

					<div class="col-md-7">
						<input type="text" name="main_locations_min_zoom_level" id="main_locations_min_zoom_level" class="form-control input-mini text-center" value="<?php echo $this->config->get('main_locations_min_zoom_level');?>" />
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel<?php echo $this->config->get('location_service_provider') != 'foursquare' ? ' hide' : '';?>" data-panel-foursquare data-panel-integration>
			
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_FOURSQUARE'); ?>

			<div class="panel-body">
				<div class="row">
					<div class="col-md-9">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_FOURSQUARE_INFO');?>
					</div>
					<div class="col-md-3 text-right">
						<a href="https://developer.foursquare.com/" target="_blank" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_FOURSQUARE_CREATE_APP');?></a>
					</div>
				</div>
				
				<div class="form-group mt-10">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LOCATIONS_FOURSQUARE_CLIENT_ID', 'foursquare_client_id'); ?>

					<div class="col-md-7">
						<input type="text" name="foursquare_client_id" id="foursquare_client_id" class="form-control" value="<?php echo $this->config->get('foursquare_client_id');?>" size="60" />
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LOCATIONS_FOURSQUARE_CLIENT_SECRET', 'foursquare_client_secret'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'foursquare_client_secret', $this->config->get('foursquare_client_secret')); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel<?php echo $this->config->get('location_service_provider') != 'maps' ? ' hide' : '';?>" data-panel-maps data-panel-integration>
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEMAPS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY', 'googlemaps_api_key'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="googlemaps_api_key" id="googlemaps_api_key" class="form-control" value="<?php echo $this->config->get('googlemaps_api_key');?>" />
							<span class="input-group-btn">
								<a href="https://stackideas.com/docs/easyblog/administrators/configuration/location-services-settings" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-md-5"></label>

					<div class="col-md-7">
						<div class="small mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY_NOTE');?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel<?php echo $this->config->get('location_service_provider') != 'places' ? ' hide' : '';?>" data-panel-places data-panel-integration>
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEPLACES'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY', 'googleplaces_api_key'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="googleplaces_api_key" id="googleplaces_api_key" class="form-control" value="<?php echo $this->config->get('googleplaces_api_key');?>" />
							<span class="input-group-btn">
								<a href="https://stackideas.com/docs/easyblog/administrators/configuration/location-services-settings" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-md-5"></label>

					<div class="col-md-7">
						<div class="small mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY_NOTE');?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
