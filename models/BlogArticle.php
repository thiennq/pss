<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class BlogArticle extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'blog_article';

  public function store($blog_id, $article_id) {
    $check = BlogArticle::where('article_id', $article_id)->where('blog_id', $blog_id)->first();
    if ($check) return -1;
		$blog_article = new BlogArticle;
		$blog_article->article_id = $article_id;
    $blog_article->blog_id = $blog_id;
    $blog_article->save();
  }

  public function removeAll($article_id) {
    $result = BlogArticle::where('article_id', $article_id)->delete();
    if ($result) return 0;
    return -3;
  }
}
