<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Blog.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminBlogController extends AdminController {

  public function new(Request $request, Response $response) {
    return $this->view->render($response, 'admin/blog_new.pug');
  }

  public function create(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $code = Blog::create($data);
    $result = Helper::response($code);
		return $response->withJson($result, 200);
  }

  public function fetch(Request $request, Response $response) {
    $page_number = 1;
    $params = $request->getQueryParams();
    if($params['page']) $page_number = $params['page'];
    $perpage = 12;
    $data = Blog::fetch($page_number,$perpage);
    return $this->view->render($response, 'admin/blog_list.pug', array(
      'data' => $data
    ));
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Blog::get($id);
    if ($code == -2) {
      $result = Helper::response($code);
      $response->withJson($result, 200);
    }
    return $this->view->render($response, 'admin/blog_edit.pug', array(
      'data' => $code
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

  public function searchBlog(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $title = $params['q'];
    $id = $params['id'];
    $Blog = Blog::where('title', 'LIKE', '%'.$title.'%')->where('id', '!=', $id)->take(10)->get();
    if(count($Blog)) {
      return $response->withJson(array(
        'code' => 0,
        'data' => $Blog
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Empty'
    ));
  }

}

?>
