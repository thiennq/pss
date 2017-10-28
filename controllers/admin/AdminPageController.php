<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Page.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminPageController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $data = Page::orderBy('updated_at', 'desc')->get();
    return $this->view->render($response, 'admin/page', array(
      'data' => $data
    ));
  }

  public function create(Request $request, Response $response) {
    return $this->view->render($response, 'admin/page_new.pug');
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Page::create($body);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $page = Page::find($id);
    if (!$page) return $response->withStatus(302)->withHeader('Location', '/404');
    return $this->view->render($response, 'admin/page_edit', array(
			'data' => $page
    ));
  }

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $code = Page::update($id, $body);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Page::remove($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

}

?>
