<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");
require_once("../models/ArticleRelated.php");

class AdminArticleController extends AdminController {

  public function create(Request $request, Response $response) {
    $list_collection = Collection::where('show_landing_page', 1)->orderBy('breadcrumb', 'asc')->get();
		foreach ($list_collection as $key => $collection) {
			$collection['breadcrumb'] = str_replace(',', '/', $collection['breadcrumb']);
		}
    return $this->view->render($response, 'admin/article_new.pug', [
      'list_collection' => $list_collection
    ]);
  }

  public function showLandingPage(Request $request, Response $response) {
    $article = Article::where('type', 'khuyen-mai')->get();
    return $this->view->render($response, 'admin/article_landing_page.pug', array(
			'data' => $article
    ));
  }

  public function showNews(Request $request, Response $response) {
    $article = Article::where('type', 'tin-tuc')->get();
    return $this->view->render($response, 'admin/article_news.pug', array(
			'data' => $article
    ));
  }

  public function showInfo(Request $request, Response $response) {
    $article = Article::where('type', 'thong-tin')->get();
    return $this->view->render($response, 'admin/article_info.pug', array(
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
    $related = array();
    $arr_related = ArticleRelated::where('article_id', $id)->get();
    foreach ($arr_related as $key => $value) {
      $item = Article::find($value->article_related);
      array_push($related, $item);
    }
    $list_collection = Collection::where('show_landing_page', 1)->orderBy('breadcrumb', 'asc')->get();
		foreach ($list_collection as $key => $collection) {
			$collection['breadcrumb'] = str_replace(',', '/', $collection['breadcrumb']);
		}
    return $this->view->render($response, 'admin/article_edit.pug', array(
			'data' => $article,
      'related' => $related,
      'list_collection' => $list_collection
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $article = new Article;
    $article->title = $body['title'];
    $article->type = $body['type'];
    $article->handle = $body['handle'];
    $article->link = $body['type'] . '/' . $body['handle'];
    $article->image = $body['image'] ? renameOneImage($body['image'], $body['handle']) : '';
    $article->description = $body['description'] ? $body['description'] : '';
    $article->description_seo = $body['description_seo'] ? $body['description_seo']: '';
    $article->content = $body['content'];
    $article->content_promotion = $body['content_promotion'];
    $article->author = $_SESSION['fullname'];
    $article->display = $body['display'];
    $article->collection_id = $body['collection_id'];
    $article->meta_robots = $body['meta_robots'];
    $article->view = 0;
    $article->created_at = date('Y-m-d H:i:s');
    $article->updated_at = date('Y-m-d H:i:s');
    if($article->save()) {
      $article_id = $article->id;
      Article::updateLinkArticle($article_id);
      $arr_related = $body['arr_related'];
      foreach ($arr_related as $key => $item) {
        ArticleRelated::store($article_id, $item);
      }
      setMemcached("article_index", '');
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
      $article->type = $body['type'];
      $article->handle = $body['handle'];
      $link = $body['type'] . '/' . $body['handle'] . '-' . $id;
      $article->link = $link;
      if($body['image']) $article->image = renameOneImage($body['image'], $body['handle']);
      if($body['description']) $article->description = $body['description'];
      if($body['description_seo']) $article->description_seo = $body['description_seo'];
      $article->content = $body['content'];
      $article->content_promotion = $body['content_promotion'];
      $article->author = $_SESSION['fullname'];
      $article->display = $body['display'];
      $article->collection_id = $body['collection_id'];
      $article->meta_robots = $body['meta_robots'];
      $article->updated_at = $body['updated_at'] ? $body['updated_at'] : date('Y-m-d H:i:s');
      $article->save();
      setMemcached("article_index", '');
      setMemcached("article_" . $link, '');
      $arr_related = $body['arr_related'];
      ArticleRelated::where('article_id', $id)->delete();
      foreach ($arr_related as $key => $item) {
        ArticleRelated::store($id, $item);
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
      ArticleRelated::where('article_id', $id)->orWhere('article_related', $id)->delete();
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

  public function removeArticleRelated(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $article_id = $params['article_id'];
    $article_related = $params['article_related'];
    $article = ArticleRelated::where('article_id', $article_id)->where('article_related', $article_related)->delete();
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Deleted'
    ));
  }
}

?>
