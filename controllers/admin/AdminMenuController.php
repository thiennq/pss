<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Menu.php");
require_once(ROOT . '/models/Collection.php');
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminMenuController extends AdminController {

	public function getListMenu(Request $request, Response $response) {
		$type = $request->getAttribute('type');
		if($type == 'collection') $data = Collection::listBreadCrumb();
		else if($type == 'tin-tuc') $data = Article::listAllArticle();
    else if($type == 'thong-tin') $data = Article::listAllPage();
		return $response->withJson([
			'code' => 0,
			'data' => $data
		]);
	}

	public function index(Request $request, Response $response) {
		$menu = Menu::getMenu();
		$article = Article::listAllArticle();
    $page = Article::listAllPage();
    $collection = Collection::listBreadCrumb();
		return $this->view->render($response, 'admin/menu.pug', [
			'data' => $menu,
      'collection' => $collection,
			'article' => $article,
      'page' => $page
		]);
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
	public function getMenu(Request $request, Response $response)	{
		$id = $request->getAttribute('id');
		$menu = Menu::find($id);
		if ($menu) {
			return $response->withJson([
				'code' => 0,
				'data' => $menu
			]);
		}
	}
}

?>
