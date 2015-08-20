<?php


defined('_JEXEC') or die;
$user = JFactory::getUser();
$params = JComponentHelper::getParams( 'com_krakenimage' );
?>
<form target="_parent" action="index.php?option=com_krakenimage&amp;tmpl=index&amp;folder=<?php echo $this->state->folder; ?>" method="post" id="krakenimagemanager-form" name="krakenimagemanager-form">
	<div class="manager">
	<table class="table table-striped table-condensed">
	<thead>
		<tr>
		<!--	<th width="8%"><?php echo JText::_('JGLOBAL_PREVIEW'); ?></th> -->
			<th><?php echo JText::_('COM_KRAKENIMAGE_NAME'); ?></th>
			<th width="15%"><?php echo JText::_('COM_KRAKENIMAGE_PIXEL_DIMENSIONS'); ?></th>
            <th width="10%"><?php echo JText::_('COM_KRAKENIMAGE_FILESIZE_ORIGINAL'); ?></th>
			<th width="10%"><?php echo JText::_('COM_KRAKENIMAGE_FILESIZE'); ?></th>
            <th width="15%"><?php echo JText::_('COM_KRAKENIMAGE_FILESIZE_SAVINGS'); ?></th>
		<?php if ($user->authorise('core.edit', 'com_krakenimage')):?>
			<th width="8%"><?php echo JText::_('COM_KRAKENIMAGE_REDUCE_MEDIA'); ?></th>
		<?php endif;?>
		</tr>
	</thead>
	<tbody>
		<?php echo $this->loadTemplate('up'); ?>

		<?php for ($i = 0, $n = count($this->folders); $i < $n; $i++) :
			$this->setFolder($i);
			echo $this->loadTemplate('folder');
		endfor; ?>

		<?php for ($i = 0, $n = count($this->documents); $i < $n; $i++) :
			$this->setDoc($i);
			echo $this->loadTemplate('doc');
		endfor; ?>

		<?php for ($i = 0, $n = count($this->images); $i < $n; $i++) :
			$this->setImage($i);
			echo $this->loadTemplate('img');
		endfor; ?>
       
	</tbody>
	</table>
    <?php
         //$totalBytesSaved = $params->get('pi_totalBytesSaved'); 
         //echo JText::sprintf('<p><b>Total Saved: %s</b></p>', JHtml::_('number.bytes', $totalBytesSaved));
    ?>
	<input type="hidden" name="task" value="list" />
	<input type="hidden" name="username" value="" />
	<input type="hidden" name="password" value="" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
