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
	session_start();

	$creds['user_login'] = $request["username"];
	$creds['user_password'] =  $request["password"];

	$_SESSION['se_login'] = $creds['user_login'];
	$_SESSION['se_password'] = $creds['user_password'];

	if($_SESSION['se_login'] == "" || $_SESSION['se_password'] == ""){

		$required = "Unauthorized";
		http_response_code(401);
		echo $required;
		exit;
	}

	// echo $_SESSION['se_login'];
	// echo $_SESSION['se_password'];

	// echo $_SESSION['username['];
	// echo $request["username"];
	// print_r($creds);
	// $creds['remember'] = true;
	$user = wp_signon( $creds, false );

	if ( is_wp_error($user) )
		echo $user->get_error_message();
	return $user;
}

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

add_action( 'rest_api_init', 'register_api_hooks_appoint' );

//////////////////////////////////////////*appointment form*//////////////////////////////////////////

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

	$nm=$_POST['post_title']; //name
	$mo=$_POST['to_ping'];		//mobile
	$em=$_POST['post_content'];	//email
	$dt=$_POST['post_date'];	//date
	// $ad=$_POST['address'];		//address
	$se=$_POST['post_excerpt'];	//service
	$pinged = $_POST['pinged']; //userID
	$ID = $_GET['ID']; //ID
	// var_dump($ID);
	// echo $pinged;
	// var_dump($pinged)
	// $con = mysqli_connect('localhost','root','','wordpress');

	// $field = file_get_contents('php://input');
	// echo $field;
	// $object = json_decode($field);
	// echo ($object);

	if(!preg_match("/^[a-zA-Z]+$/", $nm) ||  !preg_match("/^[0-9]{10}$/", $mo) || !preg_match("/^[a-zA-Z0-9.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $em) || $dt == "" || $se == "" ){
		$required = "BAD Request";
		http_response_code(400);
		echo $required;
		exit;
		// echo "error"; 
	}
	else{
		$con = mysqli_connect('localhost','root','','wordpress');

		$qy= "INSERT INTO wp_posts (post_title, post_content, post_excerpt, post_date, to_ping, pinged ) 
		VALUES ('$nm','$em','$se','$dt','$mo', '$pinged')";

		if ($con->query($qy) === TRUE) {
			$last_id = mysqli_insert_id($con);
			// echo $last_id;
			echo $last_id ;

		} 
		else {
			echo "Error: " . $qy . "<br>" . $con->error;
		}
	}
}
	// if( $nm == "" ||  $mo == "" || $em == "" || $dt == "" || $se == "" ){
	// 	// echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	// }
	// if(!preg_match("/^[0-9]{10}$/", $mo)){
	// 	echo "mobile no ,";
	// }
	// if(!preg_match("/^[a-zA-Z0-9.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $em)){
	// 	echo " email";
	// }
	// if(!preg_match("/^[a-zA-Z]+$/", $nm)){
	// 	echo " name";
	// }

//////////////////////////////////////////*login*//////////////////////////////////////////

add_action( 'rest_api_init', 'register_api_hooks_logout' );

function register_api_hooks_logout() {
	register_rest_route(
		'custom-plugin', '/logout/',
		array(
			'methods'  => 'GET',
			'callback' => 'logout',
		)
	);
}

function logout(){
	session_destroy();
	echo "destroy";
}

//////////////////////////////////////////*data display*//////////////////////////////////////////

add_action( 'rest_api_init', 'register_api_hooks_book_appoint' );

function register_api_hooks_book_appoint() {
	register_rest_route(
		'custom-plugin', '/booking/',
		array(
			'methods'  => 'GET',
			'callback' => 'appointment',
		)
	);
}

function appointment($request){
	// echo "hello";

	$pinged = $_GET['pinged'];
	// var_dump($request);

	$json = $request->get_headers();
	// var_dump($json);

	// echo " <h1>dhyey</h1> ";
	// echo "<br>";

	$user_req_email = '';
	foreach ($json as $key => $feature) {
		if($key == 'auth'){
			$user_email = $feature[0];
		}
	}

	if($user_email){
		echo $user_email;
		exit();
	}

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "wordpress";

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$sql = "SELECT id, post_date, post_content, post_title, post_excerpt, to_ping FROM wp_posts WHERE pinged = $pinged";
	$result = mysqli_query($conn, $sql);
	$respnose = []; 

	// print_r($_SERVER);

	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			// $sql = "SELECT ID, post_date, post_content, post_title, post_excerpt, to_ping FROM wp_posts WHERE pinged = $pinged";
			// echo "Name: "  . $row["post_title"].  "\n". 
			// // "ID: "  . $row["ID"].  "\n".
			// "Email: "  . $row["post_content"].  "\n". 
			// "Date:"   . $row["post_date"]. "\n".
			// "Service: "  . $row["post_excerpt"].  "\n". 
			// "Mobile:". $row["to_ping"]."\n" ;
			// $json_response = json_encode($row);
			// echo $json_response
			// exit();
			array_push($respnose,$row);
		}
		echo json_encode($respnose);
		exit();
	}
	else 
	{
		echo "This User is not valid to see his bookings"; 
	}
	mysqli_close($conn);
}

//////////////////////////////////////////*update*//////////////////////////////////////////
add_action( 'rest_api_init', 'register_api_hooks_update' );

function register_api_hooks_update() {
	register_rest_route(
		'custom-plugin', '/update/',
		array(
			'methods'  => 'PUT',
			'callback' => 'record',
		)
	);
}

function record(){
// 	$ID = $_GET['ID']; //ID

// 	$servername = "localhost";
// 	$username = "root";
// 	$password = "";
// 	$dbname = "wordpress";

// // Create connection
// 	$conn = new mysqli($servername, $username, $password, $dbname);
// // Check connection
// 	if ($conn->connect_error) {
// 		die("Connection failed: " . $conn->connect_error);
// 	}

// 	// echo $nm;

// 	// $postdata = file_get_contents("php://input");
	
// 	parse_str( file_get_contents("php://input"), $_PUT );
// 	print_r($_PUT);
// 	var_dump($ID);

// 	$sql=mysqli_query($conn, "UPDATE wp_posts SET
// 		post_title='".$_PUT['post_title']."', post_content='".$_PUT['post_content']."',to_ping='".$_PUT['to_ping']."',post_date='".$_PUT['post_date']."',post_excerpt='".$_PUT['post_excerpt']."' WHERE ID = '$ID' ");

// 	if($sql==true){ 
//         echo "Records was updated successfully.";

//     } else{ 
//         echo "ERROR: Could not able to execute $sql. " . $mysqli->error;
//     } 
	$ID = $_GET['ID'];
	var_dump($ID);

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "wordpress";

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	parse_str(file_get_contents('php://input'),$_PUT);
    // print_r($_PUT);

	//
	$json = json_encode($json_array);
	echo $json;;
	echo $json;

	$sql = mysqli_query($conn, "UPDATE wp_posts SET
		post_title='".$_PUT['post_title']."', post_content='".$_PUT['post_content']."',to_ping='".$_PUT['to_ping']."',post_date='".$_PUT['post_date']."',post_excerpt='".$_PUT['post_excerpt']."' WHERE ID = '$ID' " );

	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . $conn->error;
	}
	$conn->close();
}

//////////////////////////////////////////*delete*//////////////////////////////////////////

add_action( 'rest_api_init', 'register_api_hooks_delete' );

function register_api_hooks_delete() {
	register_rest_route(
		'custom-plugin', '/delete/',
		array(
			'methods'  => 'DELETE',
			'callback' => 'deletedata',
		)
	);
}

function deletedata(){
	$ID = $_GET['ID']; //ID

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "wordpress";

// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}


	$query=mysqli_query($conn, "DELETE FROM wp_posts WHERE ID = '$ID' ");

	if($query==true){ 
		echo "Records was delete successfully.";

	} else{ 
		echo "ERROR:Records is not delete . " . $mysqli->error;
	} 
}

//////////////////////////////////////////*signup*//////////////////////////////////////////

add_action( 'rest_api_init', 'register_api_hooks_singup');

function register_api_hooks_singup() {
	register_rest_route(
		'custom-plugin', '/signup/',
		array(
			'methods'  => 'POST',
			'callback' => 'signupdata',
		)
	);
}
function signupdata($request){

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "wordpress";

	$ul = $_POST['user_login'];
	$up = $_POST['user_pass'];
	// $md = md5($up);
	$user_pass = wp_hash_password("$up");
	$ue = $_POST['user_email'];
	$nk = $_POST['user_nicename'];
	$d= date("Y-m-d h:i:sa");

	var_dump($nk);
	var_dump($up);
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$qy = "INSERT into wp_users (user_login, user_pass, user_email, user_registered, user_nicename, display_name) 
	VALUES ('$ul','$user_pass','$ue','$d','$nk','$nk')";

	if (mysqli_query($conn, $qy)) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $qy . "<br>" . mysqli_error($conn);
	}
}

//////////////////////////////////////////*forgot*//////////////////////////////////////////
add_action( 'rest_api_init', 'register_api_hooks_forgot' );

function register_api_hooks_forgot() {
	register_rest_route(
		'custom-plugin', '/forgot/',
		array(
			'methods'  => 'PUT',
			'callback' => 'pass',
		)
	);
}

function pass(){

	$user_email = $_GET['user_email'];
	$user_pass = $_POST['user_pass'];
	$user_pass = wp_hash_password("$user_pass");
	var_dump($user_pass);

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "wordpress";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	parse_str( file_get_contents("php://input"), $_PUT );
	print_r($_PUT);
	var_dump($ID);

	$sql=mysqli_query($conn, "UPDATE wp_users SET user_pass='$user_pass' WHERE  user_email = '$user_email'");

	if($sql==true){ 
		echo "Records was updated successfully.";

	} else{ 
		echo "ERROR: Could not able to execute $sql. " . $mysqli->error;
	}
}

	// $user_login = $_GET['user_login'];
	// $user_email = $_GET['user_email'];
	// var_dump($user_login);
	// var_dump($user_email);
	// $up = $_POST['user_pass'];
	// // $md = md5($up);
	// // $user_pass = wp_hash_password("$up");
	// var_dump($user_pass);

	// $servername = "localhost";
	// $username = "root";
	// $password = "";
	// $dbname = "wordpress";

	// $conn = new mysqli($servername, $username, $password, $dbname);
	// if ($conn->connect_error) {
	// 	die("Connection failed: " . $conn->connect_error);
	// }
	// parse_str(file_get_contents('php://input'),$_PUT);
 //    // print_r($_PUT);

	// //
	// $json_array[] = $_PUT;
 //    $json = json_encode($json_array);
 //    echo $json;

	// // $sql = mysqli_query($conn, "UPDATE wp_users SET user_pass='$up' WHERE user_email = '$user_email' ");

 // 	$sql = mysqli_query($conn, "UPDATE wp_posts SET	user_pass='".$_PUT['user_pass']."' WHERE  user_email = '$user_email'");
	// if ($conn->query($sql) === TRUE) {	
	// 	echo "Record updated successfully";
	// } else {
	// 	echo "Error updating record: " . $conn->error;
	// }
	// $conn->close();