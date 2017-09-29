<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Blog.php");

class AdminBlogController extends AdminController {

  public function create(Request $request, Response $response) {
    return $this->view->render($response, 'admin/blog_new.pug');
  }

  public function showNews(Request $request, Response $response) {
    $Blog = Blog::all();
    return $this->view->render($response, 'admin/blog_list.pug', array(
			'data' => $Blog
    ));
  }

  public function searchBlog(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $title = $params['q'];
    $id = $params['id'];
    $Blog = Blog::where('title', 'LIKE', '%'.$title.'%')->where('id', '!=', $id)->take(10)->get();
    if(count($Blog)) {
      return $response->withJson(array(
        'code' => 0,
        'data' => $Blog
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Empty'
    ));
  }

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $Blog = Blog::find($id);
    return $this->view->render($response, 'admin/blog_edit.pug', array(
			'data' => $Blog,
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $Blog = new Blog;
    $Blog->title = $body['title'];
    $Blog->type = $body['type'];
    $Blog->handle = $body['handle'];
    $Blog->link = $body['type'] . '/' . $body['handle'];
    $Blog->image = $body['image'] ? renameOneImage($body['image'], $body['handle']) : '';
    $Blog->description = $body['description'] ? $body['description'] : '';
    $Blog->description_seo = $body['description_seo'] ? $body['description_seo']: '';
    $Blog->content = $body['content'];
    $Blog->content_promotion = $body['content_promotion'];
    $Blog->author = $_SESSION['fullname'];
    $Blog->display = $body['display'];
    $Blog->collection_id = $body['collection_id'];
    $Blog->meta_robots = $body['meta_robots'];
    $Blog->view = 0;
    $Blog->created_at = date('Y-m-d H:i:s');
    $Blog->updated_at = date('Y-m-d H:i:s');
    if($Blog->save()) {
      $Blog_id = $Blog->id;
      Blog::updateLinkBlog($Blog_id);
      setMemcached("Blog_index", '');
      return $response->withJson(array(
        'code' => 0,
        'id' => $Blog_id
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
    $Blog = Blog::find($id);
    if($Blog) {
      $Blog->title = $body['title'];
      $Blog->type = $body['type'];
      $Blog->handle = $body['handle'];
      $link = $body['type'] . '/' . $body['handle'] . '-' . $id;
      $Blog->link = $link;
      if($body['image']) $Blog->image = renameOneImage($body['image'], $body['handle']);
      if($body['description']) $Blog->description = $body['description'];
      if($body['description_seo']) $Blog->description_seo = $body['description_seo'];
      $Blog->content = $body['content'];
      $Blog->content_promotion = $body['content_promotion'];
      $Blog->author = $_SESSION['fullname'];
      $Blog->display = $body['display'];
      $Blog->collection_id = $body['collection_id'];
      $Blog->meta_robots = $body['meta_robots'];
      $Blog->updated_at = $body['updated_at'] ? $body['updated_at'] : date('Y-m-d H:i:s');
      $Blog->save();
      setMemcached("Blog_index", '');
      setMemcached("Blog_" . $link, '');
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
    $Blog = Blog::find($id);
    if($Blog) {
      $Blog->delete();
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
