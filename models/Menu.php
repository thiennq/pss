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
    $menu->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
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
    if ($menu->delete()) return 0;
    return -3;
  } 
  public function getMenu() {
    $menus = Menu::where('parent_id', -1)->get();
    foreach ($menus as $menu) {
      $menu->handle = 'menu-' . convertHandle($menu->title);
      $id = $menu->id;
      $menu->submenu = 0;
      $submenu = Menu::where('parent_id', $id)->get();
      if(count($submenu)) {
        foreach ($submenu as $value) {
          $value->handle = 'menu-' . convertHandle($value->title);
        }
        $menu->submenu = $submenu;
      }
    }
    return $menus;
  }
}
