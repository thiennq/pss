<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Blog extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'blog';

    function fetch($page_number, $perpage) {
      $skip = ($page_number - 1) * $perpage;
      $blogs = Blog::orderBy('updated_at', 'desc')->skip($skip)->take($perpage)->get();
      return $blogs;
    }

    function create($data) {
      $blog = Blog::where('title', $data['title'])->first();
      if ($blog) return -1;
      $blog = new Blog;
      $blog->title = $data['title'];
      $blog->handle = $data['handle'];
      $blog->image = $data['image'] ? renameOneImage($data['image'], $data['handle']) : '';
      $blog->description = $data['description'] ? $data['description'] : '';
      $blog->meta_description = $data['meta_description'] ? $data['meta_description']: '';
      $blog->content = $data['content'];
      $blog->author = $_SESSION['fullname'];
      $blog->display = $data['display'];
      $blog->meta_robots = $data['meta_robots'];
      $blog->view = 0;
      $blog->created_at = date('Y-m-d H:i:s');
      $blog->updated_at = date('Y-m-d H:i:s');
      if($blog->save()) {
        return $blog->id;
      }
      return -3;
    }

    function get($id) {
      $data = Blog::find($id);
      if ($data) return $data;
      return -2;
    }

    function update($id, $data) {
      $blog = Blog::find($id);
      if (!$blog) {
        return -2;
      }
      $blog->title = $data['title'];
      $blog->handle = $data['handle'];
      if($data['image']) $blog->image = renameOneImage($data['image'], $data['handle']);
      if($data['description']) $blog->description = $data['description'];
      if($data['meta_description']) $blog->meta_description = $data['meta_description'];
      $blog->content = $data['content'];
      $blog->author = $_SESSION['fullname'];
      $blog->display = $data['display'];
      $blog->meta_robots = $data['meta_robots'];
      $blog->updated_at = $data['updated_at'] ? $data['updated_at'] : date('Y-m-d H:i:s');
      if ($blog->save()) {
        return 0;
      }
      return -3;
    }

    function remove($id) {
      $blog = Blog::find($id);
      if (!$blog) {
        return -2;
      }
      if ($blog->delete()) return 0;
      return -3;
    }

  }
?>
