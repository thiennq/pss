<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Menu extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'menu';

  public function store($data) {
    $menu = new Menu;
    $menu->title = $data['title'];
    $menu->link = $data['link'] ? $data['link'] : '';
    $menu->link_type = $data['link_type'] ? $data['link_type'] : '';
    $menu->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $menu->submenu = $data['submenu'] ? $data['submenu'] : '';
    $menu->created_at = date('Y-m-d H:i:s');
    $menu->updated_at = date('Y-m-d H:i:s');
    if ($menu->save()) return $menu->id;
    return -3;
  }

  public function update($id, $data) {
    $menu = Menu::find($id);
    if (!$menu) return -2;
    $menu->title = $data['title'];
    $menu->link = $data['link'] ? $data['link'] : '';
    $menu->link_type = $data['link_type'] ? $data['link_type'] : '';
    $menu->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $menu->submenu = $data['submenu'] ? $data['submenu'] : '';
    $menu->updated_at = date('Y-m-d H:i:s');
    if ($menu->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $menu = Menu::find($id);
    if (!$menu) return -2;
    if ($menu->delete()) return 0;
    return -3;
  }

  public function menuSidebarCollection() {
    $menus = Collection::where('show_landing_page', 0)->where('parent_id', -1)->get();
    foreach ($menus as $key => $menu) {
      $childs = Collection::where('show_landing_page', 0)->where('parent_id', $menu->id)->get();
      foreach ($childs as $key => $child) {
        $child->link = '/' . $child->link;
        $count = CollectionProduct::where('collection_id', $child->id)
          ->join('product', 'collection_product.product_id', '=', 'product.id')->where('product.in_stock', 1)->where('product.display', 1)->count();
        if(!$count) unset($childs[$key]);
        else $child->count_product = $count;
      }
      $menu->link = '/' . $menu->link;
      $menu->childs = $childs;
      $menu->count_child = count($childs);
    }
    return $menus;
  }

  public function getMenu(){
    $menus = Menu::all();
    foreach ($menus as $key => $menu) {
      $check_submenu = strlen($menu->submenu);
      $menu->check_submenu = $check_submenu;
    }
    return $menus;
  }
}
