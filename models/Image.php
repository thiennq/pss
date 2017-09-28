<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Image extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'image';

  public function getImage($type, $typeId) {
    $image = Image::where('type', $type)->where('typeId', $typeId)->get();
    return $image;
  }

  public function store($name, $type, $typeId) {
    $image = new Image;
    $image->name = $name;
    $image->type = $type;
    $image->typeId = $typeId;
    $image->created_at = date('Y-m-d H:i:s');
    $image->updated_at = date('Y-m-d H:i:s');
    if ($image->save()) return 0;
    return -3;
  }

  public function remove($typeId) {
    $image = Image::where('typeId', $typeId)->first();
    if (!$image) return -2;
    $image = Image::where('typeId', $typeId)->get();
    if ($image->delete()) {
      foreach ($image as $key => $value) {
        removeImage($value->name);
      }
      return 0;
    }
    return -3;
  }

}
