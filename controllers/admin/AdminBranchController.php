<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Region.php");
require_once("../models/Branch.php");

class AdminBranchController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Branch::all();
    return $this->view->render($response, 'admin/branch_list.pug', array(
      'data' => $data
    ));
  }

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $branch = Branch::find($id);
    $regions = Region::orderBy('name', 'asc')->get();
    return $this->view->render($response, 'admin/branch_edit.pug', array(
      'branch' => $branch,
      'regions' => $regions
    ));
  }

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $branch = Branch::find($id);
    if($branch) {
      $branch->name = $body['name'];
      $branch->address = $body['address'];
      $branch->region_id = $body['region_id'];
      $branch->hotline = $body['hotline'];
      $branch->open_hours = $body['open_hours'];
      $branch->close_hours = $body['close_hours'];
      $branch->link = $body['link'];
      $branch->featured_image = $body['featured_image'];
      $branch->calc_inventory = $body['calc_inventory'] ? $body['calc_inventory'] : 0;
      $branch->branch_center = $body['branch_center'] ? $body['branch_center'] : 0;
      $branch->display = $body['display'] ? $body['display'] : 0;
      $branch->updated_at = date('Y-m-d H:i:s');
      if($branch->save()) {
        return $response->withJson(array(
          'code' => 0,
          'message' => 'Updated'
        ));
      }
      return $response->withJson(array(
        'code' => -1,
        'message' => 'Error'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Page Not Found'
    ));
  }


}

?>
