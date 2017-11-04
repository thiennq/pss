<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Coupon.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminCouponController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $coupon = Coupon::all();
    return $this->view->render($response, 'admin/coupon',array(
        'coupon' => $coupon
    ));
  }

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    error_log("dsfd");
    $coupon = Coupon::store($data);
    $result = Helper::response($coupon);
    return $response->withJson($result, 200);
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $coupon = Coupon::find($id);
    $result = Helper::response($coupon);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $data = $request->getParsedBody();
    $code = Coupon::update($id, $data);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Coupon::remove($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
