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
