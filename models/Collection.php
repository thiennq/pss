<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Collection extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'collection';

  public function listAll() {
    $data = Collection::orderBy('title', 'asc')->get();
    return $data;
  }

  public function listBreadCrumb() {
    $data = Collection::orderBy('breadcrumb', 'asc')->get();
    return $data;
  }
}
