<?php


defined('_JEXEC') or die;


class KrakenImageController extends JControllerLegacy
{
	
	public function display($cachable = false, $urlparams = false)
	{
		JPluginHelper::importPlugin('content');
        if(!isset($this->input)){
            $this->input = JFactory::getApplication()->input;
        }        
		$vName = $this->input->get('view', 'media');
		switch ($vName)
		{
			case 'images':
				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName = 'manager';
				break;

			case 'imagesList':
				$mName = 'list';
				$vLayout = $this->input->get('layout', 'default', 'string');
				break;

			case 'mediaList':
				$app	= JFactory::getApplication();
				$mName = 'list';
                // Modify media/tmpl/default_navigation.php to re-enable view selection buttons
                // For now only allow the details view
                $app->setUserState('pi_media.list.layout', 'details');
				$vLayout = $app->getUserStateFromRequest('pi_media.list.layout', 'layout', 'thumbs', 'word');
				break;

			case 'media':
			default:
				$vName = 'media';
				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName = 'manager';
				break;
		}

		$document = JFactory::getDocument();
		$vType    = $document->getType();

		// // Get/Create the view
		$view = $this->getView($vName, $vType);
		$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.'/views/'.strtolower($vName).'/tmpl');
        
		// Get/Create the model
		if ($model = $this->getModel($mName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}
        
		// Set the layout
		$view->setLayout($vLayout);
        
		// Display the view
		$view->display();

		return $this;
	}

	public function ftpValidate()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
	}
}
