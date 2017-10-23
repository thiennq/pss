<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Page.php");

class AdminPageController extends AdminController {

  public function new(Request $request, Response $response) {
    return $this->view->render($response, 'admin/page_new.pug');
  }

  public function create(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $page_id = Page::create($data);
    if ($page_id != -1) {
      return $response->withJson(array(
        'code' => 0,
        'id' => $page_id
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
    $data = Page::fetch($page_number,$perpage);
    return $this->view->render($response, 'admin/page_list.pug', array(
      'data' => $data
    ));
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $page = Page::find($id);
    if (!$page) {
      $this->view->render($response, '404.pug');
      return $response->withStatus(404);
    }
    return $this->view->render($response, 'admin/page_edit.pug', array(
			'data' => $page,
    ));
  }

  public function update(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $page = Page::update($id,$data);
    if ($page != -1) {
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
    $page = Page::remove($id);
    if ($page != -1) {
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

  public function searchPage(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $title = $params['q'];
    $id = $params['id'];
    $Page = Page::where('title', 'LIKE', '%'.$title.'%')->where('id', '!=', $id)->take(10)->get();
    if(count($Page)) {
      return $response->withJson(array(
        'code' => 0,
        'data' => $Page
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Empty'
    ));
  }

}

?>
