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
									'description'   => __( 'Set your Api Key', 'cherry-mailchimp' ),
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
									'description'  => __( 'Default placeholder for email input', 'cherry-mailchimp' ),
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
<div class="cherry-page-wrapper">
	<div class="cherry-page-title">
		<span>
			<?php echo __( 'Plugin options', 'cherry-mailchimp' ) ?>
		</span>
	</div>
</div>
<!-- END Page Title -->
<!-- Documentation link -->
<!--div class="cherry-info-box">
	<div class="documentation-link">Feel free to view detailed
		<a href="http://cherryframework.com/documentation/cf4/index.php?project=wordpress&lang=en_US" title="Documentation" target="_blank">
			Cherry Framework 4 documentation
		</a>
	</div>
</div-->
<!-- End Documentation link -->
<!-- Options -->
<div class="wrap cherry-option">
	<form id="cherry-mailchimp-option" method="POST">
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
							<small id="cherry-mail-chimp-connect" class="text-<?php echo $connect_class ?>">
								(<?php echo $connect_message ?>)
							</small>
						<?php endif; ?>
					</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="description">
						<?php echo $strings['description'] ?>
					</div>
					<?php
					if ( 'apikey' == $field || 'list' == $field ) :
						$tooltips_content = array(
							'apikey'    => array(
								'content'   => __( 'You can read more information on the mailchimp knowledge base', 'cherry-mailchimp' ),
								'url'       => 'http://kb.mailchimp.com/accounts/management/about-api-keys',
							),
							'list'      => array(
								'content'   => __( 'You can read more information on the mailchimp knowledge base', 'cherry-mailchimp' ),
								'url'       => 'http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id',
							),
						);
						$ui_tooltip = new UI_Tooltip(
							array(
								'id'			=> 'cherry-mailchimp-options-tooltip-' . $field,
								'hint'			=> array(
									'type'		=> 'text',
									'content'	=> $tooltips_content[ $field ]['content'],
								),
								'class'			=> '',
							)
						);
						?>
						<a class="cherry-mailchimp-tooltip-url" href="<?php echo $tooltips_content[ $field ]['url'] ?>">
							<?php echo $ui_tooltip->render(); ?>
						</a>
					<?php endif; ?>
				</div>
				<div class="col-md-9">
					<?php echo $html ?>
				</div>
			</div>
			<?php endforeach; ?>
		<input type="hidden" name="action" value="cherry_mailchimp_save_options">
	</form>
	<div class="row cherry-mailchimp-submit-wrapper">
		<div class="col-md-6"></div>
		<div class="col-md-6 cherry-mail-chimp-action">
			<div class="cherry-mail-chimp-action-button">
				<a id="cherry-mailchimp-options-save" class="button button-secondary_ ">
					<?php echo __( 'Save options', 'cherry-mailchimp' ) ?>
					<div class="cherry-spinner-wordpress spinner-wordpress-type-2"><span class="cherry-inner-circle"></span></div>
				</a>
			</div>
			<div id="cherry-mailchimp-generate-view" class="cherry-mail-chimp-action-button">
				<!-- Shortcode -->
				<?php
				do_action( 'cherry_shortcode_generator_buttons' );
				?>
				<!-- END Shortcode -->
			</div>
		</div>
	</div>
</div>
<!-- END Options -->
<div class="cherry-clear"></div>
