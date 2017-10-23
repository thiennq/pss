<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Page extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'page';

    function fetch($page_number, $perpage) {
      $skip = ($page_number - 1) * $perpage;
      $pages = Page::orderBy('updated_at', 'desc')->skip($skip)->take($perpage)->get();
      return $pages;
    }

    function create($data) {
      $page = Page::where('title', $data['title'])->first();
      if ($page) return -1;
      $page = new Page;
      $page->title = $data['title'];
      $page->handle = $data['handle'];
      $page->image = $data['image'] ? renameOneImage($data['image'], $data['handle']) : '';
      $page->description = $data['description'] ? $data['description'] : '';
      $page->meta_description = $data['meta_description'] ? $data['meta_description']: '';
      $page->content = $data['content'];
      $page->author = $_SESSION['fullname'];
      $page->display = $data['display'];
      $page->meta_robots = $data['meta_robots'];
      $page->view = 0;
      $page->created_at = date('Y-m-d H:i:s');
      $page->updated_at = date('Y-m-d H:i:s');
      if($page->save()) {
        return $page->id;
      }
      return -3;
    }

    function get($id) {
      $data = Page::find($id);
      if ($data) return $data;
      return -2;
    }

    function update($id, $data) {
      $page = Page::find($id);
      if (!$page) {
        return -2;
      }
      $page->title = $data['title'];
      $page->handle = $data['handle'];
      if($data['image']) $page->image = renameOneImage($data['image'], $data['handle']);
      if($data['description']) $page->description = $data['description'];
      if($data['meta_description']) $page->meta_description = $data['meta_description'];
      $page->content = $data['content'];
      $page->author = $_SESSION['fullname'];
      $page->display = $data['display'];
      $page->meta_robots = $data['meta_robots'];
      $page->updated_at = $data['updated_at'] ? $data['updated_at'] : date('Y-m-d H:i:s');
      if ($page->save()) {
        return 0;
      }
      return -3;
    }

    function remove($id) {
      $page = Page::find($id);
      if (!$page) {
        return -2;
      }
      if ($page->delete()) return 0;
      return -3;
    }

  }
?>
