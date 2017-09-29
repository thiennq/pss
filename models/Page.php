<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Page extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'page';

    function updateLinkPage($id) {
      $article = Page::find($id);
      $article->link = $article->link . '-' .$id;
      $article->save();
    }
  }
?>
