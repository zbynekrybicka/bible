<?php
require_once 'vendor/autoload.php';
require_once 'common.php';

$userId = checkAuth();
if (!$userId) {
    http_response_code(401);
} else {
    $knihy = $db->select('nazev')->from('knihy')->fetchPairs(null, 'nazev');
    $lekce = $db->select('id, nazev, popis')->from('lekce')->fetchAll();
    $slovnik = $db->select('*')->from('slovnik')->fetchAll();
    $lekce_verse_slovnik = $db->select('*')->from('lekce_verse_slovnik')->fetchAll();

    $lekce_verse = $db->select('k.nazev, lv.*')
        ->from('lekce_verse lv')
        ->join('knihy k')->on('lv.kniha = k.zkratka')
        // ->join('verse v')->on('v.kniha = lv.kniha AND v.kapitola = lv.kapitola AND v.vers BETWEEN lv.`from` AND lv.`to`')
        ->fetchAll();
    foreach ($lekce_verse as &$vers) {
        $vers->obsah = $db->select('vers, obsah')->from('verse')->where('kniha = %s AND kapitola = %u AND vers BETWEEN %u AND %u', $vers->kniha, $vers->kapitola, $vers->from, $vers->to)->fetchPairs('vers', 'obsah');
    }

    echo json_encode([ 
        'knihy' => $knihy,
        'lekce' => $lekce,
        'lekce_verse' => $lekce_verse,
        'slovnik' => $slovnik,
        'lekce_verse_slovnik' => $lekce_verse_slovnik,
    ]);
}