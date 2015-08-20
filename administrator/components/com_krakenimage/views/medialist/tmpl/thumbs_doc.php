<?php


defined('_JEXEC') or die;

$user = JFactory::getUser();
$params = new JRegistry;
// Use the right dispatcher for the version of Joomla
$jver = new JVersion;
if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
    $dispatcher	= JDispatcher::getInstance();
}
else {
    $dispatcher	= JEventDispatcher::getInstance();
}
$dispatcher	= JEventDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_krakenimage.file', &$this->_tmp_doc, &$params));
?>
		<li class="imgOutline thumbnail height-80 width-80 center">
			<?php if ($user->authorise('core.delete', 'com_krakenimage')):?>
				<input class="pull-left" type="checkbox" name="rd[]" value="<?php echo $this->_tmp_doc->name; ?>" />
				<div class="clearfix"></div>
			<?php endif;?>
			<div class="height-50">
				<a style="display: block; width: 100%; height: 100%" title="<?php echo $this->_tmp_doc->name; ?>" >
					<?php echo JHtml::_('image', $this->_tmp_doc->icon_32, $this->_tmp_doc->name, null, true, true) ? JHtml::_('image', $this->_tmp_doc->icon_32, $this->_tmp_doc->title, null, true) : JHtml::_('image', 'media/con_info.png', $this->_tmp_doc->name, null, true); ?></a>
			</div>
			<div class="small" title="<?php echo $this->_tmp_doc->name; ?>" >
				<?php echo JHtml::_('string.truncate', $this->_tmp_doc->name, 10, false); ?>
			</div>
		</li>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_krakenimage.file', &$this->_tmp_doc, &$params));
?>
