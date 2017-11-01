<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once(ROOT . '/models/Collection.php');
require_once(ROOT . '/models/Product.php');
require_once(ROOT . '/models/Seo.php');
require_once(ROOT . '/models/History.php');
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminCollectionController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Collection::all();
    $tree = $this->buildTree($data);
    error_log(json_encode($tree));
    foreach ($tree as $key => $value) {
      if (count($value->children)) {
        foreach ($value->children as $key => $a) {
          error_log($a->title);
        }
      }
    }
    return $this->view->render($response, 'admin/collection', array(
      'collections' => $tree
    ));
  }

  public function buildTree($arr, $pid = -1) {
    $tree = array();
    foreach($arr as $item) {
      $obj = new stdClass();
      if($item->parent_id == $pid ) {
        $obj->id = $item->id;
        $obj->parent_id = $item->parent_id;
        $obj->title = $item->title;
        $children =  $this->buildTree($arr, $item->id);
        if(count($children)) {
          $obj->children = $children;
        }
        array_push($tree, $obj);
      }
    }
    return $tree;
  }

  public function create(Request $request, Response $response) {
    $collection = Collection::orderBy('breadcrumb', 'asc')->get();
    return $this->view->render($response, 'admin/collection_create', array(
      'collection' => $collection
    ));
  }

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $data = Collection::find($id);
    if (!$data) return $response->withStatus(302)->withHeader('Location', '/404');
    $collection = Collection::where('id', '!=', $id)->orderBy('breadcrumb', 'asc')->get();
    $seo = Seo::get('collection', $id);
    return $this->view->render($response, 'admin/collection_edit', array(
      'data' => $data,
      'collection' => $collection,
      'seo' => $seo
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
    if ($code) {
      Seo::store('collection', $code, $body);
      History::store('Tạo mới nhóm bài viết', 'collection', $code);
    }
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
    if (!$code) {
      Seo::update('collection', $id, $body);
      History::store('Chỉnh sửa nhóm bài viết', 'collection', $id);
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $title = Collection::find($id)->title;
    $code = Collection::remove($id);
    if (!$code) History::store('Xóa nhóm bài viết ' . $title, 'collection', $id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}
