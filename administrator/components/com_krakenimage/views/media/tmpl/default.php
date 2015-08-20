<?php


defined('_JEXEC') or die;

$user  = JFactory::getUser();
$input = JFactory::getApplication()->input;

// Use our custom container from our bootstrap.min.css for Joomla 2.5
$jver = new JVersion;
if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
  echo '<div class="pi-container">';
}
?>

<div id="green-box" style="display:none;padding:20px;background-color:green">
<p>
</p>
</div>

<div class="row-fluid">
	<!-- Begin Sidebar -->
	<div class="span2">
		<div id="treeview">
			<div id="media-tree_tree" class="sidebar-nav">
				<?php echo $this->loadTemplate('folders'); ?>
			</div>
		</div>
	</div>
	<style>
		.overall-progress,
		.current-progress {
			width: 150px;
		}
	</style>
	<!-- End Sidebar -->
	<!-- Begin Content -->
	<div class="span10">
		<?php echo $this->loadTemplate('navigation'); ?>
		<?php if (($user->authorise('core.create', 'com_krakenimage')) and $this->require_ftp) : ?>
			<form action="index.php?option=com_krakenimage&amp;task=ftpValidate" name="ftpForm" id="ftpForm" method="post">
				<fieldset title="<?php echo JText::_('COM_KRAKENIMAGE_DESCFTPTITLE'); ?>">
					<legend><?php echo JText::_('COM_KRAKENIMAGE_DESCFTPTITLE'); ?></legend>
					<?php echo JText::_('COM_KRAKENIMAGE_DESCFTP'); ?>
					<label for="username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
					<input type="text" id="username" name="username" class="inputbox" size="70" value="" />

					<label for="password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
					<input type="password" id="password" name="password" class="inputbox" size="70" value="" />
				</fieldset>
			</form>
		<?php endif; ?>

		<form action="index.php?option=com_krakenimage" name="adminForm" id="krakenimagemanager-form" method="post" enctype="multipart/form-data" >
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="cb1" id="cb1" value="0" />
			<input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $this->state->folder; ?>" />
		</form>
        
        <form action="index.php?option=com_krakenimage&amp;task=folder.create&amp;tmpl=<?php echo $input->getCmd('tmpl', 'index');?>" name="folderForm" id="folderForm" method="post">
			<div id="folderview">
				<div class="view">
					<iframe class="thumbnail" src="index.php?option=com_krakenimage&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder;?>" id="folderframe" name="folderframe" width="100%" height="500px" marginwidth="0" marginheight="0" scrolling="auto"></iframe>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>

	</div>
	<!-- End Content -->
</div>
<?php
//$app	= JFactory::getApplication();
//$component = $app->input->get('option', '');
//JHtml::_('script', $component.'/test.js', true, true); 

// Finish using our custom container from our bootstrap.min.css for Joomla 2.5
if ( substr($jver->getShortVersion(), 0, 3) == '2.5' ) {
  echo '</div>';
}
?>

