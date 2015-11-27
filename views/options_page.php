<?php

$fields = array(
	'apikey'            => __('Set your Api Key'),
	'list'              => __('Subscribe list id'),
	'placeholder'       => __('Placeholder of email input'),
	'button_text'       => __('Submit button text'),
	'success_message'   => __('Success_message'),
	'fail_message'      => __('Fail message'),
	'warning_message'   => __('Warning message'),
);

?>

<div class="wrap">
	<h1><?php echo __('Plugin options') ?></h1>
	<form method="POST">
		<?php foreach ($fields as $field=>$title): ?>
		<div class="form-group">
			<?php
				$ui_{$field} = new UI_Text(
						array(
								'id'            => $field,
								'type'          => 'text',
								'name'          => $field,
								'placeholder'   => $title,
								'value'         => $this->options[$field],
								'label'         => $title,
						)
				);
				$html = $ui_{$field}->render();
				echo $html;
			?>
		</div>
		<?php endforeach; ?>
		<input type="submit" class="button button-primary" name="action" value="Save">
	</form>

	<h2><?php echo __('Shortcode') ?></h2>
	<div class="container">
		<?php echo $shortcode ?>
	</div>
</div>
