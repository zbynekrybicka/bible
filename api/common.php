<?php
require_once('vendor/autoload.php'); // načtení knihovny pro JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dibi\Connection;

ini_set('display_errors', 'off');

define("JWT_KEY", "e%Lfd!1ttcUK%6wfOCu%AskBitE&4sS1%S@^t^UhTUfFeLo*3l");

function handleError() {
  
  $error = error_get_last();
  if ($error !== null && $error['type'] === E_ERROR) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode($error);
  }
}

function checkAuth() {
  $jwt = apache_request_headers()['Authorization'] ?? false;
  if (!$jwt) {
    return null;
  }

  $jwt = str_replace('Bearer ', '', $jwt);
  try {
    $user = JWT::decode($jwt, new Key(JWT_KEY, 'HS256'));
  } catch (\Exception $e) {
    throw $e;
    return null;
  }

  return true;
}

$db = new Connection([
  'driver' => 'sqlite3',
  'database' => '../bible.db',
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
  $data = json_decode(file_get_contents("php://input"));
} else {
  $data = null;
}


register_shutdown_function('handleError');
// Nastavení CORS hlaviček
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Pokud je request metoda OPTIONS, vrátí povolené metody
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  http_response_code(204);
  exit();
}