<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Page.php");

class PageController extends Controller {

  public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $page = Page::where('handle', $handle)->first();
    if (!$page) {
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }
    return $this->view->render($response, 'page', array(
      'page' => $page
    ));
  }

  public function PageNotFound(Request $request, Response $response) {
    $this->view->render($response, '404');
    return $response->withStatus(404);
  }
}

?>
