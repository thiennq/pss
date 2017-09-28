<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ArticleRelated extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'article_related';

  public function store ($article_id, $article_related) {
    $check = ArticleRelated::where('article_id', $article_id)->where('article_related', $article_related)->first();
    if(!$check) {
      $article = new ArticleRelated;
      $article->article_id = $article_id;
      $article->article_related = $article_related;
      $article->created_at = date('Y-m-d H:i:s');
      $article->updated_at = date('Y-m-d H:i:s');
      $article->save();
    }
  }
}
