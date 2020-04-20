<?php namespace Sagenda;

use Sagenda\Controllers\SearchController;
use Sagenda\Controllers\SubscriptionController;
use Sagenda\Controllers\AdminTokenController;
use Sagenda\Controllers\InformationMessageController;
use Sagenda\Controllers\CalendarController;
use Sagenda\Helpers\ArrayHelper;

include_once( SAGENDA_PLUGIN_DIR . 'controllers/SearchController.php' );
include_once( SAGENDA_PLUGIN_DIR . 'controllers/SubscriptionController.php' );
include_once( SAGENDA_PLUGIN_DIR . 'controllers/CalendarController.php' );
include_once( SAGENDA_PLUGIN_DIR . 'controllers/AdminTokenController.php' );
include_once( SAGENDA_PLUGIN_DIR . 'controllers/InformationMessageController.php' );
include_once( SAGENDA_PLUGIN_DIR . 'helpers/ArrayHelper.php' );

// TODO : did we need include once if we already use namespace?

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
* This class instanciate most of the needed objects needed to make sagenda's wp plugin run.
*/
class initializer
{

  /**
  * Responsible to initialize the frontend views
  * @param    Array   The shortcode parameters
  * @return   the view according to TWIG rendering
  */
    function initFrontend($shorcodeParametersArray)
    {
        $twig = self::initTwig();
        $shortcode = ArrayHelper::getElementIfSetAndNotEmpty($shorcodeParametersArray, 'view');

        if (strcasecmp($shortcode, "calendar") == 0) {
            $calendarController = new CalendarController();
            return $calendarController->showCalendar($twig, $shorcodeParametersArray);
        } else {
            $searchController = new SearchController();
            return $searchController->showSearch($twig, $shorcodeParametersArray);
        }
    }

  /**
  * Responsible to initialize the backend view
  * @return the view according to TWIG rendering
  */
    function initAdminSettings()
    {
        $twig = self::initTwig();
        $adminTokenController = new AdminTokenController();
        return $adminTokenController->showAdminTokenSettingsPage($twig);
    }

  /**
  * Responsible to initialize the TWIG instance (template rendering)
  * @return an instanciate TWIG object
  */
    public static function initTwig()
    {
        if( class_exists('Twig_Autoloader') === false ) {
          include_once(SAGENDA_PLUGIN_DIR.'/assets/vendor/twig/lib/Twig/Autoloader.php');
        }
        
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(SAGENDA_PLUGIN_DIR.'/views');

        // TODO : should we use caching system?
        //$twig = new Twig_Environment($loader, array('cache' => '/path/to/compilation_cache',));

        // TODO : remove debug before production
        $twig = new \Twig_Environment($loader, array('debug' => true,)
        );
      //$twig->addExtension(new Twig_Extension_Debug());
        return $twig;
    }
}
