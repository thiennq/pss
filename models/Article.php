<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Article extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'article';

  function create($data) {
    $article = Article::where('title', $data['title'])->first();
    if ($article) return -1;
    $article = new Article;
    $article->title = $data['title'];
    $article->handle = $data['handle'];
    $article->image = $data['image'] ? renameOneImage($data['image'], $data['handle']) : '';
    $article->description = $data['description'] ? $data['description'] : '';
    $article->description_seo = $data['description_seo'] ? $data['description_seo']: '';
    $article->content = $data['content'];
    $article->author = $_SESSION['fullname'];
    $article->display = $data['display'];
    $article->meta_robots = $data['meta_robots'];
    $article->view = 0;
    $article->created_at = date('Y-m-d H:i:s');
    $article->updated_at = date('Y-m-d H:i:s');
    if($article->save()) return $article->id;
    return -3;
  }

  function fetch($page_number, $perpage) {
    $skip = ($page_number - 1) * $perpage;
    $articles = Article::orderBy('updated_at', 'desc')->skip($skip)->take($perpage)->get();
    return $articles;
  }

  function get($id) {
    $data = Article::find($id);
    if ($data) return $data;
    return -2;
  }

  function update($id, $data) {
    $article = Article::find($id);
    if (!$article) return -2;
    $article->title = $data['title'];
    $article->handle = $data['handle'];
    if($data['image']) $article->image = renameOneImage($data['image'], $data['handle']);
    if($data['description']) $article->description = $data['description'];
    if($data['description_seo']) $article->description_seo = $data['description_seo'];
    $article->content = $data['content'];
    $article->author = $_SESSION['fullname'];
    $article->display = $data['display'];
    $article->meta_robots = $data['meta_robots'];
    $article->updated_at = $data['updated_at'] ? $data['updated_at'] : date('Y-m-d H:i:s');
    if($article->save()) return $article->id;
    return -3;
  }

  function remove($id) {
    $article = Article::find($id);
    if (!$article) return -2;
    if ($article->delete()) {
      return 0;
    }
    return -3;
  }

}
