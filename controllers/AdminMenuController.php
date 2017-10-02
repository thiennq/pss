<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Menu.php");
require_once("../models/Collection.php");
require_once("helper.php");
use ControllerHelper as Helper;

class AdminMenuController extends AdminController {

	public function getListMenu(Request $request, Response $response) {
		$type = $request->getAttribute('type');
		if($type == 'collection') {
			$data = Collection::orderBy('breadcrumb', 'asc')->get();
			foreach ($data as $key => $collection) {
				$collection['breadcrumb'] = str_replace(',', '/', $collection['breadcrumb']);
				$collection['breadcrumb'] = strtoupper($collection['breadcrumb']);
			}
		}
		else if($type == 'article') $data = Article::orderBy('title', 'asc')->get();
		return $response->withJson(array(
			'code' => 0,
			'data' => $data
		));
	}

	public function index(Request $request, Response $response) {
		$menu = Menu::all();
    $menu_mobile = Meta::where('key', 'menu_mobile')->first();
		return $this->view->render($response, 'admin/menu.pug', array(
			'data' => $menu,
      'menu_mobile' => $menu_mobile->value
		));
	}

	public function create(Request $request, Response $response) {
		$list_collection = Collection::orderBy('breadcrumb', 'asc')->get();
		foreach ($list_collection as $key => $collection) {
			$collection['breadcrumb'] = str_replace(',', '/', $collection['breadcrumb']);
			$collection['breadcrumb'] = strtoupper($collection['breadcrumb']);
		}
		return $this->view->render($response, 'admin/menu_new.pug', array(
			'list_collection' => $list_collection
		));
	}

	public function show(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$list_collection = Collection::where('show_landing_page', 0)->orderBy('breadcrumb', 'asc')->get();
		foreach ($list_collection as $key => $collection) {
			$collection['breadcrumb'] = str_replace(',', '/', $collection['breadcrumb']);
			$collection['breadcrumb'] = strtoupper($collection['breadcrumb']);
		}
		$list_article = Article::orderBy('title', 'asc')->get();
		$menu = Menu::find($id);
		return $this->view->render($response, 'admin/menu_edit.pug', array(
			'data' => $menu,
			'list_collection' => $list_collection,
			'list_article' => $list_article
		));
	}

	public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
		$code = Menu::store($body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function update(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$code = Menu::update($id, $body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function delete (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$code = Menu::remove($id);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}
}

?>
