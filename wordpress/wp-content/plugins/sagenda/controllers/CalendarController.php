<?php namespace Sagenda\Controllers;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
use Sagenda\webservices\sagendaAPI;
use Sagenda\Helpers\PickadateHelper;
use Sagenda\Helpers\DateHelper;
use Sagenda\Helpers\ArrayHelper;
use Sagenda\Helpers\UrlHelper;
use Sagenda\Models\Entities\Booking;
use Sagenda\Models\Entities\BookableItem;

include_once( SAGENDA_PLUGIN_DIR . 'helpers/PickadateHelper.php' );
include_once( SAGENDA_PLUGIN_DIR . 'helpers/UrlHelper.php' );
include_once( SAGENDA_PLUGIN_DIR . 'helpers/DateHelper.php' );
include_once( SAGENDA_PLUGIN_DIR . 'helpers/ArrayHelper.php' );
include_once( SAGENDA_PLUGIN_DIR . 'webservices/SagendaAPI.php' );
include_once( SAGENDA_PLUGIN_DIR . 'models/entities/Booking.php' );
include_once( SAGENDA_PLUGIN_DIR . 'models/entities/BookableItem.php' );

/**
* This controller will be responsible for displaying the free events in frontend in order to be searched and booked by the visitor.
*/
class CalendarController {

  /**
  * @var string - name of the view to be displayed
  */
  private $view = "calendar.twig" ;

  /**
  * Display the calendar form
  * @param    Array   The shortcode parameters
  * @param  object  $twig   TWIG template renderer
  */
  public function showCalendar($twig, $shorcodeParametersArray)
  {
    $sagendaAPI = new sagendaAPI();

    return $twig->render($this->view, array(
    ));
  }
}
