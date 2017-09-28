<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Article extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'article';

  function updateLinkArticle($id) {
    $article = Article::find($id);
    $article->link = $article->link . '-' .$id;
    $article->save();
  }

}
