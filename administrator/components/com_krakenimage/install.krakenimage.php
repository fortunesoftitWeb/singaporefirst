<?php
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Script file of KrakenImage component
 */
class com_krakenimageInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL('index.php?option=com_krakenimage');
		
		$this->updateDatabase( );
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		// $parent is the class calling this method
		$this->updateDatabase( );
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
	}
	
	function updateDatabase( )
	{
		// Under Windows, filePaths were being stored as complete paths and not relative paths.
		// This routine puts those records in the proper format.
		$app = JFactory::getApplication();		

	     // Get the database
   		$db = JFactory::getDBO();
        if (defined('IMAGE_SERVICE_DATABASE'))
            $dbTableName = IMAGE_SERVICE_DATABASE;
        else
            $dbTableName = '#__krakenimage';

		// Check first if the krakenimage table exists
		$prefix = $app->getCfg('dbprefix');	
		$tables = $db->getTableList();
		if (!in_array(str_replace('#__', $prefix, $dbTableName), $tables))
			return;
		
		// search for any files with a backslash, could've happened under Windows
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName($dbTableName));
		$query->where($db->quoteName('filePath') . ' LIKE ' . $db->quote('%\\\\%'));
		$db->setQuery((string)$query);
		$db->query();
		
		$num_rows = $db->getNumRows( );
		if ($num_rows > 0)
		{			
			//$app->enqueueMessage(JText::sprintf('COM_KRAKENIMAGE_DATABASE_INSTALLUPDATE', $num_rows), 'Message');
			$app->enqueueMessage(JText::sprintf('Updating %d records in the kraken Image database.', $num_rows), 'Message');
		
			$fileObjs = $db->loadObjectList( );
			foreach( $fileObjs as $fileObj )
			{
				// Update filePath
				$fileObj->filePath = $this->getRelativeMediaPath(str_replace("\\", '/', $fileObj->filePath));	
				$db->updateObject($dbTableName, $fileObj, 'id', false);
			}
		}
	}

	function getRelativeMediaPath($filePath, $requireLeadingDirSeparator=true)
    {
		$mediaParams = JComponentHelper::getParams( 'com_media' );
        $com_media_base = JPATH_ROOT . '/' . $mediaParams->get('image_path', 'images');
        if (!$requireLeadingDirSeparator)
            $com_media_base .= '/';
        $mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', $com_media_base);
        $relativePath = str_replace($mediaBase, '', $filePath);
        return $relativePath;
    }
    
}