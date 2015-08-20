<?php


defined('_JEXEC') or die;

// Check the version of Joomla
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
$dispatcher->trigger('onContentBeforeDisplay', array('com_krakenimage.file', &$this->_tmp_img, &$params));
?>
		<tr>
		<!--	<td>
				<a class="img-preview" href="<?php echo COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>"><?php echo JHtml::_('image', COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative, JText::sprintf('COM_KRAKENIMAGE_IMAGE_TITLE', $this->_tmp_img->title, JHtml::_('number.bytes', $this->_tmp_img->size)), array('width' => $this->_tmp_img->width_16, 'height' => $this->_tmp_img->height_16)); ?></a>
			</td> -->
			<td class="description">
					<i class="icon-file pull-left" data-target="#collapseFolder-banners" data-toggle="collapse"></i>
				<a href="<?php echo  COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>" rel="preview"><?php echo $this->escape($this->_tmp_img->title); ?></a>
			</td>
			<td class="dimensions">
				<?php echo JText::sprintf('COM_KRAKENIMAGE_IMAGE_DIMENSIONS', $this->_tmp_img->width, $this->_tmp_img->height); ?>
			</td>
			<td class="filesize">
				<?php echo JHtml::_('number.bytes', $this->_tmp_img->originalSize); ?>
			</td>
   			<td class="filesize">
				<?php echo JHtml::_('number.bytes', $this->_tmp_img->size); ?>
			</td>
   			<td class="filesize">
				<?php 
                    if ($this->_tmp_img->processed)
                    {
                        if($this->_tmp_img->status == 0)
                        {
                            $savings = $this->_tmp_img->originalSize - $this->_tmp_img->size;
                            if ($savings != 0)
                            {
                                $percent_savings = ($savings/$this->_tmp_img->originalSize)*100;
                                echo JText::sprintf('COM_KRAKENIMAGE_FILESIZE_SAVINGS_PERCENT', $percent_savings);
                                $str_savings = JHtml::_('number.bytes', abs($savings));
                                $sign = '';
                                if ($savings < 0)
                                    $sign = '-';                                    
                                echo ' (' . $sign . $str_savings . ')';                            
                            }
                            else
                                echo JText::_('COM_KRAKENIMAGE_FILESIZE_SAVINGS_NONE');
                        }
                        else
                        {
                            echo JText::_('COM_KRAKENIMAGE_FILE_HAS_ERROR');
                        }
                    }
                    else
                    {
                        echo JText::_('COM_KRAKENIMAGE_FILE_NOTPROCESSED');
                    }
                ?>
			</td>

		<?php if ($user->authorise('core.edit', 'com_krakenimage')):?>
			<td>
				<input type="checkbox" name="rd[]" value="<?php echo $this->_tmp_img->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_krakenimage.file', &$this->_tmp_img, &$params));
?>
