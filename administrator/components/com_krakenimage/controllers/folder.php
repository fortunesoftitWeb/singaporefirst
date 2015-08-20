<?php


defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');


class KrakenImageControllerFolder extends JControllerLegacy
{

   
	public function reduce()
	{
        $app = JFactory::getApplication();
        
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$user	= JFactory::getUser();

        // check and set this for Joomla 2.5
        if(!isset($this->input)){
            $this->input = JFactory::getApplication()->input;
        }
                
		// Get some data from the request
		$tmpl   = $this->input->get('tmpl');
		$paths  = $this->input->get('rd', array(), 'array');
		$folder = $this->input->get('folder', '', 'path');

		$redirect = 'index.php?option=com_krakenimage&folder=' . $folder;

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}

		$this->setRedirect($redirect);

		// Just return if there's nothing to do
		if (empty($paths))
		{
			return true;
		}

		if (!$user->authorise('core.edit', 'com_krakenimage'))
		{
			// User is not authorised to edit
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));

			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		$ret = true;
        
   		JPluginHelper::importPlugin('content');
        // Set the right dispatcher for version of Joomla
        $jver = new JVersion;
        if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
            $dispatcher	= JDispatcher::getInstance();
        }
        else {
            $dispatcher	= JEventDispatcher::getInstance();
        }

		if (count($paths))
		{
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
		}

		return $ret;
    }
    
	public function reduceall()
	{
		
		
		$app = JFactory::getApplication();
        
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$user	= JFactory::getUser();

        // check and set this for Joomla 2.5
        if(!isset($this->input)){
            $this->input = JFactory::getApplication()->input;
        }
                
		// Get some data from the request
		$tmpl   = $this->input->get('tmpl');
		$folder = $this->input->get('folder', '', 'path');
        
		$redirect = 'index.php?option=com_krakenimage&folder=' . $folder;

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}

		$this->setRedirect($redirect);

		$paths  = JFolder::files(COM_MEDIA_BASE, '.', true, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
        
		// Just return if there's nothing to do
		if (empty($paths))
		{
			return true;
		}

		if (!$user->authorise('core.edit', 'com_krakenimage'))
		{
			// User is not authorised to edit
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		$ret = true;
        
   		JPluginHelper::importPlugin('content');
   		
        // Set the right dispatcher for version of Joomla
        $jver = new JVersion;
        if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
            $dispatcher	= JDispatcher::getInstance();
        }
        else {
            $dispatcher	= JEventDispatcher::getInstance();
        }

		if (count($paths))
		{
			foreach ($paths as $path)
			{
                $fullPath = $path;
				if (is_file($fullPath))
				{
                    $this->reduceFile($dispatcher, $fullPath);
				}
			}
            
            $this->setMessage(JText::sprintf('COM_KRAKENIMAGE_REDUCE_COMPLETE'));
		}
        
        // Disable the Reduce Media Library Button so the user has to explicity reset it in the configuration
        $this->disableReduceMediaLibraryButton();
        
		return $ret;
		
    }
    
    public function reducemedialibrary()
	{
		
		$app = JFactory::getApplication();
        
		//JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$user	= JFactory::getUser();

        // check and set this for Joomla 2.5
        if(!isset($this->input)){
           $this->input = JFactory::getApplication()->input;
        }
                
		//Get some data from the request
		$tmpl   = $this->input->get('tmpl');
		$folder = $this->input->get('folder', '', 'path');
        
		$redirect = 'index.php?option=com_krakenimage&folder='.$folder;

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}

		$this->setRedirect($redirect);

		$paths  = JFolder::files(COM_MEDIA_BASE, '.', true, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
		
		$total  	= count($paths);
		$start_date = date('Y-m-d');
		$end_date	= date('Y-m-d');
		$process 	= '';
		$active 	= '';
		
		
		/*$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__medialibrary'));
		$db->setQuery($query);
		$result = $db->execute(); */
	
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$columns = array('total', 'start_date', 'end_date','process','active');   
		$values = array($db->quote($total), $db->quote($start_date),$db->quote($end_date),0,1);

		$query->insert($db->quoteName('#__medialibrary'))
		->columns($db->quoteName($columns))
		->values(implode(',', $values));

		$db->setQuery($query);
		$db->query();

		$addressID = $db->insertid();
	
		// Just return if there's nothing to do
		if (empty($paths))
		{
			return true;
		}

		if (!$user->authorise('core.edit', 'com_krakenimage'))
		{
			// User is not authorised to edit
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		$ret = true;
        
   		JPluginHelper::importPlugin('content');
   		
        // Set the right dispatcher for version of Joomla
        $jver = new JVersion;
        if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ){
            $dispatcher	= JDispatcher::getInstance();
        }
        else{
            $dispatcher	= JEventDispatcher::getInstance();
        }

		if(count($paths))
		{
			foreach ($paths as $path)
			{
                $fullPath = $path;
				if (is_file($fullPath))
				{
                    $this->reduceFile($dispatcher, $fullPath,$addressID);
				}
			}
            
            $this->setMessage(JText::sprintf('COM_KRAKENIMAGE_REDUCE_COMPLETE'));
		}
        
      
        // Disable the Reduce Media Library Button so the user has to explicity reset it in the configuration
        $this->disableReduceMediaLibraryButton();
        
        $query =  "update `qqclj_medialibrary` set active = 0 where id = $addressID ";
		$db->setQuery($query);
		$result = $db->execute();
		
		$reponse['msg'] 			= 'Optimization Succusfully Completed';
		
		echo json_encode($reponse);	
		exit;
    }

	public function medialibrary()
	{
		
		$db = JFactory::getDbo();
		
		$query = "  SELECT   *
					FROM   `qqclj_medialibrary`
					ORDER BY id DESC
					LIMIT    0, 1 ";
					
		$db->setQuery($query);
		$result = $db->loadAssoc();

		if(isset($result['active']) && $result['active'] == 1 && $result['process'] < $result['total']){
			$reponse['active']  		= 1;
			$reponse['msg'] 			= 'Optimized '.$result['process'].' of '.$result['total'].' images';
			$reponse['process'] 			= $result['process'];
			$reponse['total'] 			= $result['total'];
		}else{
			$reponse['active']  		= 0;
			$reponse['msg'] 			= 'Optimization Succusfully Completed';
			$reponse['process'] 			= $result['process'];
			$reponse['total'] 			= $result['total'];
		}		
		
		echo json_encode($reponse);	
		exit;
	}	
	
    public function reduceFile($dispatcher, $fullPath,$id= null)
	{
        $filesize 	 = filesize($fullPath);
        $object_file = new JObject(array('filepath' => $fullPath, 'tmp_name' => $fullPath, 'size' => $filesize));

		// Trigger the onkrakenImage event.
        $result = $dispatcher->trigger('onkrakenImageReduce', array('com_krakenimage.file', &$object_file, false, false,$id));
        if(in_array(false, $result, true))
        {
            // There are some errors in the kraken Image plugin
            JError::raiseWarning(100, JText::plural('COM_KRAKENIMAGE_ERROR_REDUCE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
            continue;
        }
 		return true;
    }
    
    public function disableReduceMediaLibraryButton( )
    {
        // Get the params and set the new values
        $params = JComponentHelper::getParams('com_krakenimage');
        $params->set('pi_reduceMediaLibraryButtonEnable', 0);

        // Get a new database query instance
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Build the query
        $query->update('#__extensions AS a');
        $query->set('a.params = ' . $db->quote((string)$params));
        $query->where('a.element = "com_krakenimage"');

        // Execute the query
        $db->setQuery($query);
        $db->query();
        
        return true;
    }
    
    public function reduceurl()
    {
    	$app = JFactory::getApplication();
    	
    	JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
    	
    	$user	= JFactory::getUser();
    	
    	// check and set this for Joomla 2.5
    	if(!isset($this->input)){
    		$this->input = JFactory::getApplication()->input;
    	}
    	
    	// Get some data from the request
    	$tmpl   = $this->input->get('tmpl');
    	$paths  = $this->input->get('imageurl', '', 'RAW');
    	$folder = $this->input->get('folder', '', 'path');
    	
    	$redirect = 'index.php?option=com_krakenimage&folder=' . $folder;
    	
    	if ($tmpl == 'component')
    	{
    		// We are inside the iframe
    		$redirect .= '&view=mediaList&tmpl=component';
    	}
    	
    	$this->setRedirect($redirect);
    	
    	// Just return if there's nothing to do
    	if (empty($paths))
    	{
    		return true;
    	}
    	
    	if (!$user->authorise('core.edit', 'com_krakenimage'))
    	{
    		// User is not authorised to edit
    		JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
    	
    		return false;
    	}
    	
    	// Set FTP credentials, if given
    	JClientHelper::setCredentialsFromRequest('ftp');
    	
    	$ret = true;
    	
    	JPluginHelper::importPlugin('content');
    	// Set the right dispatcher for version of Joomla
    	$jver = new JVersion;
    	if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
    		$dispatcher	= JDispatcher::getInstance();
    	}
    	else {
    		$dispatcher	= JEventDispatcher::getInstance();
    	}
    	
    	if (!empty($paths))
    	{
    		$filesize = "";
    		$fullPath = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder)));
    		$object_file = new JObject(array('filepath' => $fullPath, 'tmp_name' => $paths, 'size' => $filesize));
    		
    		// Trigger the onkrakenImage event.
    		$result = $dispatcher->trigger('onkrakenImageUrl', array('com_krakenimage.file', &$object_file, false, false));
    		if (in_array(false, $result, true))
    		{
    			// There are some errors in the kraken Image plugin
    			JError::raiseWarning(100, JText::plural('COM_KRAKENIMAGE_ERROR_REDUCE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
    			continue;
    		}
    		$this->setMessage(JText::sprintf('COM_KRAKENIMAGE_REDUCE_COMPLETE'));
    	}
    	
    	return $ret;
    }

}
