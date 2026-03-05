<?php
require_once __DIR__ . '/../vendor/autoload.php';

use BDCConecta\BDCConectaClient;
use BDCConecta\Configuration;
use BDCConecta\Exceptions\ApiException;

$config = Configuration::production(
    $_ENV['BDC_CLIENT_ID'] ?? 'your-client-id',
    $_ENV['BDC_CLIENT_SECRET'] ?? 'your-client-secret',
    $_ENV['BDC_SECRET_KEY'] ?? 'your-secret-key'
);

$client = new BDCConectaClient($config);

try {
    echo "=== POST /sub-account - Crear Subcuenta CVU ===\n";
    $response = $client->cvuAccounts()->createSubAccount(
        originId: 'SUBCTA-' . time(),
        cbu: '4320001010003138730019',
        label: 'subcuenta.ejemplo',
        currency: '032'
    );
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

} catch (ApiException $e) {
    echo "Error de API [{$e->getHttpStatusCode()}]: " . $e->getMessage() . "\n";
    if ($e->getResponseBody()) {
        echo "Detalles: " . json_encode($e->getResponseBody(), JSON_PRETTY_PRINT) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
