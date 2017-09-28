<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Page extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'page';

    public function store($data) {
      $check = Page::where('title', $data['title'])->first();
      if($check) return -1;
      $Page = new Page;
  		$Page->title = $data['title'];
      $Page->handle = $data['handle'];
      $Page->content = $data['content'];
      $Page->display = $data['display'];
      if($Page->save()) return $Page->id;
      return -2;
    }

    public function update($id, $data) {
      $check = Page::where('title', $data['title'])->where('id', '!=', $id)->first();
      if($check) return -1;
      $Page = Page::find($id);
      if(!$Page) return -2;
      $Page->title = $data['title'];
      $Page->handle = $data['handle'];
      $Page->content = $data['content'];
      $Page->display = $data['display'];
      if($Page->save()) return 0;
      return -3;
    }
  }
?>
