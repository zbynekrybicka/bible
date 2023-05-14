<?php
require_once 'vendor/autoload.php';
require_once 'common.php';

$userId = checkAuth();

if (!$userId) {
    http_response_code(401);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE
    $kniha = $db->select('zkratka')->from('knihy')->where('nazev = %s', $data->kniha)->fetchSingle();
    if (!$kniha) {
        http_response_code(400);
        echo json_encode(["error" => "Neexistující kniha"]);
        die;
    }
    if (!$data->from) {
        $data->from = 0;
        $data->to = 1000;
    }
    if (!$data->to) {
        $data->to = $data->from;
    }
    $dbData = [
        'lekce_id' => $data->lekce_id,
        'kniha' => $kniha,
        'kapitola' => $data->kapitola,
        'from' => $data->from,
        'to' => $data->to,
    ];
    $db->insert('lekce_verse', $dbData)->execute();
    $dbData['nazev'] = $data->kniha;
    $dbData['obsah'] = $db->select('vers, obsah')->from('verse')->where('kniha = %s AND kapitola = %u AND vers BETWEEN %u AND %u', $kniha, $data->kapitola, $data->from, $data->to)->fetchPairs('vers', 'obsah');
    $dbData['id'] = $db->getInsertId();
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode($dbData);
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // UPDATE
    $db->update('lekce_verse', [
        'vysvetleni' => $data->vysvetleni,
    ])->where('id = ?', $data->id)->execute();
    http_response_code(204);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // DELETE
    $id = $_GET['id'];
    $db->delete('lekce_verse_slovnik')->where('lekce_vers_id = %u', $id)->execute();
    $db->delete('lekce_verse')->where('id = %u', $id)->execute();
    http_response_code(204);
} else {
    // INVALID METHOD
    http_response_code(405);
}