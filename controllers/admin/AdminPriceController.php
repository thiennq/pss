<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Price.php");

class AdminPriceController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Price::orderBy('id', 'asc')->get();
    return $this->view->render($response, 'admin/price', array(
      'data' => $data
    ));
  }

  public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
    $check = Price::where('price', $body['price'])->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $price = new Price;
    $price->price = $body['price'];
    if($price->save()) {
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
    $check = Price::where('price', $body['price'])->where('id', '!=', $id)->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $price = Price::find($id);
    $price->price = $body['price'];
    if($price->save()) {
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
    $price = Price::find($id);
    if($price) {
      $price->delete();
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
