<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Blog.php");

class BlogController extends Controller {

  public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $blog = Blog::where('handle', $handle)->first();

    $page_number = 1;
    $params = $request->getQueryParams();
    if($params['page']) $page_number = $params['page'];

    return $this->view->render($response, 'blog.pug', array(
      'blog' => $blog,
      'page_number' => $page_number
    ));
  }
}

?>
