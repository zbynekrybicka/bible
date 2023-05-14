<?php
require_once 'vendor/autoload.php';
require_once 'common.php';
require_once 'GoogleAuthenticator/PHPGangsta/GoogleAuthenticator.php';

use Firebase\JWT\JWT;

$code = $data->code;
$secret = "LPRNT54WMSLBP7KB";
$authId = "g8IOvjB7WVpUH195lCA8COlJLsQ8ooFIv3fyvlGu7EDBtjk5DP";

$ga = new \PHPGangsta_GoogleAuthenticator();
$valid = $ga->verifyCode($secret, $code);

if ($valid) {
  // Kód je platný, autentizace byla úspěšná
  $payload = array(['authId' => $authId]);
  $token = JWT::encode($payload, JWT_KEY, 'HS256');
  echo json_encode($token);
} else {
  // Kód není platný
  http_response_code(400);
  echo json_encode(array('error' => 'Autorizační kód není platný.'));
}