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
    echo "=== Healthcheck ===\n";
    $health = $client->healthcheck();
    echo json_encode($health, JSON_PRETTY_PRINT) . "\n\n";

    echo "=== Autenticacion automatica al listar cuentas ===\n";
    $accounts = $client->accounts()->list();
    echo json_encode($accounts, JSON_PRETTY_PRINT) . "\n\n";

    echo "Token activo: " . ($client->getAuthManager()->isAuthenticated() ? 'si' : 'no') . "\n";

} catch (ApiException $e) {
    echo "Error de API [{$e->getHttpStatusCode()}]: " . $e->getMessage() . "\n";
    if ($e->getResponseBody()) {
        echo "Detalles: " . json_encode($e->getResponseBody(), JSON_PRETTY_PRINT) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
