<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Color.php");

class AdminColorController extends AdminController {

  public function index(Request $request, Response $response) {
    $color = Color::orderBy('name', 'asc')->get();
    return $this->view->render($response, 'admin/color.pug', array(
      'data' => $color
    ));
  }

  public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
    $check = Color::where('name', $body['name'])->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $color = new Color;
    $color->name = $body['name'];
    $color->hex = $body['hex'];
    if($color->save()) {
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
    $check = Color::where('name', $body['name'])->where('id', '!=', $id)->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $color = Color::find($id);
    $color->name = $body['name'];
    $color->hex = $body['hex'];
    if($color->save()) {
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
    $color = Color::find($id);
    if($color) {
      $color->delete();
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
