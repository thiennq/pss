<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");
require_once("../models/Blog.php");
require_once("../models/BlogArticle.php");
require_once("../models/Contact.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminContactController extends AdminController {

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $code = Contact::store($data);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Contact::update($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Contact::delete($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
