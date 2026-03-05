<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Ejemplo de como se genera el X-SIGNATURE manualmente.
 * El SDK lo genera automaticamente para requests con payload (POST/PUT/PATCH).
 *
 * Formato: HMAC-SHA256 de '[uriPath]' + JSON payload usando el secretKey
 */

$secretKey = 'ZO2GppJgbjp2BSNnkP7sG';

// Ejemplo: POST a /movements/transfer-request
$uri = '/movements/transfer-request';
$payload = [
    'originAccount' => '0000003100012345678901',
    'destinationAccount' => '0000003100099999999999',
    'amount' => 1000.50,
    'currency' => 'ARS'
];

$uriPath = ltrim($uri, '/');
$data = '[' . $uriPath . ']' . json_encode($payload);
$signature = hash_hmac('sha256', $data, $secretKey);

echo "URI: {$uri}\n";
echo "Payload: " . json_encode($payload) . "\n";
echo "Data para firmar: {$data}\n";
echo "X-SIGNATURE: {$signature}\n";
