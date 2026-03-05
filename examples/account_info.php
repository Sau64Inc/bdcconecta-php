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

// Puede ser CBU, CVU o Alias
$identifier = 'ALIAS_PRUEBA';

try {
    echo "=== GET /accounts/info/{CBU_CVU_ALIAS} - Obtener Informacion de Cuenta ===\n";
    $response = $client->accounts()->getInfo($identifier);
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

} catch (ApiException $e) {
    echo "Error de API [{$e->getHttpStatusCode()}]: " . $e->getMessage() . "\n";
    if ($e->getResponseBody()) {
        echo "Detalles: " . json_encode($e->getResponseBody(), JSON_PRETTY_PRINT) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
