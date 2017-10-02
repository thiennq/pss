<?php

$ROOT = dirname(dirname(__FILE__));
require($ROOT . '/vendor/autoload.php');
use GuzzleHttp\Client;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
$dotenv = new Dotenv\Dotenv($ROOT);
$dotenv->load();

function sendTelegram($message){
  $API_KEY = getenv('TELEGRAM_KEY');
  $chatID = getenv('TELEGRAM_GROUPID');
  $client = new GuzzleHttp\Client([
    'curl' => [
      CURLOPT_SSL_VERIFYPEER => false
    ]
  ]);

  $target_url = 'https://api.telegram.org/bot' .$API_KEY. '/sendMessage';
  $data = array(
      'chat_id'   => $chatID,
      'text'     => $message
  );
  try {
    $response = $client->request('POST', $target_url, array(
      'json' => $data
    ));
  } catch (Exception $e) {
    $response = $e->getResponse();
    die($e->getMessage());
  }

  $data = $response->getBody(true);
	$res = json_decode($data, true);
  if ($res["ok"]) {
    return array(
      "code" => 0,
      "message" => "success"
    );
  } else {
    return array(
      "code" => $res["error_code"],
      "message" => $res["description"]
    );
  }
}
