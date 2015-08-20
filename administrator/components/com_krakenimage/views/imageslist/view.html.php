<?php


defined('_JEXEC') or die;


class KrakenImageViewImagesList extends JViewLegacy
{
	public function display($tpl = null)
	{
        // Do not allow cache
        $jver = new JVersion();
        if ( substr($jver->getShortVersion(), 0, 3) == '2.5' )
        {
            JResponse::allowCache(false);
        }
        else
        {
            JFactory::getApplication()->allowCache(false);
        }

		$lang	= JFactory::getLanguage();

		JHtml::_('stylesheet', 'media/popup-imagelist.css', array(), true);
		if ($lang->isRTL()) {
			JHtml::_('stylesheet', 'media/popup-imagelist_rtl.css', array(), true);
		}

		$document = JFactory::getDocument();
		$document->addScriptDeclaration("var ImageManager = window.parent.ImageManager;");

		$images = $this->get('images');
		$folders = $this->get('folders');
		$state = $this->get('state');

		$this->baseURL = COM_MEDIA_BASEURL;
		$this->images = &$images;
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
}
