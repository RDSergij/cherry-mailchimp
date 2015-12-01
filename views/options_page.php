<?php

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

$fields = array(
	'apikey'            => __('Set your Api Key'),
	'list'              => __('Subscribe list id'),
	'confirm'           => __('Email confirmation'),
	'placeholder'       => __('Placeholder of email input'),
	'button_text'       => __('Submit button text'),
	'success_message'   => __('Success_message'),
	'fail_message'      => __('Fail message'),
	'warning_message'   => __('Warning message'),
);

?>

<div class="wrap">
	<h1><?php echo __('Plugin options') ?></h1>

	<?php if ( !empty($shortcode) ): ?>

	<h2><?php echo __('Shortcode') ?></h2>
	<div class="container">
		<?php echo $shortcode ?>
	</div>

	<?php endif; ?>

</div>

<div class="wrap">
	<form method="POST">
		<table class="table table-striped">
			<tr>
				<td><?php echo __('Check account') ?></td>
				<td>
					<?php

					if ($this->check_apikey()) {
						$connect_class = 'success';
						$connect_message = __('CONNECT');
					} else {
						$connect_class = 'danger';
						$connect_message = __('DISCONNECT');
					}
					?>

					<span class="text-<?php echo $connect_class ?>">
						<?php echo $connect_message ?>
					</span>
				</td>
			</tr>
			<?php foreach ($fields as $field=>$title): ?>
				<?php
					if ( 'confirm' == $field ) {
						$ui_{$field} = new UI_Switcher(
								array(
										'name'				=> 'confirm',
										'value'				=> $this->options['confirm'],
										'toggle'			=> array(
												'true_toggle'	=> 'On',
												'false_toggle'	=> 'Off',
										),

										'style'		=> 'small',
								)
						);
					} else {
						$ui_{$field} = new UI_Text(
								array(
										'id'            => $field,
										'type'          => 'text',
										'name'          => $field,
										'placeholder'   => $title,
										'value'         => $this->options[$field],
										'label'         => '',
								)
						);
					}

					$html = $ui_{$field}->render();
					?>
					<tr>
						<td><?php echo $title ?></td>
						<td><?php echo $html ?></td>
					</tr>
					<?php
				?>
			<?php endforeach; ?>
		</table>
		<input type="submit" class="button button-primary" name="action" value="Save">
	</form>
</div>
