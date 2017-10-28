<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Blog.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminBlogController extends AdminController {

  public function create(Request $request, Response $response) {
    return $this->view->render($response, 'admin/blog_new.pug');
  }

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $code = Blog::create($data);
    $result = Helper::response($code);
		return $response->withJson($result, 200);
  }

  public function fetch(Request $request, Response $response) {
    $data = Blog::orderBy('updated_at', 'desc')->get();
    return $this->view->render($response, 'admin/blog.pug', array(
      'data' => $data
    ));
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $blog = Blog::find($id);
    if (!$blog) return $response->withStatus(302)->withHeader('Location', '/404');
    return $this->view->render($response, 'admin/blog_edit.pug', array(
      'data' => $blog
    ));
  }

  public function update(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $code = Blog::update($id,$data);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Blog::remove($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

}

?>
