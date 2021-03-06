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
    $article->handle = createHandle($data['title']);
    $article->image = $data['image'] ? renameOneImage($data['image'], $article->handle) : '';
    $article->description = $data['description'] ? $data['description'] : '';
    $article->content = $data['content'];
    $article->author = $_SESSION['name'];
    $article->display = $data['display'];
    $article->view = 0;
    $article->meta_title = $data['meta_title'] ? $data['meta_title']: '';
    $article->meta_description = $data['meta_description'] ? $data['meta_description']: '';
    $article->meta_robots = $data['meta_robots'];
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
    $article->handle = createHandle($data['handle']);
    $article->image = $data['image'] ? renameOneImage($data['image'], $article->handle) : '';
    $article->description = $data['description'] ? $data['description'] : '';
    $article->content = $data['content'];
    $article->author = $_SESSION['name'];
    $article->display = $data['display'];
    $article->meta_title = $data['meta_title'] ? $data['meta_title']: '';
    $article->meta_description = $data['meta_description'] ? $data['meta_description']: '';
    $article->meta_robots = $data['meta_robots'];
    $article->updated_at = date('Y-m-d H:i:s');
    if($article->save()) return 0;
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
