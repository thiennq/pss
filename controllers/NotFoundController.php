<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class NotFoundController extends Controller {
  public function show(Request $request, Response $response) {
    return $this->view->render($response, "404.pug");
  }
}
