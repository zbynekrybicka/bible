<?php
require_once 'vendor/autoload.php';
require_once 'common.php';

$knihy = $db->select('zkratka, nazev')->from('knihy')->fetchPairs('zkratka', 'nazev');
echo json_encode($knihy);