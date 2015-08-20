<?php


defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');


class KrakenImageModelList extends JModelLegacy
{
	public function getState($property = null, $default = null)
	{
		static $set;

		if (!$set)
		{
			$input  = JFactory::getApplication()->input;
			$folder = $input->get('folder', '', 'path');
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	public function getImages()
	{
		$list = $this->getList();

		return $list['images'];
	}

	public function getFolders()
	{
		$list = $this->getList();

		return $list['folders'];
	}

	public function getDocuments()
	{
		$list = $this->getList();

		return $list['docs'];
	}

	/**
	 * Build imagelist
	 *
	 * @param string $listFolder The image directory to display
	 * @since 1.0
	 */
	public function getList()
	{
		static $list;
		static $useAlternativeDBCall = false;
		
		// Only process the list once per request
		if (is_array($list))
		{
			return $list;
		}

		// Get current path from request
		$current = $this->getState('folder');

		// If undefined, set to empty
		if ($current == 'undefined')
		{
			$current = '';
		}

		if (strlen($current) > 0)
		{
			$basePath = COM_MEDIA_BASE.'/'.$current;
		}
		else
		{
			$basePath = COM_MEDIA_BASE;
		}

		$mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', COM_MEDIA_BASE.'/');

		$images		= array ();
		$folders	= array ();
		$docs		= array ();

		$fileList = false;
		$folderList = false;
		if (file_exists($basePath))
		{
			// Get the list of files and folders from the given folder
			$fileList	= JFolder::files($basePath);
			$folderList = JFolder::folders($basePath);
		}

        // Get the database
   		$db = JFactory::getDBO();
        if (defined('IMAGE_SERVICE_DATABASE'))
            $dbTableName = IMAGE_SERVICE_DATABASE;
        else
            $dbTableName = '#__krakenimage';
        
		if (!$useAlternativeDBCall)
		{
			try
			{
				// This query returns all the files processed from a directory
				$validFilenameChars = '[A-Za-z0-9\^\&\'\@\{\}\}\,\$\=\!\#\(\)\.\%\+\~\_[.hyphen.][.left-square-bracket.][.right-square-bracket.] ]+$';
				$dbFolderSearch = '^' . str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean('/' . $current . '/')) . $validFilenameChars;
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from($db->quoteName($dbTableName));
				$query->where($db->quoteName('filePath') . ' REGEXP ' . $db->quote($dbFolderSearch));
				$db->setQuery((string)$query);
				$db->query();
			}
			catch (Exception $e)
			{
				$useAlternativeDBCall = true;
			}
		} 
		
		if ($useAlternativeDBCall)
		{
			$params = JComponentHelper::getParams('com_krakenimage');
			$debug = ($params->get('pi_debug') != 0);
			if ($debug)
			{
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_KRAKENIMAGE_DATABASE_ALTCALL'), 'Message');
			}
			// returns more records but doesn't use regular expressions
			$dbFolderSearch = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean('/' . $current . '/')) .'%';
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName($dbTableName));
			$query->where($db->quoteName('filePath') . ' LIKE ' . $db->quote($dbFolderSearch) );
			$db->setQuery((string)$query);
			$db->query();	
		}
		
		$num_rows = $db->getNumRows( );
		$fileRows = $db->loadObjectList('filePath');
        
		// Iterate over the files if they exist
		if ($fileList !== false)
		{
			foreach ($fileList as $file)
			{
				if (is_file($basePath.'/'.$file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html' && MediaHelper::file_is_handled_by_image_service($file))
				{
					$tmp = new JObject;
					$tmp->name = $file;
					$tmp->title = $file;
					$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $file));
					$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);

					$tmp->size = filesize($tmp->path);
                    $tmp->originalSize = $tmp->size;
                                   
                    // check to see if the file is in the database
                    $fileRow = null;
                    $fileKey = '/'.$tmp->path_relative;
                    if ($num_rows != 0 && array_key_exists($fileKey, $fileRows))
                        $fileRow = $fileRows[$fileKey];
                    if ($fileRow)
                    {
                        // record exists
                        // set the original file size
                        $tmp->originalSize = $fileRow->originalFileSize;
                        $tmp->processed = true;
                        $tmp->status = $fileRow->lastReduceStatus;
                    }       
                    else
                    {
                        // record does not exist
                        $tmp->processed = false;
                    }
                    

					$ext = strtolower(JFile::getExt($file));
					switch ($ext)
					{
						// Image
						case 'jpg':
						case 'png':
						case 'gif':
						case 'xcf':
						case 'odg':
						case 'bmp':
						case 'jpeg':
						case 'ico':
							$info = @getimagesize($tmp->path);
							$tmp->width		= @$info[0];
							$tmp->height	= @$info[1];
							$tmp->type		= @$info[2];
							$tmp->mime		= @$info['mime'];

							if (($info[0] > 60) || ($info[1] > 60))
							{
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 60);
								$tmp->width_60 = $dimensions[0];
								$tmp->height_60 = $dimensions[1];
							}
							else {
								$tmp->width_60 = $tmp->width;
								$tmp->height_60 = $tmp->height;
							}

							if (($info[0] > 16) || ($info[1] > 16))
							{
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 16);
								$tmp->width_16 = $dimensions[0];
								$tmp->height_16 = $dimensions[1];
							}
							else {
								$tmp->width_16 = $tmp->width;
								$tmp->height_16 = $tmp->height;
							}

                            $images[] = $tmp;
							break;

						// Non-image document
						default:
							$tmp->icon_32 = "media/mime-icon-32/".$ext.".png";
							$tmp->icon_16 = "media/mime-icon-16/".$ext.".png";
							$docs[] = $tmp;
							break;
					}
				}
			}
		}

		// Iterate over the folders if they exist
		if ($folderList !== false)
		{
			foreach ($folderList as $folder)
			{
				$tmp = new JObject;
				$tmp->name = basename($folder);
				$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $folder));
				$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
				$count = MediaHelper::countFiles($tmp->path);
				$tmp->files = $count[0];
				$tmp->folders = $count[1];

				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'docs' => $docs, 'images' => $images);

		return $list;
	}
}
