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
      $blog = new Blog;
      $blog->title = $data['title'];
      $blog->handle = $data['handle'];
      $blog->image = $data['image'] ? renameOneImage($data['image'], $data['handle']) : '';
      $blog->description = $data['description'] ? $data['description'] : '';
      $blog->description_seo = $data['description_seo'] ? $data['description_seo']: '';
      $blog->content = $data['content'];
      $blog->author = $_SESSION['fullname'];
      $blog->display = $data['display'];
      $blog->meta_robots = $data['meta_robots'];
      $blog->view = 0;
      $blog->created_at = date('Y-m-d H:i:s');
      $blog->updated_at = date('Y-m-d H:i:s');
      if($blog->save()) {
        $blog_id = $blog->id;
        return $blog_id;
      }
      else return -1;
    }

    function get($id) {
      $data = Blog::find($id);
      return $data;
    }

    function update($id, $data) {
      $blog = Blog::find($id);
      if ($blog) {
        $blog->title = $data['title'];
        $blog->handle = $data['handle'];
        if($data['image']) $blog->image = renameOneImage($data['image'], $data['handle']);
        if($data['description']) $blog->description = $data['description'];
        if($data['description_seo']) $blog->description_seo = $data['description_seo'];
        $blog->content = $data['content'];
        $blog->author = $_SESSION['fullname'];
        $blog->display = $data['display'];
        $blog->meta_robots = $data['meta_robots'];
        $blog->updated_at = $data['updated_at'] ? $data['updated_at'] : date('Y-m-d H:i:s');
        $blog->save();
        return 0;
      }
      return -1;
    }

    function remove($id) {
      $blog = Blog::find($id);
      if($blog) {
        $blog->delete();
        return 0;
      }
      return -1;
    }

  }
?>
