<?php
require_once 'vendor/autoload.php';
require_once 'common.php';

$userId = checkAuth();

if (!$userId) {
    http_response_code(401);
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
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
        'kniha' => $kniha,
        'kapitola' => $data->kapitola,
        'from' => $data->from,
        'to' => $data->to,
    ];
    $db->update('lekce_verse', $dbData)->where('id = %u', $data->id)->execute();
    $dbData['id'] = $data->id;
    $dbData['nazev'] = $data->kniha;
    $dbData['obsah'] = $db->select('vers, obsah')->from('verse')->where('kniha = %s AND kapitola = %u AND vers BETWEEN %u AND %u', $kniha, $data->kapitola, $data->from, $data->to)->fetchPairs('vers', 'obsah');
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($dbData);
} else {
    // INVALID METHOD
    http_response_code(405);
}
