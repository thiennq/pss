<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once(ROOT . '/models/Collection.php');
require_once(ROOT . '/models/Product.php');
require_once(ROOT . '/models/Seo.php');
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminCollectionController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Collection::all();
        foreach ($data as $key => $value) {
            $value->image = convertImage($value->image, 240);
        }
        $tree = $this->buildTree($data);
    return $this->view->render($response, 'admin/collection', array(
      'collections' => $tree
    ));
  }

  public function create(Request $request, Response $response) {
    $collection = Collection::where('parent_id', -1)->orderBy('breadcrumb', 'asc')->get();
    return $this->view->render($response, 'admin/collection_create', array(
      'collection' => $collection
    ));
  }

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $data = Collection::find($id);
    if (!$data) return $response->withStatus(302)->withHeader('Location', '/404');
    $collection = Collection::where('id', '!=', $id)->get();
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
    if ($code) Seo::store('collection', $code, $body);
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
    Seo::update('collection', $id, $body);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Collection::remove($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function buildTree( $arr, $pid = -1 ) {
    $op = array();
    foreach( $arr as $item ) {
      if( $item->parent_id == $pid ) {
        $op[$item->id] = array(
          'id' => $item->id,
          'parent_id' => $item->parent_id,
          'title' => $item->title,
        );
        $children =  $this->buildTree( $arr, $item->id );
        if( $children ) {
          $op[$item->id]['children'] = $children;
        }
      }
    }
    return $op;
  }
}
