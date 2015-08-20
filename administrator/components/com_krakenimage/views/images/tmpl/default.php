<?php


defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen', 'select');

// Check for version of Joomla running
$jver = new JVersion( );
if ( substr($jver->getShortVersion(), 0, 3) != '2.5' ) {
    // Load tooltip instance without HTML support because we have a HTML tag in the tip
    JHtml::_('bootstrap.tooltip', '.noHtmlTip', array('html' => false));
}

$user  = JFactory::getUser();
$input = JFactory::getApplication()->input;
?>
<script type='text/javascript'>
var image_base_path = '<?php $params = JComponentHelper::getParams('com_krakenimage');
echo $params->get('image_path', 'images'); ?>/';
</script>
<form action="index.php?option=com_krakenimage&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author'); ?>" class="form-vertical" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true) ?>
	</div>
	<div class="well">
		<div class="row">
			<div class="span9 control-group">
				<div class="control-label">
					<label class="control-label" for="folder"><?php echo JText::_('COM_KRAKENIMAGE_DIRECTORY') ?></label>
				</div>
				<div class="controls">
					<?php echo $this->folderList; ?>
					<button class="btn" type="button" id="upbutton" title="<?php echo JText::_('COM_KRAKENIMAGE_DIRECTORY_UP') ?>"><?php echo JText::_('COM_KRAKENIMAGE_UP') ?></button>
				</div>
			</div>
			<div class="pull-right">
				<button class="btn btn-primary" type="button" onclick="<?php if ($this->state->get('field.id')):?>window.parent.jInsertFieldValue(document.id('f_url').value,'<?php echo $this->state->get('field.id');?>');<?php else:?>ImageManager.onok();<?php endif;?>window.parent.SqueezeBox.close();"><?php echo JText::_('COM_KRAKENIMAGE_INSERT') ?></button>
				<button class="btn" type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
			</div>
		</div>
	</div>

	<iframe id="imageframe" name="imageframe" src="index.php?option=com_krakenimage&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>"></iframe>

	<div class="well">
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_url"><?php echo JText::_('COM_KRAKENIMAGE_IMAGE_URL') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_url" value="" />
				</div>
			</div>
			<?php if (!$this->state->get('field.id')):?>
			<div class="span6 control-group">
				<div class="control-label">
					<label title="<?php echo JText::_('COM_KRAKENIMAGE_ALIGN_DESC'); ?>" class="noHtmlTip" for="f_align"><?php echo JText::_('COM_KRAKENIMAGE_ALIGN') ?></label>
				</div>
				<div class="controls">
					<select size="1" id="f_align">
						<option value="" selected="selected"><?php echo JText::_('COM_KRAKENIMAGE_NOT_SET') ?></option>
						<option value="left"><?php echo JText::_('JGLOBAL_LEFT') ?></option>
						<option value="center"><?php echo JText::_('JGLOBAL_CENTER') ?></option>
						<option value="right"><?php echo JText::_('JGLOBAL_RIGHT') ?></option>
					</select>
				</div>
			</div>
			<?php endif;?>
		</div>
		<?php if (!$this->state->get('field.id')):?>
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_alt"><?php echo JText::_('COM_KRAKENIMAGE_IMAGE_DESCRIPTION') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_alt" value="" />
				</div>
			</div>
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_title"><?php echo JText::_('COM_KRAKENIMAGE_TITLE') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_title" value="" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_caption"><?php echo JText::_('COM_KRAKENIMAGE_CAPTION') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_caption" value="" />
				</div>
			</div>
			<div class="span6 control-group">
				<div class="control-label">
					<label title="<?php echo JText::_('COM_KRAKENIMAGE_CAPTION_CLASS_DESC'); ?>" class="noHtmlTip" for="f_caption_class"><?php echo JText::_('COM_KRAKENIMAGE_CAPTION_CLASS_LABEL') ?></label>
				</div>
				<div class="controls">
					<input type="text" list="d_caption_class" id="f_caption_class" value="" />
					<datalist id="d_caption_class">
						<option value="text-left">
						<option value="text-center">
						<option value="text-right">
					</datalist>
				</div>
			</div>
		</div>
		<?php endif;?>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />

	</div>
</form>

