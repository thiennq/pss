<?php

class ControllerHelper {
  public function parseJson($code, $field = null) {
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
      case -4:
        return [
          'code' => -4,
          'message' => $field . ' cannot be empty'
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

  public function checkNull($arr) {
    foreach ($arr as $key => $value) {
      if (!$value) {
        return ControllerHelper::parseJson(-4, $key);
      }
    }
    return 0;
  }

}
