<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Brand extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'brand';

  public function store($obj) {
    $brand = Brand::find($obj->id);
    if($brand) {
      $old_name = $brand->name;
      if($old_name != $obj->name) {
        $product = Product::where('brand', $old_name)->get();
        foreach ($product as $key => $value) {
          $temp = Product::find($value->id);
          $temp->brand = $obj->name;
          $temp->save();
        }
      }
      $brand->name = $obj->name;
      $brand->handle = $obj->handle;
      if($obj->description) $brand->description = $obj->description;
      if($obj->meta_title) $brand->meta_title = $obj->meta_title;
      if($obj->meta_description) $brand->meta_description = $obj->meta_description;
      if($obj->image) $brand->image = $obj->image;
      if($obj->highlight) $brand->highlight = $obj->highlight;
      if($obj->display) $brand->display = $obj->display;
      $brand->updated_at = date('Y-m-d H:i:s');
      $brand->save();
      return true;
    } else {
      $brand = new Brand;
      $brand->id = $obj->id;
      $brand->name = $obj->name;
      $brand->handle = $obj->handle ? $obj->handle : '';
      $brand->description = $obj->description ? $obj->description : '';
      $brand->meta_title = $obj->meta_title ? $obj->meta_title : '';
      $brand->meta_description = $obj->meta_description ? $obj->meta_description : '';
      $brand->image = $obj->image ? $obj->image : '';
      $brand->highlight = $obj->highlight ? $obj->highlight : 0;
      $brand->display = $obj->display ? $obj->display : 0;
      $brand->created_at = date('Y-m-d H:i:s');
      $brand->updated_at = date('Y-m-d H:i:s');
      $brand->save();
      return $brand->id;
    }
  }

  public function sortBrand($brands) {
    $list_brand = [];
    $count_brand = count($brands);
    $floor = floor($count_brand / 7);
    $fmod = fmod($count_brand, 7);
    $skip = 0;
    $temp = 1;
    for ($i=0; $i<7; $i++) {
      $perpage = $floor;
      $skip = $i * $floor;
      $take = $floor;
      if($fmod) {
        $take = $take + 1;
        $fmod--;
        if($i) {
          $skip = $skip + $temp;
          $temp++;
        }
      } else $skip = $skip + $temp;
      $brand = Brand::join('product', 'product.brand', '=', 'brand.name')->where('product.display', 1)->where('product.price', '>', 0)->select('brand.handle as handle', 'brand.name as name')->skip($skip)->take($take)->groupBy('brand.name')->get();
      array_push($list_brand, $brand);
    }
    return $list_brand;
  }
}
