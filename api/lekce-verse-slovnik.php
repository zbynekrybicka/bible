<?php
require_once 'vendor/autoload.php';
require_once 'common.php';

$userId = checkAuth();

if (!$userId) {
    http_response_code(401);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE
    $data = [
        'lekce_vers_id' => $data->lekce_vers_id,
        'slovnik_id' => $data->slovnik_id
    ]; 
    $db->insert('lekce_verse_slovnik', $data)->execute();
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode($data);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // DELETE
    list($lekce_vers_id, $slovnik_id) = explode("-", $_GET['id']);
    $db->delete('lekce_verse_slovnik')->where('lekce_vers_id = %u AND slovnik_id = %u', $lekce_vers_id, $slovnik_id)->execute();
    http_response_code(204);
} else {
    // INVALID METHOD
    http_response_code(405);
}