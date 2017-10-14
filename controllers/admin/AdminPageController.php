<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Page.php");

class AdminPageController extends AdminController {

  public function create(Request $request, Response $response) {
    return $this->view->render($response, 'admin/page_new.pug');
  }

  public function showNews(Request $request, Response $response) {
    $Page = Page::all();
    return $this->view->render($response, 'admin/page_list.pug', array(
			'data' => $Page
    ));
  }

  public function searchPage(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $title = $params['q'];
    $id = $params['id'];
    $Page = Page::where('title', 'LIKE', '%'.$title.'%')->where('id', '!=', $id)->take(10)->get();
    if(count($Page)) {
      return $response->withJson(array(
        'code' => 0,
        'data' => $Page
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Empty'
    ));
  }

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $Page = Page::find($id);
    return $this->view->render($response, 'admin/page_edit.pug', array(
			'data' => $Page,
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $Page = new Page;
    $Page->title = $body['title'];
    $Page->handle = $body['handle'];
    $Page->link = '/' . $body['handle'];
    $Page->image = $body['image'] ? renameOneImage($body['image'], $body['handle']) : '';
    $Page->description = $body['description'] ? $body['description'] : '';
    $Page->description_seo = $body['description_seo'] ? $body['description_seo']: '';
    $Page->content = $body['content'];
    $Page->author = $_SESSION['fullname'];
    $Page->display = $body['display'];
    $Page->meta_robots = $body['meta_robots'];
    $Page->view = 0;
    $Page->created_at = date('Y-m-d H:i:s');
    $Page->updated_at = date('Y-m-d H:i:s');
    if($Page->save()) {
      $Page_id = $Page->id;
      Page::updateLinkPage($Page_id);
      // setMemcached("Page_index", '');
      return $response->withJson(array(
        'code' => 0,
        'id' => $Page_id
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Error'
    ));
  }

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $Page = Page::find($id);
    if($Page) {
      $Page->title = $body['title'];
      $Page->handle = $body['handle'];
      $link = '/' . $body['handle'] . '-' . $id;
      $Page->link = $link;
      if($body['image']) $Page->image = renameOneImage($body['image'], $body['handle']);
      if($body['description']) $Page->description = $body['description'];
      if($body['description_seo']) $Page->description_seo = $body['description_seo'];
      $Page->content = $body['content'];
      $Page->author = $_SESSION['fullname'];
      $Page->display = $body['display'];
      $Page->meta_robots = $body['meta_robots'];
      $Page->updated_at = $body['updated_at'] ? $body['updated_at'] : date('Y-m-d H:i:s');
      $Page->save();
      // setMemcached("Page_index", '');
      // setMemcached("Page_" . $link, '');
      return $response->withJson(array(
        'code' => 0,
        'message' => 'Updated'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Not found'
    ));
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $Page = Page::find($id);
    if($Page) {
      $Page->delete();
      return $response->withJson(array(
        'code' => 0,
        'message' => 'Deleted'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Not found'
    ));
  }
}

?>
