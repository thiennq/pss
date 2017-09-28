<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Customer.php");
require_once("../models/Subscribe.php");

class CustomerController extends Controller {

  public function checkPhone(Request $request, Response $response) {
    $officical = 'officical';
    $body = $request->getParsedBody();
		$phone = $body['phone'];
    $data = Customer::where('phone', $phone)->where('type', $officical)->first();
    if($data) {
      return json_encode(array(
  			'code' => 0,
  			'customer' => $data
  		));
    } else {
      return json_encode(array(
  			'code' => -1,
  			'message' => 'Not found'
  		));
    }
  }

  public function subscribe(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $email = $body['email'];
    $code = Subscribe::store($email);
    return $response->withJson([
      'code' => $code
    ]);
  }
}

?>
