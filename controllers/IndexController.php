<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class IndexController extends Controller {

  public function index(Request $request, Response $response) {
		return $this->view->render($response, 'index', array(
      "title" => "Le Minh Truyen"
		));
  }
}

?>
