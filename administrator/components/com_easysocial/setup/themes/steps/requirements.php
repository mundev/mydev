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

$gd = function_exists('gd_info');
$curl = is_callable('curl_init');

############################################
## MySQL info
############################################
$db = JFactory::getDBO();
$mysqlVersion = $db->getVersion();

############################################
## PHP info
############################################
$phpVersion = phpversion();
$uploadLimit = ini_get('upload_max_filesize');
$memoryLimit = ini_get('memory_limit');
$postSize = ini_get('post_max_size');
$magicQuotes = get_magic_quotes_gpc() && JVERSION > 3;

$postSize = 4;
$hasErrors = false;

if (stripos($memoryLimit, 'G') !== false) {
	list($memoryLimit) = explode('G', $memoryLimit);
	$memoryLimit = $memoryLimit * 1024;
}

if (!$gd || !$curl || $magicQuotes) {
	$hasErrors 	= true;
}

##########################################
## Paths
##########################################
$files = array();

$files['admin']	= new stdClass();
$files['admin']->path = JPATH_ROOT . '/administrator/components';

$files['site']	= new stdClass();
$files['site']->path = JPATH_ROOT . '/components';

$files['tmp'] = new stdClass();
$files['tmp']->path = JPATH_ROOT . '/tmp';

$files['media']	= new stdClass();
$files['media']->path 	= JPATH_ROOT . '/media';

$files['user']	= new stdClass();
$files['user']->path 	= JPATH_ROOT . '/plugins/user';

$files['system']	= new stdClass();
$files['system']->path 	= JPATH_ROOT . '/plugins/system';

$files['user']	= new stdClass();
$files['user']->path 	= JPATH_ROOT . '/plugins/user';

$files['auth']	= new stdClass();
$files['auth']->path 	= JPATH_ROOT . '/plugins/authentication';


##########################################
## Determine states
##########################################
$hasErrors	= false;

foreach ($files as $file) {
	// The only proper way to test this is to not use is_writable
	$contents = "<body></body>";
	$state = JFile::write($file->path . '/tmp.html', $contents);

	// Initialize this to false by default
	$file->writable = false;

	if ($state) {
		JFile::delete($file->path . '/tmp.html');

		$file->writable = true;
	}

	if (!$file->writable) {
		$hasErrors = true;
	}
}
?>
<script type="text/javascript">
jQuery(document).ready(function($){

	$('[data-installation-submit]' ).bind('click', function(){
		$('[data-installation-form]').submit();
	});

	<?php if ($hasErrors) { ?>
	$('[data-installation-submit]').hide();
	$('[data-installation-refresh]').removeClass('hide');

	// now we rebind the click.
	$('[data-installation-refresh]').on('click', function() {
		window.location.reload();
	});
	<?php } ?>
});
</script>
<form name="installation" method="post" data-installation-form>

<p>
Thank you for your recent purchase of <a href="https://stackideas.com/easysocial">EasySocial</a>! Before proceeding with the Installation, please ensure that these Requirement Dependencies are met. These are the Required Dependencies to ensure that EasySocial runs smoothly on your site.
</p>

<?php if (!$hasErrors) { ?>
<hr />
<p class="alert alert-success">Awesome! The minimum requirements are met. You may proceed with the installation process now.</p>
<?php } ?>

<div class="alert alert-error <?php echo $hasErrors ? '' : 'hide';?>" data-requirements-error>
	<p>Some of the requirements below are not met. Please ensure that all of the requirements below are met.</p>
</div>

<div class="requirements-table" data-system-requirements>
	<table class="table table-striped mt-20 stats">
		<thead>
			<tr>
				<td width="40%">
					<?php echo JText::_('Settings');?>
				</td>
				<td class="text-center" width="30%">
					<?php echo JText::_('Recommended');?>
				</td>
				<td class="text-center" width="30%">
					<?php echo JText::_('Current');?>
				</td>
			</tr>
		</thead>

		<tbody>
			<tr class="<?php echo version_compare($phpVersion, '5.3.10') == -1 ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span class="label label-info"><?php echo JText::_('PHP');?></span> PHP Version
						<i class="fa fa-help" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PHP_VERSION_TIPS' );?>" data-toggle="tooltip" data-placement="bottom"></i>

						<?php if (version_compare($phpVersion, '5.3.10') == -1) { ?>
						<a href="https://stackideas.com/docs/easysocial/administrators/welcome/getting-started" class="pull-right btn btn-es-danger btn-mini"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_FIX_THIS' );?></a>
						<?php } ?>
					</div>
				</td>
				<td class="text-center text-success">
					5.3.10 +
				</td>
				<td class="text-center text-<?php echo version_compare($phpVersion , '5.3.10' ) == -1 ? 'error' : 'success';?>">
					<?php echo $phpVersion;?>
				</td>
			</tr>
			<tr class="<?php echo !$gd ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span class="label label-info"><?php echo JText::_('PHP');?></span> GD Library
						<i class="fa fa-help" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PHP_GD_TIPS' );?>" data-toggle="tooltip" data-placement="bottom"></i>

						<?php if( !$gd ){ ?>
						<a href="https://stackideas.com/docs/easysocial/administrators/setup/gd-library" target="_blank" class="pull-right btn btn-es-danger btn-mini"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_FIX_THIS' );?></a>
						<?php } ?>
					</div>
				</td>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php if( $gd ){ ?>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php } else { ?>
				<td class="text-center text-error">
					<i class="icon-cancel-2"></i>
				</td>
				<?php } ?>
			</tr>

			<tr class="<?php echo !$curl ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span class="label label-info"><?php echo JText::_('PHP');?></span> CURL Library
						<i class="fa fa-help" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PHP_CURL_TIPS' );?>" data-toggle="tooltip" data-placement="bottom"></i>
						<?php if( !$curl ){ ?>
						<a href="https://stackideas.com/docs/easysocial/administrators/setup/curl-library" target="_blank" class="pull-right btn btn-es-danger btn-mini"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_FIX_THIS' );?></a>
						<?php } ?>
					</div>
				</td>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php if( $curl ){ ?>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php } else { ?>
				<td class="text-center text-error">
					<i class="icon-cancel-2"></i>
				</td>
				<?php } ?>
			</tr>
			<tr class="<?php echo $magicQuotes ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span class="label label-info"><?php echo JText::_('PHP');?></span> Magic Quotes GPC
						<i class="fa fa-help" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PHP_MAGICQUOTES_TIPS' );?>" data-toggle="tooltip" data-placement="bottom"></i>

						<?php if( $magicQuotes ){ ?>
						<a href="https://stackideas.com/docs/easysocial/administrators/setup/magic-quotes" target="_blank" class="pull-right btn btn-es-danger btn-mini"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_FIX_THIS' );?></a>
						<?php } ?>
					</div>
				</td>
				<td class="text-center text-success">
					<?php echo JText::_('Disabled');?>
				</td>
				<td class="text-center text-<?php echo $magicQuotes ? 'error' : 'success';?>">
					<?php if( !$magicQuotes ){ ?>
						<?php echo JText::_('Disabled');?>
					<?php } else { ?>
						<?php echo JText::_('Enabled');?>
					<?php } ?>
				</td>
			</tr>
			<tr class="<?php echo $memoryLimit < 64 ? 'error' : '';?>">
				<td>
					<span class="label label-info"><?php echo JText::_('PHP');?></span> memory_limit
					<i class="fa fa-help" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PHP_MEMORYLIMIT_TIPS' );?>" data-toggle="tooltip" data-placement="bottom"></i>
				</td>
				<td class="text-center text-success">
					64 <?php echo JText::_( 'M' );?>
				</td>
				<td class="text-center text-<?php echo $memoryLimit < 64 ? 'error' : 'success';?>">
					<?php echo $memoryLimit; ?>
				</td>
			</tr>
			<tr>
				<td>
					<span class="label label-success"><?php echo JText::_('MySQL');?></span> MySQL Version
					<i class="fa fa-help" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_INSTALLATION_MYSQL_VERSION_TIPS');?>" data-toggle="tooltip" data-placement="bottom"></i>
				</td>
				<td class="text-center text-success">
					5.0.4
				</td>
				<td class="text-center text-<?php echo !$mysqlVersion || version_compare( $mysqlVersion , '5.0.4' ) == -1 ? 'error' : 'success'; ?>">
					<?php echo !$mysqlVersion ? 'N/A' : $mysqlVersion;?>
				</td>
			</tr>
		</tbody>
	</table>
	
	<table class="table table-striped mt-20 stats">
		<thead>
			<tr>
				<td width="75%">
					<?php echo JText::_('Directory'); ?>
				</td>
				<td class="text-center" width="25%">
					<?php echo JText::_('State'); ?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($files as $file) { ?>
			<tr class="<?php echo !$file->writable ? 'text-error' : '';?>">
				<td>
					<?php echo $file->path;?>
				</td>

				<?php if ($file->writable) { ?>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php } else { ?>
				<td class="text-center text-error">
					<i class="icon-cancel-2"></i>&nbsp; <?php echo JText::_('Unwritable');?>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>

		</tbody>
	</table>

</div>

<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="active" value="<?php echo $active; ?>" />
</form>
