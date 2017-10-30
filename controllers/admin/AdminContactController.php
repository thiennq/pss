<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");
require_once("../models/Blog.php");
require_once("../models/BlogArticle.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminContactController extends AdminController {

  public function fetch(Request $request, Response $response) {
    return $this->view->render($response, 'admin/contact');
  }

  public function create(Request $request, Response $response) {
    $blogs = Blog::all();
    return $this->view->render($response, 'admin/article_create', array(
      'blogs' => $blogs
    ));
  }

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $code = Article::create($data);
    if ($code) {
      $blog_id = $data['blog_id'];
      foreach ($blog_id as $key => $value) {
        BlogArticle::store($value, $code);;
      }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);

  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $article = Article::find($id);
    if (!$article) return $response->withStatus(302)->withHeader('Location', '/404');
    $blogs = Blog::all();
    $blog_article = BlogArticle::where('article_id', $id)->get();
    return $this->view->render($response, 'admin/article_edit', array(
			'data' => $article,
      'blogs' => $blogs,
      'blog_article' => $blog_article
    ));
  }

  public function update(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $code = Article::update($id, $data);
    if (!$code) {
      BlogArticle::removeAll($id);
      $blog_id = $data['blog_id'];
      foreach ($blog_id as $key => $blogId) {
        BlogArticle::store($blogId, $id);
      }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Article::remove($id);
    if (!$code) {
      BlogArticle::removeAll($id);
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
