<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Blog extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'blog';

    public function store($data) {
      $check = Blog::where('title', $data['title'])->first();
      if($check) return -1;
  		$blog = new Blog;
  		$blog->title = $data['title'];
      $blog->handle = $data['handle'];
      $blog->description = $data['description'];
  		$blog->created_at = date('Y-m-d H:i:s');
  		$blog->updated_at = date('Y-m-d H:i:s');
      if($blog->save()) return 0;
      return -2;
    }

    public function update($id, $data) {
      $check = Blog::where('title', $data['title'])->where('id', '!=', $id)->first();
      if($check) return -1;
      $blog = Blog::find($id);
      if(!$blog) return -2;
      $blog->title = $data['title'];
      $blog->handle = $data['handle'];
      $blog->description = $data['description'];
  		$blog->updated_at = date('Y-m-d H:i:s');
      if($blog->save()) return 0;
      return -3;
    }
  }
?>
