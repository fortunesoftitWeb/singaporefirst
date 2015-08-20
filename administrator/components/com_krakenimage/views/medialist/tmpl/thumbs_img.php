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
$dispatcher->trigger('onContentBeforeDisplay', array('com_krakenimage.file', &$this->_tmp_img, &$params));
?>
		<li class="imgOutline thumbnail height-80 width-80 center">
			<?php if ($user->authorise('core.edit', 'com_krakenimage')):?>
				<input class="pull-left" type="checkbox" name="rd[]" value="<?php echo $this->_tmp_img->name; ?>" />
				<div class="clearfix"></div>
			<?php endif;?>
			<div class="height-50">
				<a class="img-preview" href="<?php echo COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>" >
					<?php echo JHtml::_('image', COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative, JText::sprintf('COM_KRAKENIMAGE_IMAGE_TITLE', $this->_tmp_img->title, JHtml::_('number.bytes', $this->_tmp_img->size)), array('width' => $this->_tmp_img->width_60, 'height' => $this->_tmp_img->height_60)); ?>
				</a>
			</div>
			<div class="small">
				<a href="<?php echo COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>" class="preview"><?php echo JHtml::_('string.truncate', $this->_tmp_img->name, 10, false); ?></a>
			</div>
		</li>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_krakenimage.file', &$this->_tmp_img, &$params));
?>
