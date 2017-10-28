<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Redirect.php");

class AdminDirectController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Redirect::all();
    return $this->view->render($response, 'admin/redirect', array(
      'data' => $data
    ));
  }

  public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
    $check = Redirect::where('old', $body['old_url'])->where('new', $body['new_url'])->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $redirect = new Redirect;
    $redirect->old = $body['old_url'];
    $redirect->new = $body['new_url'];
    $redirect->created_at = date('Y-m-d H:i:s');
    $redirect->updated_at = date('Y-m-d H:i:s');
    if($redirect->save()) {
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
    $check = Redirect::where('old', $body['old_url'])->where('new', $body['new_url'])->where('id', '!=', $id)->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $redirect = Redirect::find($id);
    $redirect->old = $body['old_url'];
    $redirect->new = $body['new_url'];
    $redirect->updated_at = date('Y-m-d H:i:s');
    if($redirect->save()) {
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
    $redirect = Redirect::find($id);
    if($redirect) {
      $redirect->delete();
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
