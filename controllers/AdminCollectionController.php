<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Collection.php");
require_once("../models/Product.php");
require_once("../models/CollectionTag.php");
require_once("helper.php");
use ControllerHelper as Helper;

class AdminCollectionController extends AdminController {

	public function index(Request $request, Response $response) {
		$data = Collection::orderBy('title')->get();
		foreach ($data as $key => $value) {
			$value->image = convertImage($value->image, 240);
		}
		return $this->view->render($response, 'admin/collection.pug', array(
			'collections' => $data
		));
	}

	public function create(Request $request, Response $response) {
		$collection = Collection::where('parent_id', -1)->orderBy('breadcrumb', 'asc')->get();
		return $this->view->render($response, 'admin/collection_new.pug', array(
			'collection' => $collection
		));
	}

	public function show(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$data = Collection::find($id);
		if (!$data) {
			$this->view->render($response, '404.pug');
			return $response->withStatus(404);
		}
		$collection = Collection::where('id', '!=', $id)->get();
		return $this->view->render($response, 'admin/collection_edit.pug', array(
			'data' => $data,
			'collection' => $collection
		));
	}

	public function store (Request $request, Response $response) {
		$body = $request->getParsedBody();
		$arr = [
			'title' => $body['title']
		];
		$checkNull = Helper::checkNull($arr);
		if ($checkNull) {
			return $response->withJson($checkNull, 200);
		}
		$code = Collection::store($body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function update (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$arr = [
			'title' => $body['title']
		];
		$checkNull = Helper::checkNull($arr);
		if ($checkNull) {
			return $response->withJson($checkNull, 200);
		}
		$code = Collection::update($id, $body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function delete(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$code = Collection::remove($id);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}
}

?>
