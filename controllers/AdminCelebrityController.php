<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Celebrity.php");


class AdminCelebrityController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Celebrity::all();
    setMemcached("celebrity", '');
    return $this->view->render($response, 'admin/celebrity.pug', array(
      'celebrities' => $data
    ));
  }

  public function store (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $Celebrity = new Celebrity;
    $Celebrity->name = $body['name'];
    $Celebrity->image = $body['image'];
    $Celebrity->link = $body['link'];
    $Celebrity->display = $body['display'];
    $Celebrity->created_at = date('Y-m-d H:i:s');
    $Celebrity->updated_at = date('Y-m-d H:i:s');
    if($Celebrity->save()) {
      return $response->withJson(array(
        'code' => 0,
        'message' => 'Created'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Error'
    ));
  }

  public function getCelebrity(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $celebrity = Celebrity::find($id);
    if ($celebrity) {
      return $response->withJson(array(
        'code' => 0,
        'data' => $celebrity
      ));
    }
  }

  public function update (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $celebrity = Celebrity::find($id);
    if($celebrity) {
      $celebrity->name = $body['name'];
      $celebrity->image = $body['image'];
      $celebrity->link = $body['link'];
      $celebrity->display = $body['display'];
      $celebrity->updated_at = date('Y-m-d H:i:s');
      if($celebrity->save()) {
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
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Unknown celebrity'
    ));
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $Celebrity = Celebrity::find($id);
    if($Celebrity) {
      if($Celebrity->delete()) {
        return $response->withJson(array(
          'code' => 0,
          'message' => 'Deleted'
        ));
      }
      return $response->withJson(array(
        'code' => -1,
        'message' => 'Error'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Unknown celebrity'
    ));
  }
}

?>
