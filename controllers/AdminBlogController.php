<?php

  require_once("../models/Blog.php");
  require_once("helper.php");
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;
  use ControllerHelper as Helper;

  class AdminBlogController extends AdminController {
    public function index(Request $request, Response $response) {
      $blog = Blog::all();
      return $this->view->render($response, $response, 'admin/blog.pug', array(
        "data" => $blog
      ));
    }

    public function store (Request $request, Response $response) {
  		$body = $request->getParsedBody();
      $code = Blog::store($body);
      return $response->withJson(Helper::response($code));
  	}

    public function update (Request $request, Response $response) {
  		$body = $request->getParsedBody();
      $id = $request->getAttribute('id');
      $code = Blog::update($id, $body);
      return $response->withJson(Helper::response($code));
  	}

    public function delete (Request $request, Response $response) {
      $id = $request->getAttribute('id');
      $blog = Blog::find($id);
      if(!$blog) return $response->withJson([
        'code' => -1,
        'message' => 'Not found'
      ]);
      if ($blog->delete()) {
        return $response->withJson([
          'code' => 0,
          'message' => 'Deleted'
        ]);
      }
      return $response->withJson([
        'code' => -2,
        'message' => 'Error'
      ]);
  	}
  }
?>
