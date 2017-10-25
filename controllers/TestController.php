<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once('helper.php');
use ControllerHelper as Helper;

class TestController extends Controller {

  public function truyen() {
  }

  public function sendMail() {
    $to = 'duynhan.nguyenhoang@gmail.com';
    $subject = 'Test Mail';
    $variables = array();
    $variables['customer_name'] = 'Nhan';
    $variables['customer_email'] = 'nhan@abcxyz.com';
    $variables['create-pass-link'] = 'gmail.com';
    $template = file_get_contents('../framework/mail-template/forget-pw.html');
    foreach ($variables as $key => $value) {
      $template = str_replace('{{'.$key.'}}', $value, $template);
    }
    $header = 'reply from: Nhan';
    PHPMailer($to, $subject, $template, $header);
  }

}
?>
