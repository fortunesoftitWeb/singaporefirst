<?php

defined('_JEXEC') or die;

$user = JFactory::getUser();

// Check the version of Joomla
$jver = new JVersion;
if ( substr($jver->getShortVersion(), 0, 3) != '2.5' ) {
    JHtml::_('bootstrap.tooltip');
}
?>
		<tr>
		<!--<td class="imgTotal">
				<a href="index.php?option=com_krakenimage&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe">
					<i class="icon-folder-2"></i></a>
			</td> -->
			<td class="description">
				<i class="icon-folder pull-left" data-target="#collapseFolder-banners" data-toggle="collapse"></i>
				<a href="index.php?option=com_krakenimage&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe"><?php echo $this->_tmp_folder->name; ?></a>
			</td>
			<td>&#160;

			</td>
			<td>&#160;

			</td>
			<td>&#160;

			</td>            
			<td>&#160;

			</td>                        
		<?php if ($user->authorise('core.edit', 'com_krakenimage')):?>
			<td>
				<input type="checkbox" name="rd[]" value="<?php echo $this->_tmp_folder->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
