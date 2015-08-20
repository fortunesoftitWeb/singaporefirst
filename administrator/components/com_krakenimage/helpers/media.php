<?php


defined('_JEXEC') or die;


abstract class MediaHelper
{

	/**
	 * Calculate the size of a resized image
	 *
	 * @param   integer  $width   Image width
	 * @param   integer  $height  Image height
	 * @param   integer  $target  Target size
	 *
	 * @return  array  The new width and height
	 *
	 * @since   1.0
	 */
	public static function imageResize($width, $height, $target)
	{   
        $jver = new JVersion;
        if ( substr($jver->getShortVersion(), 0, 3) != '2.5' ) {
            $mediaHelper = new JHelperMedia;
            return $mediaHelper->imageResize($width, $height, $target);
        }
        else
        {
            //takes the larger size of the width and height and applies the
            //formula accordingly...this is so this script will work
            //dynamically with any size image
            if ($width > $height) {
                $percentage = ($target / $width);
            } else {
                $percentage = ($target / $height);
            }

            //gets the new value and applies the percentage, then rounds the value
            $width = round($width * $percentage);
            $height = round($height * $percentage);

            return array($width, $height);
        }
	}

	/**
	 * Counts the files and directories in a directory that are not php or html files.
	 *
	 * @param   string  $dir  Directory name
	 *
	 * @return  array  The number of files and directories in the given directory
	 *
	 * @since   1.0
	 */
	public static function countFiles($dir)
	{
        $jver = new JVersion;
        if ( substr($jver->getShortVersion(), 0, 3) != '2.5' ) {
            $mediaHelper = new JHelperMedia;
            return $mediaHelper->countFiles($dir);
        }
        else {
            $total_file = 0;
            $total_dir = 0;

            if (is_dir($dir)) {
                $d = dir($dir);

                while (false !== ($entry = $d->read())) {
                    if (substr($entry, 0, 1) != '.' && is_file($dir . DIRECTORY_SEPARATOR . $entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
                        $total_file++;
                    }
                    if (substr($entry, 0, 1) != '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
                        $total_dir++;
                    }
                }
                $d->close();
            }
            return array ($total_file, $total_dir);
        }
	}

    // Determine if the image type is handled by kraken Image.
    // Currently we handle JPG, PNG, and GIF.
    public static function file_is_handled_by_image_service( $file )
    {
        $ext = strtolower(JFile::getExt($file));
		switch ($ext)
		{
			// Image
			case 'jpg':
            case 'jpeg':
			case 'png':
			case 'gif':
                return true;
            default:
                return false;
        }						
    }
        
	/**
	 * Gets the relative Media Path.
	 *
	 * @param   string  $filePath  Directory name
	 *
	 * @return  string  The relative path in the media library path
	 *
	 */    
    public static function getRelativeMediaPath($filePath, $requireLeadingDirSeparator=true)
    {
        $com_media_base = COM_MEDIA_BASE;
        if (!$requireLeadingDirSeparator)
            $com_media_base .= '/';
        $mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', $com_media_base);
        $relativePath = str_replace($mediaBase, '', $filePath);
        return $relativePath;
    }

    // Check if kraken Image Plugin is installed and enabled
    // Plugin is not installed if the return value is null
    // Otherwise, the enabled status of the kraken Image Plugin
    public static function iskrakenImagePluginEnabled()
    {
        // Check if kraken Image Plugin is installed and enabled
        $plugin = 'plg_content_krakenimage';
        $db = JFactory::getDbo();
        $dbTable = '#__extensions';
        $conditions = array( $db->quoteName('name') . ' = ' . $db->quote($plugin) ); 
        $query = $db->getQuery(true);
        $query->select($db->quoteName('enabled'))
              ->from($db->quoteName($dbTable))
              ->where($conditions);
        $db->setQuery((string)$query);
        $is_enabled = $db->loadResult();
        
        return $is_enabled;
    }
    
    
	public static function getstatus()
	{
		$db = JFactory::getDbo();
		$query = "  SELECT   *
					FROM   `qqclj_medialibrary`
					ORDER BY id DESC
					LIMIT    0, 1 ";
					
		$db->setQuery($query);
		$result = $db->loadAssoc();
		$res = $result['active'];
		
		return $res;
	}

}
