<?php 
	foreach ($messages as $type => $message) : ?>
		<div class="clear-fix">&nbsp;</div>
		<div class="alert alert-<?php echo $type ?>">
			 <?php echo $message ?>
		</div> 		
	<?php endforeach ?>
