<?php

  $files = scandir('.');
  $files = array_diff($files, array('.', '..', __FILE__));
  foreach ($files as $file) {
    try {
      error_log($file);
      require_once($file);
    } catch (Exception $e) {
      echo $e;
    }
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, '/api/website/initUser');
  $result = curl_exec($ch);
  curl_close($ch);
  $reuslt = json_decode($result, true);
  error_log($result['message']);
