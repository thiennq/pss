<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Brand.php");

class AdminBrandController extends AdminController {

  public function index(Request $request, Response $response) {
    $brand = Brand::orderBy('name', 'asc')->get();
    return $this->view->render($response, 'admin/brand.pug', array(
      'data' => $brand
    ));
  }

  public function create(Request $request, Response $response) {
    return $this->view->render($response, 'admin/brand_new.pug');
  }

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $brand = Brand::find($id);
    return $this->view->render($response, 'admin/brand_edit.pug', array(
      'data' => $brand
    ));
  }

  public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
    $obj = new stdClass();
    $obj->name = $body['name'];
    $obj->handle = convertHandle($body['name']);
    $obj->description = $body['description'];
    $obj->meta_title = $body['meta_title'];
    $obj->meta_description = $body['meta_description'];
    if($body['image']) $obj->image = renameOneImage($body['image'], $obj->handle);
    $obj->highlight = $body['highlight'];
    $obj->display = $body['display'];
    $result = Brand::store($obj);
    if($result) {
      setMemcached("brand_index", '');
      return $response->withJson(array(
				'code' => 0,
				'message' => 'Created',
				'id' => $result
			));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Exist'
    ));
	}

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $obj = new stdClass();
    $obj->id = $id;
    $obj->name = $body['name'];
    $obj->handle = convertHandle($body['name']);
    $obj->meta_title = $body['meta_title'];
    $obj->description = $body['description'];
    $obj->meta_description = $body['meta_description'];
    if($body['image']) $obj->image = renameOneImage($body['image'], $obj->handle);
    $obj->highlight = $body['highlight'];
    $obj->display = $body['display'];
    $result = Brand::store($obj);
    if($result) {
      setMemcached("brand_index", '');
      return $response->withJson(array(
				'code' => 0,
				'message' => 'Updated'
			));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Error'
    ));
	}

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $brand = Brand::find($id);
    if($brand) {
      $brand->delete();
      return $response->withJson(array(
				'code' => 0,
				'message' => 'Delete'
			));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Error'
    ));
	}
}

?>
