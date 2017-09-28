<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Redirect.php");

class AdminRedirectController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Redirect::all();
    return $this->view->render($response, 'admin/redirect.pug', array(
      'data' => $data
    ));
  }

  public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
    $check = Redirect::where('old', $body['old_url'])->where('new', $body['new_url'])->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $redirect = new Redirect;
    $redirect->old = $body['old_url'];
    $redirect->new = $body['new_url'];
    $redirect->created_at = date('Y-m-d H:i:s');
    $redirect->updated_at = date('Y-m-d H:i:s');
    if($redirect->save()) {
      return $response->withJson([
        'code' => 0,
				'message' => 'Created'
      ]);
    }
    return $response->withJson([
      'code' => -2,
      'message' => 'Error'
    ]);
	}

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $check = Redirect::where('old', $body['old_url'])->where('new', $body['new_url'])->where('id', '!=', $id)->first();
    if($check) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Exist'
      ]);
    }
    $redirect = Redirect::find($id);
    $redirect->old = $body['old_url'];
    $redirect->new = $body['new_url'];
    $redirect->updated_at = date('Y-m-d H:i:s');
    if($redirect->save()) {
      return $response->withJson([
        'code' => 0,
				'message' => 'Updated'
      ]);
    }
    return $response->withJson([
      'code' => -2,
      'message' => 'Error'
    ]);
	}

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $redirect = Redirect::find($id);
    if($redirect) {
      $redirect->delete();
      return $response->withJson([
        'code' => 0,
				'message' => 'Delete'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'Error'
    ]);
	}

  public function postImport(Request $request, Response $response) {
		$tmp_name = $_FILES['file']['tmp_name'];
		$new_name = time().'-'.$_FILES['file']['name'];
		$path = ROOT . '/public/excel/'.$new_name;
    $count = 0;
		if(move_uploaded_file($tmp_name, $path)) {
      $inputFileType = PHPExcel_IOFactory::identify($path);
      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
      $objPHPExcel = $objReader->load($path);
      $objWorksheet = $objPHPExcel->getSheet(0);
      $highestRow = $objWorksheet->getHighestRow();
      for ($row = 2; $row <= $highestRow; $row++) {
        $old = $objWorksheet->getCellByColumnAndRow(0, $row)->getCalculatedValue();
        $new = $objWorksheet->getCellByColumnAndRow(1, $row)->getCalculatedValue();
        if($old && $new) {
          $check = Redirect::where('old', $old)->where('new', $new)->first();
          if(!$check) {
            $redirect = new Redirect;
            $redirect->old = $old;
            $redirect->new = $new;
            $redirect->created_at = date('Y-m-d H:i:s');
            $redirect->updated_at = date('Y-m-d H:i:s');
            if($redirect->save()) {
              $count++;
              error_log($old . '--' . $new);
            }
          }
        }
      }
      return $response->withJson(array(
        'code' => 0,
        'message' => 'Success',
        'count' => $count
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Error'
    ));
  }
}

?>
