<?php


defined('_JEXEC') or die;

$title = JText::_('COM_KRAKENIMAGE_REDUCE_MEDIA');
$params = JComponentHelper::getParams('com_krakenimage');
$hasUserId = (trim($params->get('pi_userid')) != '');
$pluginEnabled = MediaHelper::iskrakenImagePluginEnabled();
if ($pluginEnabled == null)
    $pluginEnabled = false;
$enabled = ($hasUserId) && ($pluginEnabled);
?>
<button onclick="KrakenImageManager.submit('folder.reduce')" class="btn btn-small" <?php if (!$enabled) echo 'disabled'; ?>>
	<i class="icon-ok" title="<?php echo $title; ?>"></i> <?php echo $title; ?>
</button>
