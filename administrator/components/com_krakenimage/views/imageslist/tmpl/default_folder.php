<?php


defined('_JEXEC') or die;

$input = JFactory::getApplication()->input;
?>
<li class="imgOutline thumbnail height-80 width-80 center">
	<a href="index.php?option=com_krakenimage&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>" target="imageframe">
		<div class="height-50">
			<i class="icon-folder-2"></i>
		</div>
		<div class="small">
			<?php echo JHtml::_('string.truncate', $this->_tmp_folder->name, 10, false); ?>
		</div>
	</a>
</li>
