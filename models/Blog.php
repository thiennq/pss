<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Blog extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'blog';

    function updateLinkBlog($id) {
      $article = Blog::find($id);
      $article->link = $article->link . '-' .$id;
      $article->save();
    }
  }
?>
