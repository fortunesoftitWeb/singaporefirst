<?php


defined('_JEXEC') or die;
$user = JFactory::getUser();
?>
		<li class="imgOutline thumbnail height-80 width-80 center">
			<?php if ($user->authorise('core.edit', 'com_krakenimage')):?>
				<input class="pull-left" type="checkbox" name="rd[]" value="<?php echo $this->_tmp_folder->name; ?>" />
				<div class="clearfix"></div>
			<?php endif;?>
			<div class="height-50">
				<a href="index.php?option=com_krakenimage&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe">
					<i class="icon-folder-2"></i>
				</a>
			</div>
			<div class="small">
				<a href="index.php?option=com_krakenimage&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe"><?php echo JHtml::_('string.truncate', $this->_tmp_folder->name, 10, false); ?></a>
			</div>
		</li>
