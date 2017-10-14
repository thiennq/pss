<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Blog.php");

class AdminBlogController extends AdminController {

  public function new(Request $request, Response $response) {
    return $this->view->render($response, 'admin/blog_new.pug');
  }

  public function create(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $blog_id = Blog::create($data);
    if ($blog_id != -1) {
      return $response->withJson(array(
        'code' => 0,
        'id' => $blog_id
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Error'
    ));
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
    $blog = Blog::get($id);
    return $this->view->render($response, 'admin/blog_edit.pug', array(
			'data' => $blog,
    ));
  }

  public function update(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $blog = Blog::update($id,$data);
    if ($blog != -1) {
      return $response->withJson(array(
        'code' => 0,
        'message' => 'Updated'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Not found'
    ));
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $blog = Blog::delete($id);
    if ($blog != -1) {
      return $response->withJson(array(
        'code' => 0,
        'message' => 'Deleted'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Not found'
    ));
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
