<?php

  require_once(dirname(__FILE__) . "/config.php");
	function pushNoti($heading, $message, $url){
    global $config;
    if (!isset($config['onesignal'])) return;

		$content = array(
			"en" => $message
		);
		$headings = array(
			"en" => $heading
		);

		$fields = array(
			'app_id' => $config['onesignal']['app_id'],
			'included_segments' => array('All'),
			'contents' => $content,
			'headings' => $headings,
			'url' => $url
		);

		$fields = json_encode($fields);
    // print("\nJSON sent:\n");
    // print($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic ' . $config['onesignal']['rest_key']));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}
