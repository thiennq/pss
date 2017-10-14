<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Meta.php");
require_once("../models/Price.php");
require_once("../models/Filter.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminFilterController extends AdminController {

	public function index(Request $request, Response $response) {
		$data = Filter::fetch();
		error_log(json_encode($data));
		return $this->view->render($response, 'admin/setting_filter.pug', [
			'data' => $data
		]);
	}

	public function get(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$code = Filter::get($id);
		if ($code == -2) $result = Helper::response($code);
		else $result = Helper::responseData($code);
		return $response->withJson($result, 200);
	}

	public function store (Request $request, Response $response) {
		$body = $request->getParsedBody();
		$code = Filter::store($body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function update (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$code = Filter::update($id, $body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function delete(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$code = Filter::remove($id);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}
}
