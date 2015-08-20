<?php


defined('_JEXEC') or die;
?>
<form target="_parent" action="index.php?option=com_krakenimage&amp;tmpl=index&amp;folder=<?php echo $this->state->folder; ?>" method="post" id="krakenimagemanager-form" name="krakenimagemanager-form">
	<ul class="manager thumbnails">
		<?php
		echo $this->loadTemplate('up');
		?>

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

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="username" value="" />
		<input type="hidden" name="password" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</ul>
</form>
