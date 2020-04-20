<?php namespace Sagenda\Controllers;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
use Sagenda\webservices\sagendaAPI;
include_once( SAGENDA_PLUGIN_DIR . 'webservices/SagendaAPI.php' );

/**
* This controller will be responsible for displaying the Settings in WP backend.
*/
class AdminTokenController
{
  /**
  * @var string - name of the view to be displayed
  */
  private $view = "adminToken.twig" ;

  /**
  * Display the search events form
  * @param  object  $twig   TWIG template renderer
  */
  public function showAdminTokenSettingsPage($twig)
  {
    $tokenValue = $this->getAuthenticationToken();
    $this->saveAuthenticationToken();

    $sagendaAPI = new sagendaAPI();
    $result = $sagendaAPI->validateAccount($tokenValue);
    $color = "red";
    $connectedStatus = __( 'NOT CONNECTED', 'sagenda-wp' );

    if($result['didSucceed'] && $tokenValue != null )
    {
      $color = "green";
      $connectedStatus  = __( 'CONNECTED', 'sagenda-wp' );
    }

    return $twig->render($this->view, array(
      'SAGENDA_PLUGIN_URL'                    => SAGENDA_PLUGIN_URL,
      'sagendaAuthenticationSettings'         => __( 'Sagenda Authentication Settings', 'sagenda-wp' ),
      'sagendaAuthenticationCode'             => __( 'Sagenda Authentication Code', 'sagenda-wp' ),
      'saveChanges'                           => __( 'Save Changes', 'sagenda-wp' ),
      'currentStatus'                         => __( 'Current Status', 'sagenda-wp' ),
      'clickHereToGetYourAuthenticationCode'  => __( 'Click here to get your Authentication code.', 'sagenda-wp' ),
      'shortCodeInfo'                         => __( '<strong>[sagenda-wp]</strong> add this shortcode either in a post or page where you want to display the Sagenda form.', 'sagenda-wp' ),
      'shortCodeInfoAdvanced'                 => __( '<strong>[sagenda-wp bookableitem="my bookable item name"]</strong> if you want to display only one bookable item on that page. Pay attention : if your bookable item is name "My Item", bookableitem="my item" or bookableitem="My Item" will be correct, but "bookableitem="myitem" not!', 'sagenda-wp' ),
      'shortCodeInfoInPHP'                    => __( 'If you want to use a shortcode outside of the WordPress post or page editor, you can use this snippet to output it from the shortcode’s handler(s): <pre>echo do_shortcode([sagenda-wp])</pre>', 'sagenda-wp' ),
      'registeringInfo'                       => __( 'NOTE: You first need to register on Sagenda and then you will get a Authentication token which you will use to validate this Sagenda Plugin.', 'sagenda-wp' ),
      'readMore'                              => __( 'Read more', 'sagenda-wp' ),
      'createAccount'                         => __( 'Create a free account ', 'sagenda-wp' ),
      'aboutIntegrationTitle'                 => __( 'About integration in your WP WebSite :', 'sagenda-wp' ),
      'howToGetTheTokenTitle'                 => __( 'How to get the token :', 'sagenda-wp' ),
      'usefulLinksTitle'                      => __( 'Useful links :', 'sagenda-wp' ),
      'result'                                => $result,
      'color'                                 => $color,
      'connectedStatus'                       => $connectedStatus,
      'tokenValue'                            => $tokenValue,
    ));
  }

  /**
  * Get the authentication code from db or formulary
  * @return string  user authentication code
  */
  private function getAuthenticationToken()
  {
    if(isset($_POST['sagendaAuthenticationCode']))
    {
      return $_POST['sagendaAuthenticationCode'];
    }
    else
    {
      return get_option('mrs1_authentication_code');
    }
  }

  /**
  * Save the authentication code from formulary to db
  */
  private function saveAuthenticationToken()
  {
    if(isset($_POST['sagendaAuthenticationCode']))
    {
      // add option does nothing if already exist. So try to create, if exist update the value.
      add_option( 'mrs1_authentication_code', $_POST['sagendaAuthenticationCode'], '', 'yes' );
      update_option('mrs1_authentication_code', $_POST['sagendaAuthenticationCode']);
    }
  }
}
