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
	'apikey'            => array(
									'title'         => __( 'Api Key', 'cherry-mailchimp' ),
									'description'   => __( 'Set your Api Key (find in MailChimp account)', 'cherry-mailchimp' ),
									'value'         => __( '', 'cherry-mailchimp' ),
								),
	'list'              => array(
									'title'        => __( 'List' ),
									'description'  => __( 'Subscribe list id', 'cherry-mailchimp' ),
									'value'        => __( '', 'cherry-mailchimp' ),
								),
	'confirm'           => array(
									'title'        => __( 'Confirmation' ),
									'description'  => __( 'Email confirmation', 'cherry-mailchimp' ),
									'value'        => __( '', 'cherry-mailchimp' ),
								),
	'placeholder'       => array(
									'title'        => __( 'Placeholder' ),
									'description'  => __( 'Defaulr placeholder of email input', 'cherry-mailchimp' ),
									'value'        => __( 'enter your email', 'cherry-mailchimp' ),
								),
	'button_text'       => array(
									'title'        => __( 'Button' ),
									'description'  => __( 'Default submit button text', 'cherry-mailchimp' ),
									'value'        => __( 'Subscribe', 'cherry-mailchimp' ),
								),
	'success_message'   => array(
									'title'        => __( 'Success message', 'cherry-mailchimp' ),
									'description'  => __( 'Default success message', 'cherry-mailchimp' ),
									'value'        => __( 'Subscribed successfully', 'cherry-mailchimp' ),
								),
	'fail_message'      => array(
									'title'        => __( 'Fail message', 'cherry-mailchimp' ),
									'description'  => __( 'Default fail message', 'cherry-mailchimp' ),
									'value'        => __( 'Subscribed failed', 'cherry-mailchimp' ),
								),
	'warning_message'   => array(
									'title'        => __( 'Warning message', 'cherry-mailchimp' ),
									'description'  => __( 'Default warning message', 'cherry-mailchimp' ),
									'value'        => __( 'Email is incorect', 'cherry-mailchimp' ),
								),
);

// Check connect
if ( $this->check_apikey() ) {
	$connect_class = 'success';
	$connect_message = __( 'CONNECT', 'cherry-mailchimp' );
} else {
	$connect_class = 'danger';
	$connect_message = __( 'DISCONNECT', 'cherry-mailchimp' );
}

?>

<!-- Page Title -->
<div class="wrap">
	<h1><?php echo __( 'Plugin options', 'cherry-mailchimp' ) ?></h1>
</div>
<!-- END Page Title -->

<!-- Shortcode -->
<div class="wrap">
	<div class="container">
		<span class="pull-right">
		<?php
			do_action( 'cherry_shortcode_generator_buttons' );
		?>
		</span>
	</div>
</div>
<!-- END Shortcode -->

<!-- Options -->
<div class="wrap">
	<form class="cherry-option" method="POST">
		<div class="container">
			<?php foreach ( $fields as $field => $strings ) : ?>
			<?php
				// Render ui-element
				if ( 'confirm' == $field ) {
					$confirm = empty( $this->options['confirm'] ) ? 'true' : $this->options['confirm'];
					$ui_{$field} = new UI_Switcher2(
							array(
									'id'				=> 'confirm',
									'name'				=> 'confirm',
									'value'				=> $confirm,
									'toggle'			=> array(
											'true_toggle'	=> 'On',
											'false_toggle'	=> 'Off',
									),

									'style'		=> 'normal',
							)
					);
				} else {
					$value = empty( $this->options[ $field ] ) ? $strings['value'] : $this->options[ $field ];
					$ui_{$field} = new UI_Text(
							array(
									'id'            => $field,
									'type'          => 'text',
									'name'          => $field,
									'placeholder'   => $strings['title'],
									'value'         => $value,
									'label'         => '',
							)
					);
				}

				$html = $ui_{$field}->render();
			?>
			<div class="row">
				<div class="col-md-12">
					<h4>
						<?php echo $strings['title'] ?>
						<?php if ( 'apikey' === $field ) : ?>
							<small class="text-<?php echo $connect_class ?>">
								(<?php echo $connect_message ?>)
							</small>
						<?php endif; ?>
					</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 description"><?php echo $strings['description'] ?></div>
				<div class="col-md-8"><?php echo $html ?></div>
			</div>
			<?php endforeach; ?>
			<div class="row">
				<div class="col-md-12">
					<input type="submit" class="button button-primary pull-right" name="action" value="<?php echo __( 'Save', 'cherry-mailchimp' ) ?>">
				</div>
			</div>
		</div>
	</form>
</div>
<!-- END Options -->
<div class="cherry-clear"></div>
