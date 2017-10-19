<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Page.php");

class PageController extends Controller {

  public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $page = Page::where('handle', $handle)->first();

    return $this->view->render($response, 'page.pug', array(
      'page' => $page
    ));
  }
}

?>
