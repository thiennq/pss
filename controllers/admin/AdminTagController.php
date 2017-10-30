<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Tag.php");
require_once("../models/ProductTag.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminTagController extends AdminController {

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $product_id = $request->getAttribute('id');
    foreach ($data as $key=>$value){
        $code = Tag::store($value);
        if ($code) ProductTag::store($product_id, $code);
    }
  }

  public function update(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $product_id = $request->getAttribute('id');
    $tag_id = ProductTag::findViaProducId($product_id);
    if (!ProductTag::remove($product_id)){
        Tag::destroy($tag_id);
        foreach ($data as $key=>$value){
            $code = Tag::store($value);
            if ($code) ProductTag::store($product_id, $code);
        }
    }
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Tag::remove($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
