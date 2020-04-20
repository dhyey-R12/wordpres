<?php defined('ABSPATH') or die('No script kiddies please!');
/**
 * @wordpress-plugin
 * Plugin Name:       Sagenda
 * Plugin URI:        http://www.sagenda.com/
 * Description:       Sagenda is a free Online Booking / Scheduling / Reservation System, which gives customers the opportunity to choose the date and the time of an appointment according to your preferences.
 * Version:           1.3.4
 * Author:            sagenda
 * Author URI:        http://www.sagenda.com/
 * License:           GPLv2
 * Domain Path:       /languages
 */

/**
 * Plugin path management - you can re-use those constants in the plugin
 */
define('SAGENDA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SAGENDA_PLUGIN_URL', plugins_url('/', __FILE__));


/**
 * Load tranlations of the plugin
 */
function sagenda_load_textdomain()
{
	load_plugin_textdomain('sagenda-wp', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'sagenda_load_textdomain');

/**
 * Shortcode management
 * @param  string  $atts   a list of parameter allowing more options to the shortcode
 */
function sagenda_main($atts)
{
	if (is_CURL_Enabled() === true) {
		if (is_PHP_version_OK() == true) {
			include_once(SAGENDA_PLUGIN_DIR . 'initializer.php');
			$initializer = new Sagenda\Initializer();
			return $initializer->initFrontend($atts);
		}
	}
}
add_shortcode('sagenda-wp', 'sagenda_main');

/**
 * Check the version of PHP used by the server. Display a message in case of error. Unirest project require php >=5.4
 * @return true if version is ok, false if version is too old.
 */
function is_PHP_version_OK()
{
	if (version_compare(phpversion(), '5.4.0', '<')) {
		echo "You are runing an outdated version of PHP !" . "<br>";
		echo "Your version is : " . phpversion() . "<br>";
		echo "Minimal version : " . "5.4.0<br>";
		echo "Recommended version : 5.6 - 7.x  (all version <5.6 are \"End of life\" and don't have security fixes!)" . "<br>";
		echo "Please read offical PHP recommendations <a href=\"http://php.net/supported-versions.php\">http://php.net/supported-versions.php</a><br>";
		echo "Please update your PHP version form your admin panel. If you don't know how to do it please contact your WebMaster or your Hosting provider!";
		return false;
	}
	return true;
}

/**
 * Check if CURL is enabled on the server, required for calling web services.
 * @return true if curl is enabled
 */
function is_CURL_Enabled()
{
	if (!function_exists('curl_version')) {
		echo "You need to install cURL module in your PHP server in order to make WebServices calls!" . "<br>";
		echo "More info there : <a href=\"http://php.net/manual/en/curl.installation.php\">http://php.net/manual/en/curl.installation.php</a><br>";
		return false;
	}
	return true;
}

/**
 * Include CSS, JavaScript in the head section of the plugin.
 */
function head_code_sagenda()
{
	// bootstrap
	$headcode = '<link rel="stylesheet" href="' . SAGENDA_PLUGIN_URL . 'assets/vendor/bootstrap/bootstrap-wrapper.css" >';
	// bootstrap validator
	$headcode .= '<script src="' . SAGENDA_PLUGIN_URL . 'assets/vendor/bootstrap-validator/validator.min.js"></script>';

	// uikit Accordion
	$headcode .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/css/uikit.min.css" />';
	$headcode .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit.min.js"></script>';
	$headcode .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit-icons.min.js"></script>';

	// pickadate
	$headcode .= '<link rel="stylesheet" href="' . SAGENDA_PLUGIN_URL . 'assets/vendor/pickadate/lib/compressed/themes/default.css" id="theme_base">';
	$headcode .= '<link rel="stylesheet" href="' . SAGENDA_PLUGIN_URL . 'assets/vendor/pickadate/lib/compressed/themes/default.date.css" id="theme_date">';
	$headcode .= '<script src="' . SAGENDA_PLUGIN_URL . 'assets/vendor/pickadate/lib/compressed/picker.js"></script>';
	$headcode .= '<script src="' . SAGENDA_PLUGIN_URL . 'assets/vendor/pickadate/lib/compressed/picker.date.js"></script>';
	$headcode .= '<script src="' . SAGENDA_PLUGIN_URL . 'assets/vendor/pickadate/lib/legacy.js"></script>';

	echo $headcode;
}

// include custom jQuery
function sagenda_include_custom_jquery()
{
	// wp_deregister_script('jquery');
	wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js', array('jquery'), '3.4.1', true);
	wp_enqueue_script('jquery', 'https://cdn.jsdelivr.net/npm/bellows-ui@6.0.1/dist/bellows.min.js"></script>', array(), '', true);

}
add_action('wp_enqueue_scripts', 'sagenda_include_custom_jquery');

/**
 * Add it in the frontend
 */
add_action('wp_head', 'head_code_sagenda');

/**
 * Action hooks for adding admin page
 */
function sagenda_admin()
{
	if (is_CURL_Enabled() === true) {
		if (is_PHP_version_OK() === true) {
			include_once(SAGENDA_PLUGIN_DIR . 'initializer.php');
			$initializer = new Sagenda\Initializer();
			echo $initializer->initAdminSettings();
		}
	}
}

function sagenda_admin_actions()
{
	add_options_page("Sagenda", "Sagenda", "manage_options", "Sagenda", "sagenda_admin");
}
add_action('admin_menu', 'sagenda_admin_actions');
