<?php
require_once 'vendor/autoload.php';
require_once 'common.php';

if (preg_match('/([0-9A-Z]+)-(\d+)(:(\d+-?\d*))?/', $_GET['id'], $match)) {
    @list(, $kniha, $kapitola, , $verse) = $match;
    @list($from, $to) = explode("-", $verse);
    if ($to) {
        $result = $db->select('vers, obsah')->from('verse')->where('kniha = %s AND kapitola = %s and vers BETWEEN %u AND %u', $kniha, $kapitola, $from, $to)->fetchPairs('vers', 'obsah');        
    } else if ($from) {
        $result = $db->select('obsah')->from('verse')->where('kniha = %s AND kapitola = %s and vers = %u', $kniha, $kapitola, $from)->fetchSingle();
    } else {
        $result = $db->select('vers, obsah')->from('verse')->where('kniha = %s AND kapitola = %s', $kniha, $kapitola)->fetchPairs('vers', 'obsah');        
    }
    echo json_encode($result);
} else {
    http_response_code(400);    
}