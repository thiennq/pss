<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Meta.php");
require_once("../models/Brand.php");
require_once("../models/Color.php");
require_once("../models/Price.php");
require_once("../models/Material.php");


class AdminFilterController extends AdminController {

	public function index(Request $request, Response $response) {
		$color = Color::all();
		$price = Price::all();
		$material = Material::all();
		return $this->view->render($response, 'admin/product-info.pug', array(
			'color' => $color,
			'price' => $price,
			'material' => $material
		));
	}

	public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
		$type = $body['type'];

		if($type == 'color') {
			$color = $body['color'];
			$hex = $body['hex'];
			$result = Color::store($color, $hex);
		} else if($type == 'price') {
			$price = $body['price'];
			$result = Price::store($price);
		} else if($type == 'material') {
			$material = $body['material'];
			$result = Material::store($material);
		}
		if($result) {
			return $response->withJson(array(
				'code' => 0,
				'message' => 'Created',
				'id' => $result
			));
		}
		return $response->withJson(array(
			'code' => -1,
			'message' => 'Exists'
		));
	}

	public function delete(Request $request, Response $response) {
		$body = $request->getParsedBody();
		$type = $body['type'];
		$id = $body['id'];
		if($type == 'color') Color::find($id)->delete();
		else if($type == 'price') Price::find($id)->delete();
		else if($type == 'material') Material::find($id)->delete();
		return $response->withJson(array(
			'code' => 0,
			'message' => 'Deleted',
		));
	}

	public function showCustomCSS(Request $request, Response $response) {
		$customCSS = Meta::where('key', 'CUSTOM_CSS')->first()->value;
		return $this->view->render($response, 'admin/custom-css.pug', array(
			'custom_css' => $customCSS
		));
	}

	public function updateCustomCSS(Request $request, Response $response) {
		$body = $request->getParsedBody();
		$css = $body['css'];
		$Meta = Meta::where('key', 'CUSTOM_CSS')->first();
		if($Meta) {
			$Meta->value = $css;
			$Meta->save();
		} else {
			$obj = new stdClass();
			$obj->key = 'CUSTOM_CSS';
			$obj->value = $css;
			Meta::store($obj);
		}
		return $response->withJson(array(
			'code' => 0,
			'message' => 'Success'
		));
	}
}

?>
