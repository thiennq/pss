<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('view.php');

class AdminController {
  protected $ci;

  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
    $detect = new Mobile_Detect;
    $deviceType = 'desktop';
    if ($detect->isMobile()){
      $deviceType = 'phone';
    }
    if ($detect->isTablet()) {
      $deviceType = 'tablet';
    }
    $themeDir = getThemeDir();
    $path =  ROOT . '/views/';

    $this->view = new View(array(
    	'path' => $path,
      'device' => $deviceType,
    ));
  }
}

?>
