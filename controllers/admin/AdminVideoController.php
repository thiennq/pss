<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Video.php");

class AdminVideoController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Video::all();
    return $this->view->render($response, 'admin/video', array(
      'videos' => $data
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $Video = new Video;
    $Video->title = $body['title'];
    $Video->embed_link = $body['embed_link'];
    $Video->created_at = date('Y-m-d H:i:s');
    $Video->updated_at = date('Y-m-d H:i:s');
    if($Video->save()) {
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

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $video = Video::find($id);
    if($video) {
      $video->title = $body['title'];
      $video->embed_link = $body['embed_link'];
      $video->updated_at = date('Y-m-d H:i:s');
      if($video->save()) {
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
      'message' => 'Video Not Found'
    ));
  }


  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $video = Video::find($id);
    if($video) {
      $video->delete();
      return $response->withJson(array(
        'code' => 0,
        'message' => 'Deleted'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Video Not Found'
    ));
  }

}

?>
