<?php namespace Sagenda\Controllers;

/**
* This controller will be responsible for displaying confirmation / information message
*/
class InformationMessageController
{
  /**
  * @var string - name of the view to be displayed
  */
  private $view = "informationMessage.twig" ;

  /**
  * Display the confirmation view
  * @param  object  $twig       TWIG template renderer
  * @param  object  $booking    Booking object
  */
  public function showMessage($twig, $booking, $result)
  {
    return $twig->render($this->view, array(
      'booking'                       => $booking,
      'result'                        => $result,
      'back'                          => __('Back', 'sagenda-wp'),
      'message'                       => __('You successfully subscribed to the event.', 'sagenda-wp'),
      'pleasePay'                     => __('Please pay for your event', 'sagenda-wp'),
      'pay'                           => __('Pay', 'sagenda-wp'),
      'bootstrapAlertType'            => "success",
    ));
  }
}
