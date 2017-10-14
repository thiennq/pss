<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");
require_once("../models/Blog.php");
require_once("../models/BlogArticle.php");

class AdminArticleController extends AdminController {

  public function create(Request $request, Response $response) {
    $blogs = Blog::all();
    return $this->view->render($response, 'admin/article_new.pug', array(
      'blogs' => $blogs
    ));
  }

  public function showNews(Request $request, Response $response) {
    $article = Article::all();
    return $this->view->render($response, 'admin/article_list.pug', array(
			'data' => $article
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

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $article = Article::find($id);
    $blogs = Blog::all();
    $blog_article = BlogArticle::where('article_id', $id)->get();
    return $this->view->render($response, 'admin/article_edit.pug', array(
			'data' => $article,
      'blogs' => $blogs,
      'blog_article' => $blog_article
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $article = new Article;
    $article->title = $body['title'];
    $article->handle = $body['handle'];
    $article->link = '/' . $body['handle'];
    $article->image = $body['image'] ? renameOneImage($body['image'], $body['handle']) : '';
    $article->description = $body['description'] ? $body['description'] : '';
    $article->description_seo = $body['description_seo'] ? $body['description_seo']: '';
    $article->content = $body['content'];
    $article->author = $_SESSION['fullname'];
    $article->display = $body['display'];

    $article->blog_id = $body['blog_id'];

    $article->meta_robots = $body['meta_robots'];
    $article->view = 0;
    $article->created_at = date('Y-m-d H:i:s');
    $article->updated_at = date('Y-m-d H:i:s');
    if($article->save()) {
      $article_id = $article->id;
      $blog_id = $article->blog_id;
      foreach ($blog_id as $key => $blog) {
        BlogArticle::store($blog, $article_id);
      }
      Article::updateLinkArticle($article_id);
      // setMemcached("article_index", '');
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

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $article = Article::find($id);
    if($article) {
      $article->title = $body['title'];
      $article->handle = $body['handle'];
      $link = '/' . $body['handle'] . '-' . $id;
      $article->link = $link;
      if($body['image']) $article->image = renameOneImage($body['image'], $body['handle']);
      if($body['description']) $article->description = $body['description'];
      if($body['description_seo']) $article->description_seo = $body['description_seo'];
      $article->content = $body['content'];
      $article->author = $_SESSION['fullname'];
      $article->display = $body['display'];
      $article->blog_id = $body['blog_id'];
      $article->meta_robots = $body['meta_robots'];
      $article->updated_at = $body['updated_at'] ? $body['updated_at'] : date('Y-m-d H:i:s');

      // error_log("blog id : " . json_encode($body['blog_id']));

      $article->save();
      // setMemcached("article_index", '');
      // setMemcached("article_" . $link, '');

      BlogArticle::removeAll($id);
      $blog_id = $body['blog_id'];
      foreach ($blog_id as $key => $blog) {
        BlogArticle::store($blog, $id);
      }

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
    $article = Article::find($id);
    if($article) {
      $article->delete();
      BlogArticle::removeAll($id);
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
