<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Size.php");

class AdminSizeController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Size::orderBy('name', 'asc')->get();
    return $this->view->render($response, 'admin/size.pug', array(
      'data' => $data
    ));
  }

  public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
    $check = Size::where('name', $body['name'])->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $size = new Size;
    $size->name = $body['name'];
    if($size->save()) {
      return $response->withJson([
        'code' => 0,
				'message' => 'Created'
      ]);
    }
    return $response->withJson([
      'code' => -2,
      'message' => 'Error'
    ]);
	}

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $check = Size::where('name', $body['name'])->where('id', '!=', $id)->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $size = Size::find($id);
    $size->name = $body['name'];
    if($size->save()) {
      return $response->withJson([
        'code' => 0,
				'message' => 'Updated'
      ]);
    }
    return $response->withJson([
      'code' => -2,
      'message' => 'Error'
    ]);
	}

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $size = Size::find($id);
    if($size) {
      $size->delete();
      return $response->withJson([
        'code' => 0,
				'message' => 'Delete'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'Error'
    ]);
	}
}

?>
