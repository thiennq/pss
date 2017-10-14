<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");
require_once("../models/Blog.php");
require_once("../models/BlogArticle.php");

class AdminArticleController extends AdminController {

  public function new(Request $request, Response $response) {
    $blogs = Blog::all();
    return $this->view->render($response, 'admin/article_new.pug', array(
      'blogs' => $blogs
    ));
  }

  public function create(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $article_id = Article::create($data);
    if ($article_id != -1) {
      return $response->withJson(array(
        'code' => 0,
        'id' => $article_id
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Error'
    ));
  }

  public function fetch(Request $request, Response $response) {
    $page_number = 1;
    $params = $request->getQueryParams();
    if($params['page']) $page_number = $params['page'];
    $perpage = 12;
    $data = Article::fetch($page_number,$perpage);
    return $this->view->render($response, 'admin/article_list.pug', array(
      'data' => $data
    ));
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $article = Article::get($id);
    $blogs = Blog::all();
    $blog_article = BlogArticle::where('article_id', $id)->get();
    return $this->view->render($response, 'admin/article_edit.pug', array(
			'data' => $article,
      'blogs' => $blogs,
      'blog_article' => $blog_article
    ));
  }

  public function update(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $article = Article::update($id, $data);
    if ($article != -1) {
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
    $article = Article::remove($id);
    if ($article != -1) {
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

  public function searchArticle(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $title = $params['q'];
    $id = $params['id'];
    $article = Article::where('title', 'LIKE', '%'.$title.'%')->where('id', '!=', $id)->take(10)->get();
    if(count($article)) {
      return $response->withJson(array(
        'code' => 0,
        'data' => $article
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Empty'
    ));
  }
}

?>
