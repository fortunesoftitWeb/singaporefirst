<?php


defined('_JEXEC') or die;


class KrakenImageViewMediaList extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		if (!$app->isAdmin())
		{
			return $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
		}

        // Do not allow cache
        $jver = new JVersion();
        if ( substr($jver->getShortVersion(), 0, 3) == '2.5' )
        {
            JResponse::allowCache(false);
        }
        else
        {
            $app->allowCache(false);
        }

		JHtml::_('behavior.framework', true);
        if ( substr($jver->getShortVersion(), 0, 3) == '2.5' )
        {
            JHtml::_('stylesheet', 'com_krakenimage/krakenimage.css', array(), true);
        }

		$images = $this->get('images');
		$documents = $this->get('documents');
		$folders = $this->get('folders');
		$state = $this->get('state');

		// Check for invalid folder name
		if (empty($state->folder)) {
			$dirname = JRequest::getVar('folder', '', '', 'string');
			if (!empty($dirname)) {
				$dirname = htmlspecialchars($dirname, ENT_COMPAT, 'UTF-8');
				JError::raiseWarning(100, JText::sprintf('COM_KRAKENIMAGE_ERROR_UNABLE_TO_BROWSE_FOLDER_WARNDIRNAME', $dirname));
			}
		}

		$this->baseURL = JUri::root();
		$this->images = &$images;
		$this->documents = &$documents;
		$this->folders = &$folders;
		$this->state = &$state;

		parent::display($tpl);
	}

	function setFolder($index = 0)
	{
		if (isset($this->folders[$index]))
		{
			$this->_tmp_folder = &$this->folders[$index];
		}
		else
		{
			$this->_tmp_folder = new JObject;
		}
	}

	function setImage($index = 0)
	{
		if (isset($this->images[$index]))
		{
			$this->_tmp_img = &$this->images[$index];
		}
		else
		{
			$this->_tmp_img = new JObject;
		}
	}

	function setDoc($index = 0)
	{
		if (isset($this->documents[$index]))
		{
			$this->_tmp_doc = &$this->documents[$index];
		}
		else
		{
			$this->_tmp_doc = new JObject;
		}
	}
}
