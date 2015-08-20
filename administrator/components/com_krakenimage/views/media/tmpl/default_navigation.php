<?php


defined('_JEXEC') or die;
$app	= JFactory::getApplication();
$style = $app->getUserStateFromRequest('pi_media.list.layout', 'layout', 'thumbs', 'word');
?>
<!-- Comment out view selection buttons, also modify controller.php to re-enable view selection buttons
<div class="media btn-group">
	<a href="#" id="thumbs" onclick="krakenImageManager.setViewType('thumbs')" class="btn <?php echo ($style == "thumbs") ? 'active' : '';?>">
	<i class="icon-grid-view-2"></i> <?php echo JText::_('COM_KRAKENIMAGE_THUMBNAIL_VIEW'); ?></a>
	<a href="#" id="details" onclick="krakenImageManager.setViewType('details')" class="btn <?php echo ($style == "details") ? 'active' : '';?>">
	<i class="icon-list-view"></i> <?php echo JText::_('COM_KRAKENIMAGE_DETAIL_VIEW'); ?></a>
</div>
-->
