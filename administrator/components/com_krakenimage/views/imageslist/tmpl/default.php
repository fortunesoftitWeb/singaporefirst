<?php


defined('_JEXEC') or die;
?>
<?php if (count($this->images) > 0 || count($this->folders) > 0) { ?>
<ul class="manager thumbnails">
	<?php for ($i = 0, $n = count($this->folders); $i < $n; $i++) :
		$this->setFolder($i);
		echo $this->loadTemplate('folder');
	endfor; ?>

	<?php for ($i = 0, $n = count($this->images); $i < $n; $i++) :
		$this->setImage($i);
		echo $this->loadTemplate('image');
	endfor; ?>
</ul>
<?php } else { ?>
	<div id="media-noimages">
		<div class="alert alert-info"><?php echo JText::_('COM_KRAKENIMAGE_NO_IMAGES_FOUND'); ?></div>
	</div>
<?php } ?>
