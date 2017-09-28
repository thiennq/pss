<?php
  require_once("../models/Page.php");
  require_once("helper.php");
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;
  use ControllerHelper as Helper;

  class AdminPageController extends AdminController {
    public function index(Request $request, Response $response) {
      $page = Page::all();
      return $this->view->render($response, 'admin/page.pug', array(
        "data" => $page
      ));
    }

    public function showNews( Request $request, Response $response ) {
      $page = Page::all();
      return $this->view->render($response,  $response, 'admin/page-list.pug', [
        'data' => $page
      ]);
    }
    public function show(Request $request, Response $response) {
      $id = $request->getAttribute('id');
      $page = Page::find($id);
      return $this->view->render($response, 'admin/page-edit.pug', array(
        'data' => $page
      ));
    }

    public function create(Request $request, Response $response) {
      return $this->view->render($response, 'admin/page-new.pug');
    }

    public function store (Request $request, Response $response) {
  		$body = $request->getParsedBody();
      $code = Page::store($body);
      return $response->withJson(Helper::response($code));
  	}

    public function update (Request $request, Response $response) {
  		$body = $request->getParsedBody();
      $id = $request->getAttribute('id');
      $code = Page::update($id, $body);
      return $response->withJson(Helper::response($code));
  	}

    public function delete (Request $request, Response $response) {
      $id = $request->getAttribute('id');
      $Page = Page::find($id);
      if(!$Page) return $response->withJson([
        'code' => -1,
        'message' => 'Not found'
      ]);

      if($Page->delete()) {
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
