<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");

class ArticleController extends Controller {
  public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $article = Article::where('handle', $handle)->first();
    if(!$article) {
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }
    $article->view = $article->view + 1;
    $article->save();
    return $this->view->render($response, 'article', array(
      'article' => $article
    ));
  }
}

?>
