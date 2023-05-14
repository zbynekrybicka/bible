<?php
require_once 'vendor/autoload.php';
require_once 'common.php';

$userId = checkAuth();

if (!$userId) {
    http_response_code(401);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE
    $data = [
        'nazev' => $data->nazev,
        'popis' => '',
    ]; 

    $db->insert('lekce', $data)->execute();
    $data['id'] = $db->getInsertId();
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode($data);
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // UPDATE
    $db->update('lekce', (array) $data)->where('id = %u', $data->id)->execute();
    http_response_code(204);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // DELETE
    $id = $_GET['id'];
    $db->delete('lekce')->where('id = ?', $id)->execute();
    http_response_code(204);
} else {
    // INVALID METHOD
    http_response_code(405);
}