<?php

defined('_JEXEC') or die;

// Check for Joomla 2.5
$jver = new JVersion;
if ( substr($jver->getShortVersion(), 0, 3) != '2.5' ) {
    JHtml::_('bootstrap.tooltip');
}

$user = JFactory::getUser();
$params = new JRegistry;
// Use the right dispatcher for the version of Joomla
if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
    $dispatcher	= JDispatcher::getInstance();
}
else {
    $dispatcher	= JEventDispatcher::getInstance();
}
$dispatcher->trigger('onContentBeforeDisplay', array('com_krakenimage.file', &$this->_tmp_doc, &$params));
?>
		<tr>
			<td>
				<a  title="<?php echo $this->_tmp_doc->name; ?>">
					<?php  echo JHtml::_('image', $this->_tmp_doc->icon_16, $this->_tmp_doc->title, null, true, true) ? JHtml::_('image', $this->_tmp_doc->icon_16, $this->_tmp_doc->title, array('width' => 16, 'height' => 16), true) : JHtml::_('image', 'media/con_info.png', $this->_tmp_doc->title, array('width' => 16, 'height' => 16), true);?> </a>
			</td>
			<td class="description"  title="<?php echo $this->_tmp_doc->name; ?>">
				<?php echo $this->_tmp_doc->title; ?>
			</td>
			<td>&#160;

			</td>
			<td class="filesize">
				<?php echo JHtml::_('number.bytes', $this->_tmp_doc->originalSize); ?>
			</td>
			<td class="filesize">
				<?php echo JHtml::_('number.bytes', $this->_tmp_doc->size); ?>
			</td>            
            
		<?php if ($user->authorise('core.edit', 'com_krakenimage')):?>
			<td>
				<input type="checkbox" name="rd[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_krakenimage.file', &$this->_tmp_doc, &$params));
?>
