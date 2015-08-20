<?php


defined('_JEXEC') or die;


class KrakenImageViewImages extends JViewLegacy
{
	public function display($tpl = null)
	{
		$config = JComponentHelper::getParams('com_krakenimage');
		$lang	= JFactory::getLanguage();

		JHtml::_('behavior.framework', true);
		JHtml::_('script', 'media/popup-imagemanager.js', true, true);
		JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);

		if ($lang->isRTL())
		{
			JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);
		}

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session = JFactory::getSession();
		$this->config = $config;
		$this->state = $this->get('state');
		$this->folderList = $this->get('folderList');
		$this->require_ftp = $ftp;

		parent::display($tpl);
	}
}
