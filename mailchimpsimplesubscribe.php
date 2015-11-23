<?php
/*
Plugin Name: MailChimp Simple Subscribe
Plugin URI: http://subscribeplugin.technocrat.org.ua/
Description: ShortCode for MailChimp
Version: 0.1
Author: Serhii Osadchyi
Author URI: http://subscribeplugin.technocrat.org.ua/
*/

		
if (!class_exists('mailChimpSimpleSubscribe')) {
	
	require_once('helpers/MailChimp.php'); //simple api class from https://github.com/drewm/mailchimp-api/blob/master/src/Drewm/MailChimp.php 
	
	class mailChimpSimpleSubscribe {
		
		private $ApiKey;
		
		function __construct() {			
			add_action( 'admin_menu', array(&$this, 'adminMenu') ); // create menu item
			
			$this->ApiKey = get_option('MailChimpApiKey'); // get Api Key	
			
			add_shortcode( 'simplesubscribe', array(&$this, 'subscribeView') );	// create shortcode	
			
			add_action( 'wp_ajax_simplesubscribe', array(&$this, 'subscriberAdd') ); // wp-ajax subscribe action
			add_action( 'wp_ajax_nopriv_simplesubscribe', array(&$this, 'subscriberAdd') );
		}
		
		private function saveOptions() { // save api key
			if (empty($_POST['action'])||$_POST['action']!='Save') return;
			$apikey = !empty($_POST['apikey']) ? $_POST['apikey'] : '';			
			update_option( 'MailChimpApiKey', mysql_real_escape_string($apikey) );
			$this->ApiKey = get_option('MailChimpApiKey');
		}		
		
		public function adminMenu() { // create menu item		
			add_options_page( 'Simple Subscribe Options', 'Simple Subscribe', 'manage_options', 'simple-subscribe-options', array(&$this, 'optionsPage') );
		}
		
		public function optionsPage() { // template for menu
			if ( !current_user_can( 'manage_options' ) )
				wp_die( __( 'Access denied.' ) );
						
			$this->saveOptions();
			
			$apiKey = !empty($this->ApiKey) ? $this->ApiKey : '';
			require_once ('views/options_page.php');
		}
		
		public function subscribeView( $atts ) { 	// create shortcode	
			 $args = shortcode_atts( array(
				'apikey'			=>$this->ApiKey,
				'button' 			=> 'Subscribe',
				'list' 				=> '0', //list id
				'placeholder' 		=> 'Please, input your email',
				'class' 			=> '',
				'id'				=>'simple_subscribe_form',
				'success_message'	=>'Subscribe successful',
				'fail_message'		=>'Subscribe failed',
			), $atts );
			extract($args);
			
			wp_register_style( 'simple-subscribe-style', plugins_url('/css/style.css', __FILE__) );
			wp_enqueue_style( 'simple-subscribe-style' );
			
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js');
			wp_enqueue_script( 'jquery' );
			
			wp_register_script( 'simple-subscribe-script', plugins_url('/js/script.js', __FILE__) );
			wp_localize_script( 'simple-subscribe-script', 'param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  
			wp_enqueue_script( 'simple-subscribe-script' );
			
			require ('views/shortcode.php');
		}
		
		public function subscriberAdd() { 	// add subscriber
			$return = array('status'=>'failed'); //default error
			if (!empty($_POST['email'])&&!empty($_POST['list'])) {
				$pattern = "/[a-zA-Z0-9_\-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/"; // Думав використати filter_var, але вирішив не відкидати більш старі версії php			
				$email = mysql_real_escape_string($_POST['email']);
				$list = mysql_real_escape_string($_POST['list']);
				$apikey = !empty($_POST['apikey']) ? mysql_real_escape_string($_POST['apikey']) : $this->ApiKey;
				
				if (preg_match($pattern, $email)&&!empty($list)) {
					$mailChimpAPI_obj = new Drewm\MailChimp($apikey); 
					$result = $mailChimpAPI_obj->call('/lists/subscribe', array('id'=>$list, 'email'=>array('email'=>$email, 'euid'=>time(), 'leid'=>time()) ), 20); // call api					
					if (!empty($result['leid']))
						$return = array('status'=>'success');
				}
			}
			wp_send_json($return);
		}
		
	}
	
	$mailChimpSimpleSubscribe = new mailChimpSimpleSubscribe();
	
}
