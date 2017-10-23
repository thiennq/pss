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
      $page->handle = createHandle($data['title']);
      $page->image = $data['image'] ? renameOneImage($data['image'], $page->handle) : '';
      $page->description = $data['description'] ? $data['description'] : '';
      $page->content = $data['content'];
      $page->author = $_SESSION['name'];
      $page->display = $data['display'];
      $page->view = 0;
      $page->meta_title = $data['meta_title'];
      $page->meta_description = $data['meta_description'] ? $data['meta_description']: '';
      $page->meta_robots = $data['meta_robots'];
      $page->created_at = date('Y-m-d H:i:s');
      $page->updated_at = date('Y-m-d H:i:s');
      if($page->save()) return $page->id;
      return -3;
    }

    function get($id) {
      $data = Page::find($id);
      if ($data) return $data;
      return -2;
    }

    function update($id, $data) {
      $page = Page::find($id);
      if (!$page) return -2;
      $check = Page::where('id', '!=', $id)->where('title', $data['title'])->first();
      if ($check) return -1;
      $page->title = $data['title'];
      $page->handle = createHandle($data['title']);
      $page->image = $data['image'] ? renameOneImage($data['image'], $page->handle) : '';
      $page->description = $data['description'] ? $data['description'] : '';
      $page->content = $data['content'];
      $page->author = $_SESSION['name'];
      $page->display = $data['display'];
      $page->meta_title = $data['meta_title'];
      $page->meta_description = $data['meta_description'] ? $data['meta_description']: '';
      $page->meta_robots = $data['meta_robots'];
      $page->updated_at = date('Y-m-d H:i:s');
      if ($page->save()) return 0;
      return -3;
    }

    function remove($id) {
      $page = Page::find($id);
      if (!$page) return -2;
      if ($page->delete()) return 0;
      return -3;
    }

  }
?>
