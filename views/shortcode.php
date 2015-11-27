<?php if (!empty($args['apikey'])): ?>
<form method="POST" id="<?php echo $args['id'] ?>" class="simple-form-mail-subscribe <?php echo $args['class'] ?>">
	<div class="message success_message"><?php echo $args['success_message'] ?></div>
	<div class="message fail_message"><?php echo $args['fail_message'] ?></div>
	
	<input type="hidden" name="list" value="<?php echo $args['list'] ?>">
	<input type="hidden" name="action" value="simplesubscribe">
	<input type="hidden" name="apikey" value="<?php echo $args['apikey'] ?>">
	
	<input type="email" name="email" value="" placeholder="<?php echo $args['placeholder'] ?>" required />
	<button type="submit" class="button button-primary"><?php echo $args['button'] ?></button>
</form>
<?php else: ?>
<div class="incorrect_apikey">Api Key is not correct</div>
<?php endif; ?>
