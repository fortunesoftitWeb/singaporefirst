<?php

defined('_JEXEC') or die;

$title = JText::_('COM_KRAKENIMAGE_REDUCE_MEDIA_ALL');
$params = JComponentHelper::getParams('com_krakenimage');
$buttonAllowed = ($params->get('pi_reduceMediaLibraryButtonEnable') != 0);
$hasUserId = (trim($params->get('pi_userid')) != '');

$pluginEnabled = MediaHelper::iskrakenImagePluginEnabled();

if ($pluginEnabled == null)
    $pluginEnabled = false;
$enabled = ($hasUserId) && ($pluginEnabled);

$status = MediaHelper::getstatus();
?>

<!--<button onclick="KrakenImageManager.submit('folder.reduceall')" class="btn btn-small" <?php //if (!$enabled) echo 'disabled'; ?>>
	<i class="icon-folder" title="<?php //echo $title; ?>"></i><?php //echo $title; ?>
</button>-->

<button id="medialibrary" class="btn btn-small" <?php if (!$enabled) echo 'disabled'; ?>>
	<i class="icon-folder" title="<?php echo $title; ?>"></i><?php echo $title; ?>
</button>

<script>

jQuery(document).ready( function(){
	
	jQuery('#medialibrary').click( function(){
		
		jQuery.ajax({
			
			type: "POST",
			url: 'index.php?option=com_krakenimage&tmpl=index&folder=',
			data: {'task':'folder.reducemedialibrary' , 'username':'' ,'password':''},
			dataType: 'json',
			success: function(data) {
				
				var res = JSON.parse(JSON.stringify((data)));
						
				jQuery('#green-box').show();
				jQuery('#green-box').html(res.msg);
				
			}
		});		
		
		/*var response = false;
		var refreshId = setInterval(function(){			
			
			if(response == false)
			resposnse = optimization(response);		
			
		}, 5000); */
		
		var refreshId = setInterval('optimization()', 5000);
					
	});	
	
});	

function optimization(response)
{
	console.log('optimization called');
		
		jQuery.ajax({
				type: "POST",
				url: 'index.php?option=com_krakenimage&tmpl=index&folder=',
				data: {'task':'folder.medialibrary' , 'username':'' ,'password':''},
				dataType: 'json',
				success: function(data) {
					
					var res = JSON.parse(JSON.stringify((data)));
					
								
					if(res.active == 1 || res.process != res.total)
					{
						jQuery('#green-box').show();
						jQuery('#green-box').html(res.msg);
						
						//return false;
					}
					if(res.process == res.total || res.active == 0 )
					{
						jQuery('#green-box').show();
						jQuery('#green-box').html(res.msg);
						//jQuery('#green-box').hide();
						
						//return true;
					}	
				}
			});	
	
}	

</script>
