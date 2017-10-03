<?php

class ControllerHelper {
  public function parseJson($code) {
    switch ($code) {
      case -1:
        return [
          'code' => -1,
          'message' => 'Exist'
        ];
      case -2:
        return [
          'code' => -2,
          'message' => 'Not found'
        ];
      case -3:
        return [
          'code' => -3,
          'message' => 'Server internal error'
        ];
      default:
        return [
          'code' => 0,
          'message' => 'Success',
        ];
    }
  }

  public function response($code) {
    $arr = ControllerHelper::parseJson($code);
    if ($code > 0) $arr['id'] = $code;
    return $arr;
  }

  public function responseData($data) {
    $arr = ControllerHelper::parseJson($data);
    $arr['data'] = $data;
    return $arr;
  }

}
