<?php


defined('_JEXEC') or die;

$user = JFactory::getUser();
?>
		<tr>
		<!--	<td class="imgTotal">
				<a href="index.php?option=com_krakenimage&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">
					<i class="icon-arrow-up"></i></a>
			</td> -->
		<!--	<td class="description">
				<a href="index.php?option=com_krakenimage&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">..</a>
			</td>
			<td>&#160;</td>
			<td>&#160;</td>
			<td>&#160;</td> 
            <td>&#160;</td>              
		<?php if ($user->authorise('core.edit', 'com_krakenimage')):?>
			<td>&#160;</td>
		<?php endif;?>
		</tr>
