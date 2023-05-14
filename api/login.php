<?php
require_once 'vendor/autoload.php';
require_once 'common.php';
require_once 'GoogleAuthenticator/PHPGangsta/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret();  
$qrCodeUrl = $ga->getQRCodeGoogleUrl('ZbynekRybicka.cz/bozi-slovo', $secret);
echo json_encode([$qrCodeUrl, $secret]);
