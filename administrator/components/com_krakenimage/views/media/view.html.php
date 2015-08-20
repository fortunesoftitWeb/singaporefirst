<?php


defined('_JEXEC') or die;


class KrakenImageViewMedia extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
        $component = $app->input->get('option', '');
		$config = JComponentHelper::getParams($component);
        $jver = new JVersion;

		if (!$app->isAdmin())
		{
			return $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
		}

		$lang	= JFactory::getLanguage();  
        
        $style = $app->getUserStateFromRequest('pi_media.list.layout', 'layout', 'thumbs', 'word');

		$document = JFactory::getDocument();
               
		JHtml::_('behavior.framework', true);
        
        JHtml::_('script', $component.'/krakenimagemanager.js', true, true); 
        
   		JHtml::_('stylesheet', $component.'/krakenimage.css', array(), true);
		if ($lang->isRTL()) :
			JHtml::_('stylesheet', $component.'/krakenimage_rtl.css', array(), true);
		endif;
        
		JHtml::_('behavior.modal');
		$document->addScriptDeclaration("
		window.addEvent('domready', function()
		{
			document.preview = SqueezeBox;
		});");

        // Load our version of bootstrap.min.css for Joomla 2.5 which contains pi-container
        if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
            JHtml::_('script', $component.'/bootstrap.min.js', true, true); 
            JHtml::_('stylesheet', $component.'/bootstrap.min.css', array(), true);
            // if ($lang->isRTL()) :
                // JHtml::_('stylesheet', $component.'/bootstrap-rtl.css', array(), true);
            // endif;
        }
        // JHtml::_('script', 'system/mootree.js', true, true, false, false);
		JHtml::_('stylesheet', 'system/mootree.css', array(), true);
		if ($lang->isRTL()) :
			JHtml::_('stylesheet', 'media/mootree_rtl.css', array(), true);
		endif;

		if (DIRECTORY_SEPARATOR == '\\')
		{
			$base = str_replace(DIRECTORY_SEPARATOR, "\\\\", COM_MEDIA_BASE);
		}
		else
		{
			$base = COM_MEDIA_BASE;
		}

		$js = "
			var basepath = '".$base."';
			var viewstyle = '".$style."';
		";
		$document->addScriptDeclaration($js);

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$session	= JFactory::getSession();
		$state		= $this->get('state');
		$this->session = $session;
		$this->config = &$config;
		$this->state = &$state;
		$this->require_ftp = $ftp;
		$this->folders_id = ' id="media-tree"';
		$this->folders = $this->get('folderTree');
		
		/*		
		$db = JFactory::getDbo();
		$query = "  SELECT   *
					FROM   `qqclj_medialibrary`
					ORDER BY id DESC
					LIMIT    0, 1 ";
		$db->setQuery($query);
		$result = $db->loadAssoc();

		$this->total  	= $result['total'];
		$this->process  = $result['process']; */
			
		// Set the toolbar
		$this->addToolbar();

		$this->configDetails();
		parent::display($tpl);
		
		echo JHtml::_('behavior.keepalive');
	        
	}

	
	function configDetails()
	{
		
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query1 = $db->getQuery(true);
		 		 
		$query1->select('*');
		$query1->from($db->quoteName('#__krakenimage'));
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query1);
		 
		// Load the results as a list of stdClass objects
		$results1 = $db->loadObjectList();
		
		$numberOfFiles  = 0;
		$total_original = 0;
		$total_reduced	= 0;
		
		foreach($results1 as $data)
		{
			$totalBytesSaved	 = 0;
			$total_original 	+= $data->originalFileSize;
			$total_reduced	 	+= $data->reducedFileSize; 
			$numberOfFiles  	+= 1;		
	   	}
	   	
		$total = round(($total_original / 1048576), 3) - round(($total_reduced / 1048576), 3);
		
		$quota_used = round(($total_original / 1073741824), 3).' GB';
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('*');
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('name') . ' = '. $db->quote('com_krakenimage'));
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects
		$results = $db->loadObjectList();
		$params = $results[0]->params;
				
		$params = json_decode($params); 
		
		$params->security_credentials = "Valid";
		$params->plan_level = "Premium";
		$params->monthly_usage = "15GB";
		
		$quota_per = ($quota_used/15);
		$quota_per = $quota_per/100;
		$quota_per = $quota_per*100;
		$quota_per = round($quota_per,3);
		
		$params->quota_used = $quota_used.' ('.$quota_per.'%)';
		$params->image_optimized = $numberOfFiles.'('.$quota_used.')';
		
		$per = ($total/15360);
		$per = $per/100;
		$per = $per*100;
		$per = round($per,4);
		
		$params->pi_totalBytesSaved   = $total.' MB ('.$per.'%)';
		$params->pi_userid 			  = $params->pi_userid;
		$params->pi_secretkey 		  = $params->pi_secretkey; 
		$params->pi_optimization_type = $params->pi_optimization_type;
		$params->pi_processOnUpload   = $params->pi_processOnUpload ;
				
		$encoded = json_encode($params); 
		
		// Fields to update.
		$fields = array(
			$db->quoteName('params') . ' = ' . $db->quote($encoded)
		);
		 
		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('name') . ' = ' . $db->quote('com_krakenimage')
		);
		 
		$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
		 
		$db->setQuery($query);
		 
		$result = $db->execute();
		
	}	
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.0
	 */
	protected function addToolbar()
	{
        $app	= JFactory::getApplication();
        
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		$user = JFactory::getUser();

        $jver = new JVersion;
        if ( substr($jver->getShortVersion(), 0, 3) != '2.5' ) {
            // The toolbar functions depend on Bootstrap JS
            JHtml::_('bootstrap.framework');
        }
        else
        {
            JHtml::_('behavior.modal');
        }

		// Set the titlebar text
        JToolbarHelper::title(JText::_('COM_KRAKENIMAGE'), 'krakenimage');
        
        // Warn if no kraken Image License Key/user_id
        $params = JComponentHelper::getParams('com_krakenimage');
        $needLicense = (trim($params->get('pi_userid')) == '');        
        if ($needLicense)
            echo '<div align="center">'.JText::_('COM_KRAKENIMAGE_NEED_USERID').'</div>';
            
        // Warn if the kraken Image Plugin is disabled or not installed
        $pluginEnabled = MediaHelper::iskrakenImagePluginEnabled();
        if ($pluginEnabled == NULL)
        {
            $app->enqueueMessage(JText::_('COM_KRAKENIMAGE_PLUGIN_NOT_INSTALLED'), 'Error');
        }
        else if ($pluginEnabled == false)
        {
            $app->enqueueMessage(JText::_('COM_KRAKENIMAGE_PLUGIN_DISABLED'), 'Warning');
        }
        
   		// Add Reduce buttons
		if ($user->authorise('core.edit', 'com_krakenimage'))
		{
            // Add buttons for Joomla 3.x
            if ( substr($jver->getShortVersion(), 0, 3) != '2.5' ) {

                // Add Reduce Media Folder        
                // Instantiate a new JLayoutFile instance and render the layout
                $layout = new JLayoutFile('toolbar.reduceall');

                $bar->appendButton('Custom', $layout->render(array()), 'reduce all');
                JToolbarHelper::divider();
		
                // Add Reduce Selected
                // Instantiate a new JLayoutFile instance and render the layout
                $layout = new JLayoutFile('toolbar.reducemedia');

                $bar->appendButton('Custom', $layout->render(array()), 'reduce');
                
                // Add Reduce Selected
                // Instantiate a new JLayoutFile instance and render the layout
                $layout = new JLayoutFile('toolbar.urlmethod');
                
                $bar->appendButton('Custom', $layout->render(array()), 'urlmethod');
            }
            else
            {
				// Add buttons for Joomla 2.5
                $params = JComponentHelper::getParams('com_krakenimage');
                $hasUserId = (trim($params->get('pi_userid')) != '');
                $pluginEnabled = MediaHelper::iskrakenImagePluginEnabled();
                if ($pluginEnabled == null)
                    $pluginEnabled = false;
                $buttonsEnabled = ($hasUserId) && ($pluginEnabled);

                // Add Reduce Media Folder
                $title = JText::_('COM_KRAKENIMAGE_REDUCE_MEDIA_ALL');
                $buttonReduceLibraryEnabled = ($params->get('pi_reduceMediaLibraryButtonEnable') != 0) && $buttonsEnabled;                
                if ($buttonReduceLibraryEnabled) {
                    $dhtml = "<a href=\"#\" onclick=\"krakenImageManager.submit('folder.reduceall')\" class=\"toolbar\" >
                            <span class=\"icon-reduceall\" title=\"$title\"></span>
                            $title</a>";
                } else {
                    $dhtml = "<a href=\"javascript: void(0)\" class=\"toolbar\" >
                            <span class=\"icon-reduceall-disabled\" title=\"$title\"></span>
                            $title</a>";
                }
                                   
                $bar->appendButton('Custom', $dhtml, 'reduce');

                // Add Reduce Selected
                $title = JText::_('COM_KRAKENIMAGE_REDUCE_MEDIA');
                if ($buttonsEnabled) {
                    $dhtml = "<a href=\"#\" onclick=\"krakenImageManager.submit('folder.reduce')\" class=\"toolbar\" >
                            <span class=\"icon-reduce\" title=\"$title\"></span>
                            $title</a>";
                } else {
                    $dhtml = "<a href=\"javascript: void(0)\" class=\"toolbar\" >
                            <span class=\"icon-reduce-disabled\" title=\"$title\"></span>
                            $title</a>";
                }     
                $bar->appendButton('Custom', $dhtml, 'reduce');
            }
            
            JToolbarHelper::divider();
        }

		// Add a preferences button
		if ($user->authorise('core.admin', 'com_krakenimage'))
		{
            if ( substr($jver->getShortVersion(), 0, 3) != '2.5' ) {
                JToolbarHelper::preferences('com_krakenimage');
            }
            else
            {
                JToolBarHelper::preferences('com_krakenimage', 450, 800, 'JToolbar_Options', '', 'window.location.reload()');
            }
			
			JToolbarHelper::divider();
		}
        
        //JToolBarHelper::help('COM_krakenIMAGE_HELP');
        
	}

	function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt = null;
		if (isset($folder['children']) && count($folder['children']))
		{
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}
		return $txt;
	}
}

?>
