<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");

class ArticleController extends Controller {

  public function getNews(Request $request, Response $response) {
    $link = $request->getAttribute('link');
    $responseData = array();
    if(getMemcached('article_' . $link)) $responseData =  json_decode(getMemcached('article_' . $link), true);
    else {
      if(strpos($link, '.html') !== false) {
        $this->view->render($response, '404.pug');
        return $response->withStatus(404);
      }
      if(substr($link, -1) == '#') $link = substr($link, 0, -1);
      $id = end(explode('-', $link));
      $handle = str_replace('-'.$id, '', $link);
  		$article = Article::where('id', $id)->where('handle', $handle)->first();
      if(!$article) {
        $this->view->render($response, '404.pug');
        return $response->withStatus(404);
      }
      $breadcrumb_collection = array();
      $obj = new stdClass();
      $obj->handle = '/tin-tuc';
      $obj->title = 'Tin tức';
      array_push($breadcrumb_collection, $obj);
      $article->view = $article->view + 1;
      $article->save();
      $other = Article::where('id', '!=', $article->id)->where('type', 'tin-tuc')->where('display', 1)->take(5)->get();
      $hot_article = Article::where('id', '!=', $article->id)->where('type', 'tin-tuc')->where('display', 1)->orderBy('view', 'desc')->orderBy('updated_at', 'desc')->take(5)->get();

      $related = array();
      $arr_related = ArticleRelated::where('article_id', $id)->get();
      foreach ($arr_related as $key => $value) {
        $item = Article::find($value->article_related);
        array_push($related, $item);
      }
      $fb_image = $article->image ? HOST . '/uploads/' . $article->image : '';
      $responseData = array(
        'fb_image' => $fb_image,
        'data' => $article,
        'breadcrumb_collection' => $breadcrumb_collection,
        'breadcrumb_title' => $article->title,
        'other' => $other,
        'hot_article' => $hot_article,
        'related' => $related
      );
      // setMemcached("article_" . $link, json_encode($responseData));
    }
    return $this->view->render($response, 'article.pug', $responseData);
  }

  public function getInfo(Request $request, Response $response) {
    $link = $request->getAttribute('link');
    if(strpos($link, '.html') !== false) {
      $this->view->render($response, '404.pug');
      return $response->withStatus(404);
    }
    if(substr($link, -1) == '#') $link = substr($link, 0, -1);
    $id = end(explode('-', $link));
    $handle = str_replace('-'.$id, '', $link);
		$article = Article::where('id', $id)->where('handle', $handle)->first();
    if(!$article) return $this->view->render($response, '404.pug');
    $breadcrumb_collection = array();
    $obj = new stdClass();
    $obj->handle = '/tin-tuc';
    $obj->title = 'Tin tức';
    array_push($breadcrumb_collection, $obj);
    $article->view = $article->view + 1;
    $article->save();
    $other = Article::where('id', '!=', $article->id)->where('type', 'thong-tin')->where('display', 1)->take(5)->get();
    $hot_article = Article::where('id', '!=', $article->id)->orderBy('view', 'desc')->where('type', 'tin-tuc')->where('display', 1)->take(5)->get();

    $related = array();
    $arr_related = ArticleRelated::where('article_id', $id)->get();
    foreach ($arr_related as $key => $value) {
      $item = Article::find($value->article_related);
      array_push($related, $item);
    }

    return $this->view->render($response, 'article.pug', array(
      'data' => $article,
      'breadcrumb_collection' => $breadcrumb_collection,
      'breadcrumb_title' => $article->title,
      'other' => $other,
      'hot_article' => $hot_article,
      'related' => $related
    ));
  }

  public function getPromotion(Request $request, Response $response) {
    $link = $request->getAttribute('link');
    if(strpos($link, '.html') !== false) {
      $this->view->render($response, '404.pug');
      return $response->withStatus(404);
    }
    if(substr($link, -1) == '#') $link = substr($link, 0, -1);
    $id = end(explode('-', $link));
		$article = Article::find($id);
    if($article->collection_id) {
      $collection_id = $article->collection_id;
  		$obj = new stdClass();
  		$obj->page_type = 'collection';
  		$obj->collection_id = $collection_id;
  		$query = Product::checkFilter($obj);
      $products = $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc')->get();
  		$products = Product::getInfoProduct($products);
    }
    return $this->view->render($response, 'promotion.pug', array(
      'data' => $article,
      'products' => $products
    ));
  }

  public function news(Request $request, Response $response) {
    $page_number = 1;
		$params = $request->getQueryParams();
		if($params['page']) $page_number = $params['page'];
		$perpage = 12;
		$skip = ($page_number - 1) * $perpage;
    $article = Article::where('type', 'tin-tuc')->where('display', 1)->orderBy('updated_at', 'desc')->skip($skip)->take($perpage)->get();
    $count_article = Article::where('type', 'tin-tuc')->where('display', 1)->count();
    $total_pages = ceil($count_article / 12);
    $hot_news = Article::where('type', 'tin-tuc')->where('display', 1)->orderBy('view', 'desc')->take(5)->get();
    $first = $article[0];
    $fb_image = $first->image ? HOST . '/uploads/' . $first->image : '';
    return $this->view->render($response, 'news.pug', array(
      'data' => $article,
      'fb_image' => $fb_image,
      'breadcrumb_title' => 'Tin tức',
      'hot_news' => $hot_news,
      'total_pages' => $total_pages,
			'page_number' => $page_number
    ));
  }

  public function PageNotFound(Request $request, Response $response) {
    $this->view->render($response, '404.pug');
    return $response->withStatus(404);
  }
}

?>
