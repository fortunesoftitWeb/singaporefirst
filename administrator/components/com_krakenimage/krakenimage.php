<?php


defined('_JEXEC') or die;

$input  = JFactory::getApplication()->input;
$user   = JFactory::getUser();
$asset  = $input->get('asset');
$author = $input->get('author');

// Access check.
if (!$user->authorise('core.manage', 'com_krakenimage')
	&&	(!$asset or (
			 !$user->authorise('core.edit', $asset)
		&&	!$user->authorise('core.create', $asset)
		&& 	count($user->getAuthorisedCategories($asset, 'core.create')) == 0)
		&&	!($user->id == $author && $user->authorise('core.edit.own', $asset))))
{
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

$params = JComponentHelper::getParams('com_krakenimage');

// Load the helper class
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/media.php';


// import the kraken Image Plugin
JPluginHelper::importPlugin('plg_content_krakenimage');

// Set the path definitions
//$popup_upload = $input->get('pop_up', null);
$path = 'file_path';

$view = $input->get('view');
if (substr(strtolower($view), 0, 6) == 'images') // || $popup_upload == 1)
{
	$path = 'image_path';
}

// Get the media library location from the media library component
$mediaParams = JComponentHelper::getParams( 'com_media' );
define('COM_MEDIA_BASE',    JPATH_ROOT . '/' . $mediaParams->get($path, 'images'));
define('COM_MEDIA_BASEURL', JUri::root() . $mediaParams->get($path, 'images'));

$controller	= JControllerLegacy::getInstance('krakenImage', array('base_path' => JPATH_COMPONENT_ADMINISTRATOR));
$controller->execute($input->get('task'));
$controller->redirect();

