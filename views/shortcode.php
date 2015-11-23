<?php if (!empty($apikey)): ?>
<form method="POST" id="<?php echo $id ?>" class="simpleFormStyleClass <?php echo $class ?>">
	<div class="message success_message"><?php echo $success_message ?></div>
	<div class="message fail_message"><?php echo $fail_message ?></div>
	
	<input type="hidden" name="list" value="<?php echo $list ?>">
	<input type="hidden" name="action" value="simplesubscribe">
	<input type="hidden" name="apikey" value="<?php echo $apikey ?>">
	
	<input type="email" name="email" value="" placeholder="<?php echo $placeholder ?>" required />		
	<button type="submit" class="button button-primary"><?php echo $button ?></button>
</form>
<?php else: ?>
<div class="incorrect_apikey">Api Key is not correct</div>
<?php endif; ?>
