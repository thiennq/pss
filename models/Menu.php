<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Menu extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'menu';

  public function listAll() {
    $data = Menu::all();
    foreach ($data as $key => $menu) {
      if($menu->parent_id) $menu->parent = Menu::find($menu->parent_id)->title;
    }
    return $data;
  }
  
  public function store($data) {
    $menu = new Menu;
    $menu->title = $data['title'];
    $menu->link = $data['link'] ? $data['link'] : '';
    $menu->link_type = $data['link_type'] ? $data['link_type'] : '';
    if ($data['parent_id'] != -1) {
      $parent = Menu::find($data['parent_id']);
      $parent->priority = $parent->priority + 1;
      $parent->save();
    }
    $menu->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $menu->priority = $data['priority'];
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
    $menu->priority = $data['priority'];
    if ($menu->parent_id != $data['parent_id']) {
      if ($menu->parent_id != -1) {
        $parent1 = Menu::find($menu->parent_id);
        $parent1->priority = $parent1->priority - 1;
        $parent1->save();
        $submenus = Menu::where('parent_id', $parent1->id)->where('priority','>', $menu->priority)->get();
        foreach ($submenus as $submenu) {
          $submenu->priority = $submenu->priority - 1;
          $submenu->save();
        }
      }
      if ($data['parent_id'] != -1) {
        $parent2 = Menu::find($data['parent_id']);
        $parent2->priority = $parent2->priority + 1;
        $menu->priority = $parent2->priority;
        $parent2->save();
      }
      else {
        $menu->priority = -1;
      }
    }
    $menu->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $menu->updated_at = date('Y-m-d H:i:s');
    if ($menu->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $menu = Menu::find($id);
    if (!$menu) return -2;
    if ($menu->parent_id == -1) {
      $submenus = Menu::where('parent_id', $id)->get();
      foreach ($submenus as $submenu) {
        $submenu->delete();
      }
    }
    else {
      $parent = Menu::find($menu->parent_id);
      $parent->priority = $parent->priority - 1;
      $parent->save();
      $submenus = Menu::where('parent_id', $menu->parent_id)->where('priority','>', $menu->priority)->get();
      foreach ($submenus as $submenu) {
        $submenu->priority = $submenu->priority - 1;
        $submenu->save();
      }
    }
    if ($menu->delete()) return 0;
    return -3;
  } 
  public function getMenu() {
    $menus = Menu::where('parent_id', -1)->get();
    foreach ($menus as $menu) {
      $menu->handle = 'menu-' . createHandle($menu->title);
      $id = $menu->id;
      $menu->submenu = 0;
      $submenu = Menu::where('parent_id', $id)->orderBy('priority', 'asc')->get();
      if(count($submenu)) {
        foreach ($submenu as $value) {
          $value->handle = 'menu-' . createHandle($value->title);
        }
        $menu->submenu = $submenu;
      }
    }
    return $menus;
  }

  public function menuSidebarCollection() {
    $menus = Collection::where('parent_id', -1)->get();
    foreach ($menus as $key => $menu) {
      $childs = Collection::where('parent_id', $menu->id)->get();
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

}
