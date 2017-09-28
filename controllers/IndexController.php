<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class IndexController extends Controller {

  public function index(Request $request, Response $response) {
    /*

    $meta_title_default = Meta::where('key', 'meta_title_default')->first();
    $meta_title_default = $meta_title_default->value;
    $meta_description_default = Meta::where('key', 'meta_description_default')->first();
    $meta_description_default = $meta_description_default->value;

    if(getMemcached('article_index')) $articles = getMemcached('article_index');
    else {
      $articles = Article::where('display',1)->where('type', 'tin-tuc')->orderby('updated_at', 'desc')->skip(0)->take(4)->get();
      setMemcached("article_index", $articles);
    }

    // $articles = Article::where('display',1)->where('type', 'tin-tuc')->orderby('updated_at', 'desc')->skip(0)->take(4)->get();

    */

    // $brand = Brand::where('display', 1)->where('highlight', 1)->get();

		return $this->view->render($response, 'index.pug', array(
      "title" => "Home"
      /*
      'title' => $meta_title_default,
      'meta_description_default' => $meta_description_default,
      'products' => $products,
      'articles' => $articles,
      'brand' => $brand,
      'template' => 'no-banner-footer'*/
		));
  }

  public function Newsdetail(Request $request, Response $response) {
    return $this->view->render($response, 'news-detail.pug', array(
      'breadcrumb_title' => 'Tin tức chi tiết'
    ));
  }
}

?>
