<?php
require_once 'vendor/autoload.php';
require_once 'common.php';

$userId = checkAuth();

if (!$userId) {
    http_response_code(401);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE
    $slovnik = $db->select('id, vyraz, vyznam')->from('slovnik')->where('vyraz = %s', $data->vyraz)->fetch();
    if (!$slovnik) {
        $slovnik = [
            'vyraz' => $data->vyraz,
            'vyznam' => ''
        ];
        $db->insert('slovnik', $slovnik)->execute();
        $slovnik['id'] = $db->getInsertId();
    } else {
        $slovnik = (array) $slovnik;
    }
    $lekceVerseSlovnik = [
        'lekce_vers_id' => $data->lekce_vers_id,
        'slovnik_id' => $slovnik['id']
    ];
    $db->insert('lekce_verse_slovnik', $lekceVerseSlovnik)->execute();
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode([
        'slovnik' => $slovnik,
        'lekce_verse_slovnik' => $lekceVerseSlovnik        
    ]);
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // UPDATE
    $db->update('slovnik', (array) $data)->where('id = ?', $data->id)->execute();
    http_response_code(204);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // DELETE
    $id = $_GET['id'];
    $db->delete('slovnik')->where('id = ?', $id)->execute();
    http_response_code(204);
} else {
    // INVALID METHOD
    http_response_code(405);
}