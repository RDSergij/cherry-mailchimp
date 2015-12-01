<?php
/**
 * Admin options page
 *
 * @package Cherry_Mailchimp
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Options fields
$fields = array(
	'apikey'            => __( 'Set your Api Key', 'cherry-mailchimp' ),
	'list'              => __( 'Subscribe list id', 'cherry-mailchimp' ),
	'confirm'           => __( 'Email confirmation', 'cherry-mailchimp' ),
	'placeholder'       => __( 'Placeholder of email input', 'cherry-mailchimp' ),
	'button_text'       => __( 'Submit button text', 'cherry-mailchimp' ),
	'success_message'   => __( 'Success message', 'cherry-mailchimp' ),
	'fail_message'      => __( 'Fail message', 'cherry-mailchimp' ),
	'warning_message'   => __( 'Warning message', 'cherry-mailchimp' ),
);

?>

<!-- Page Title -->
<div class="wrap">
	<h1><?php echo __( 'Plugin options', 'cherry-mailchimp' ) ?></h1>
</div>
<!-- END Page Title -->

<!-- Shortcode -->
<?php if ( ! empty( $shortcod ) ) : ?>

<div class="wrap">
	<h2><?php echo __( 'Shortcode', 'cherry-mailchimp' ) ?></h2>
	<div class="container">
		<?php echo $shortcode ?>
	</div>
</div>
<?php endif; ?>
<!-- END Shortcode -->

<!-- Options -->
<div class="wrap">
	<form method="POST">
		<table class="table table-striped">
			<tr>
				<td><?php echo __( 'Check account', 'cherry-mailchimp' ) ?></td>
				<td>
					<?php

					if ( $this->check_apikey() ) {
						$connect_class = 'success';
						$connect_message = __( 'CONNECT', 'cherry-mailchimp' );
					} else {
						$connect_class = 'danger';
						$connect_message = __( 'DISCONNECT', 'cherry-mailchimp' );
					}
					?>

					<span class="text-<?php echo $connect_class ?>">
						<?php echo $connect_message ?>
					</span>
				</td>
			</tr>
			<?php foreach ( $fields as $field => $title ) : ?>
				<?php
					// Render ui-element
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
										'value'         => $this->options[ $field ],
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
		<input type="submit" class="button button-primary" name="action" value="<?php echo __( 'Save', 'cherry-mailchimp' ) ?>">
	</form>
</div>
<!-- END Options -->