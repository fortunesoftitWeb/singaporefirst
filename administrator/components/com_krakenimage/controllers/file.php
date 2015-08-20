<?php


defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');


class KrakenImageControllerFile extends JControllerLegacy
{
	
	protected $folder = '';

   
	public function reduce()
	{
        $app = JFactory::getApplication();
        
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

        // check and set this for Joomla 2.5
        if(!isset($this->input)){
            $this->input = JFactory::getApplication()->input;
        }

		// Get some data from the request
		$tmpl	= $this->input->get('tmpl');
		$paths	= $this->input->get('rd', array(), 'array');
		$folder = $this->input->get('folder', '', 'path');

		$redirect = 'index.php?option=com_krakenimage&folder=' . $folder;

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}

		$this->setRedirect($redirect);

		// Nothing to reduce
		if (empty($paths))
		{
			return true;
		}

		// Authorize the user
		if (!$this->authoriseUser('edit'))
		{
			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		JPluginHelper::importPlugin('content');
        // Set the right dispatcher for version of Joomla
        $jver = new JVersion;
        if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
            $dispatcher	= JDispatcher::getInstance();
        }
        else {
            $dispatcher	= JEventDispatcher::getInstance();
        }

		$ret = true;

		foreach ($paths as $path)
		{
			if ($path !== JFile::makeSafe($path))
			{
				$path = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
			}

			$fullPath = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
			if (is_file($fullPath))
			{
                $this->reduceFile($dispatcher, $fullPath);
            }
			elseif (is_dir($fullPath))
			{
                $relPath = MediaHelper::getRelativeMediaPath( $fullPath, false );
                $app->enqueueMessage(JText::sprintf('COM_KRAKENIMAGE_REDUCE_FOLDER', $relPath), 'Message');
                $contents = JFolder::files($fullPath, '.', true, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
                if (!empty($contents))
                {
                    foreach ($contents as $fullPath)
                    {
                        if (is_file($fullPath))
                        {
                            $this->reduceFile($dispatcher, $fullPath);
                        }           
                    }
                }                    
                else
				{
					JError::raiseWarning(100, JText::sprintf('COM_KRAKENIMAGE_ERROR_UNABLE_TO_REDUCE_FOLDER_EMPTY', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
				}
			}
		}
            
        $this->setMessage(JText::sprintf('COM_KRAKENIMAGE_REDUCE_COMPLETE'));
        
		return $ret;
    }
    
    public function reduceFile($dispatcher, $fullPath)
	{
        $filesize = filesize($fullPath);
        $object_file = new JObject(array('filepath' => $fullPath, 'tmp_name' => $fullPath, 'size' => $filesize));

		// Trigger the onkrakenImage event.
        $result = $dispatcher->trigger('onkrakenImageReduce', array('com_krakenimage.file', &$object_file, false, false));
        if (in_array(false, $result, true))
        {
            // There are some errors in the kraken Image plugin
            JError::raiseWarning(100, JText::plural('COM_KRAKENIMAGE_ERROR_REDUCE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
            continue;
        }
        
		return true;
    }

    
	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param   string   $action - the action to be peformed (create or delete)
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	protected function authoriseUser($action)
	{
		if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_krakenimage'))
		{
			// User is not authorised
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));
			return false;
		}

		return true;
	}

}
