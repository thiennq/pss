<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class MetaController extends Controller {

  public function store (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $key = $body['key'];
    $value = $body['value'];

    $Meta = Meta::where('key', $key)->first();
    if($Meta) {
      $Meta->value = json_encode($value);
      $Meta->updated_at = date('Y-m-d H:i:s');
      if($Meta->save()) {
        return json_encode(array(
          'code' => 0,
          'message' => 'Update'
        ));
      } else {
        return json_encode(array(
          'code' => -2,
          'message' => 'Unknown error'
        ));
      }
    } else {
      $data = new Meta;
      $data->key = $key;
      $data->value = json_encode($value);
      $data->created_at = date('Y-m-d H:i:s');
      $data->updated_at = date('Y-m-d H:i:s');
      if($data->save()) {
        return json_encode(array(
          'code' => 0,
          'message' => 'Created'
        ));
      } else {
        return json_encode(array(
          'code' => -2,
          'message' => 'Unknown error'
        ));
      }
    }
  }
}

?>
