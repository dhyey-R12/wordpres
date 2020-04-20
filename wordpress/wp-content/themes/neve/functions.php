<?php
/**
 * Neve functions.php file
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      17/08/2018
 *
 * @package Neve
 */

define( 'NEVE_VERSION', '2.6.4' );
define( 'NEVE_INC_DIR', trailingslashit( get_template_directory() ) . 'inc/' );
define( 'NEVE_ASSETS_URL', trailingslashit( get_template_directory_uri() ) . 'assets/' );

if ( ! defined( 'NEVE_DEBUG' ) ) {
	define( 'NEVE_DEBUG', false );
}

/**
 * Themeisle SDK filter.
 *
 * @param array $products products array.
 *
 * @return array
 */
function neve_filter_sdk( $products ) {
	$products[] = get_template_directory() . '/style.css';

	return $products;
}

add_filter( 'themeisle_sdk_products', 'neve_filter_sdk' );

add_filter( 'themeisle_onboarding_phprequired_text', 'neve_get_php_notice_text' );

/**
 * Get php version notice text.
 *
 * @return string
 */
function neve_get_php_notice_text() {
	$message = sprintf(
		/* translators: %s message to upgrade PHP to the latest version */
		__( "Hey, we've noticed that you're running an outdated version of PHP which is no longer supported. Make sure your site is fast and secure, by %s. Neve's minimal requirement is PHP 5.4.0.", 'neve' ),
		sprintf(
			/* translators: %s message to upgrade PHP to the latest version */
			'<a href="https://wordpress.org/support/upgrade-php/">%s</a>',
			__( 'upgrading PHP to the latest version', 'neve' )
		)
	);

	return wp_kses_post( $message );
}

/**
 * Adds notice for PHP < 5.3.29 hosts.
 */
function neve_php_support() {
	printf( '<div class="error"><p>%1$s</p></div>', neve_get_php_notice_text() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( version_compare( PHP_VERSION, '5.3.29' ) <= 0 ) {
	/**
	 * Add notice for PHP upgrade.
	 */
	add_filter( 'template_include', '__return_null', 99 );
	switch_theme( WP_DEFAULT_THEME );
	unset( $_GET['activated'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	add_action( 'admin_notices', 'neve_php_support' );

	return;
}

require_once get_template_directory() . '/start.php';

require_once 'globals/utilities.php';
require_once 'globals/hooks.php';
require_once 'globals/sanitize-functions.php';
require_once 'globals/migrations.php';

require_once get_template_directory() . '/header-footer-grid/loader.php';




add_action( 'rest_api_init', 'register_api_hooks' );

function register_api_hooks() {
	register_rest_route(
		'custom-plugin', '/login/',
		array(
			'methods'  => 'GET',
			'callback' => 'login',
		)
	);
}

function login($request){
	$creds = array();
	$creds['user_login'] = $request["username"];
	$creds['user_password'] =  $request["password"];
	$creds['remember'] = true;
	$user = wp_signon( $creds, false );

	if ( is_wp_error($user) )
		echo $user->get_error_message();

	return $user;
}

// add_action( 'after_setup_theme', 'custom_login' );

function add_cors_http_header(){
	header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');

add_filter('allowed_http_origins', 'add_allowed_origins');

function add_allowed_origins($origins) {
	$origins[] = 'https://www.yourdomain.com/';
	return $origins;
}
add_filter( 'something', 'regis_options' );

// add_action( 'rest_api_init', 'register_api_hooks_appoint' );

// function register_api_hooks_appoint() {
// 	register_rest_route(
// 		'custom-plugin', '/appointment/',
// 		array(
// 			'methods'  => 'POST',
// 			'callback' => 'booking',
// 		)
// 	);
// }

// function booking($request){
// 	$creds = array();
// 	$nm="dhyey";
// 	echo $nm;
// 	if ( is_wp_error($user) )
//       echo $user->get_error_message();
//     return $user;
// }

// // add_action( 'after_setup_theme', 'custom_booking' );
add_action( 'rest_api_init', 'register_api_hooks_appoint' );

function register_api_hooks_appoint() {
	register_rest_route(
		'custom-plugin', '/appointment/',
		array(
			'methods'  => 'POST',
			'callback' => 'booking',
		)
	);
}
function booking($request){

	$nm=$_POST['post_content'];
	// $mo=$_POST['mo_no'];
	// $em=$_POST['email'];
	$dt=$_POST['post_date'];
	// $tm=$_POST['time'];
	// $ad=$_POST['address'];
	$co=$_POST['post_title'];

	$con = mysqli_connect('localhost','root','','wordpress');

	// $qy= "INSERT INTO wp_posts (post_title , post_content ) 
	// VALUES ('$nm' , '$co')";

	$qy= "INSERT INTO wp_posts (post_title, post_content, post_date ) VALUES ('$nm','$co','$dt')";

	// if ($con->query($qy) === TRUE) {
	// 	echo "New record created successfully";
	// } 
	// else {
	// 	echo "Error: " . $qy . "<br>" . $con->error;
	// }
	$field = file_get_contents('php://input');
	echo $field;
	$object = json_decode($field);
	echo ($object);

		// if ( is_wp_error($user) )
		// 	echo $user->get_error_message();
		// return $user;
}


// function booking($request){
// 	// $creds = array("Dhyey Rajpara","8469574224","dhyey@gmail.com","Hair Cut","17:04:2020","04:30:pm","raiya road ");

// 	// echo $creds[0],"\n",$creds[1],"\n",$creds[2],"\n",$creds[3],"\n",$creds[4],"\n",$creds[5],"\n",$creds[6];

// 	// $field = array();
//     // $nm="dhyey";
//     // echo $nm;
// 	// $field['user_name'] = $request["name"];
// 	// $field['user_email'] = $request["email"];
// 	// $field['user_phone_no'] =  $request["mobile_no"];
// 	// $field['user_date'] = $request["date"];
// 	// $field['user_time'] = $request["time"];
// 	// $field['user_service'] = $request["service"];
// 	// $field['user_address'] = $request["address"];
// 	// $field['remember'] = true;
// 	// $user = wp_postmeta( $creds, false );

	// $con = mysqli_connect('localhost','root','', 'wordpress');
	// if(!$con){
	// 	die('could not connect:'.mysqli_error());
	// }else{
	// 	echo" connection successful";
	// }
    // $q= "INSERT INTO wp_posts ( post_title , post_content , post_date , post_name) VALUES ('user user2','1238546792','18-Apr-2020 17:00','Hair Cut')";

 //    if ($con->query($qy) === TRUE) {
 //       echo "New record created successfully";
 //    } 
 //    else {
 //      echo "Error: " . $qy . "<br>" . $con->error;
 //    }


// }

// add_action( 'after_setup_theme', 'custom_booking' );

