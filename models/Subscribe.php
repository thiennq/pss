<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Subscribe extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'subscribe';

    public function store($email) {
      $sub = Subscribe::where('email', $email)->first();
      if ($sub) return -1;
      $sub = new Subscribe;
      $sub->email = $email;
      $sub->created_at = date('Y-m-d H:i:s');
      $sub->updated_at = date('Y-m-d H:i:s');
      if ($sub->save()) return 0;
      return -3;
    }
}
