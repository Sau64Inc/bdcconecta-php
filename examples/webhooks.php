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
    echo "=== Listar webhooks existentes ===\n";
    $webhooks = $client->webhooks()->list();
    echo json_encode($webhooks, JSON_PRETTY_PRINT) . "\n\n";

    echo "=== Crear nuevo webhook ===\n";
    $newWebhook = $client->webhooks()->create(
        cbu: '4320001010003138730019',
        url: 'https://example.com/webhook/bdcconecta',
        frase: 'mi_frase_secreta'
    );
    echo "Webhook creado:\n";
    echo json_encode($newWebhook, JSON_PRETTY_PRINT) . "\n\n";

    echo "=== Actualizar webhook ===\n";
    $updated = $client->webhooks()->update(
        cbu: '4320001010003138730019',
        url: 'https://example.com/webhook/bdcconecta/updated',
        frase: 'nueva_frase_secreta'
    );
    echo "Webhook actualizado:\n";
    echo json_encode($updated, JSON_PRETTY_PRINT) . "\n\n";

    echo "=== Enviar preview de notificacion ===\n";
    $preview = $client->webhooks()->sendNotificationPreview(
        frase: 'frase_secreta',
        cbu: '4320001010003138730019',
        idCoelsa: 'KLOEJWV9JGK6D789QMD0GZ',
        cuitOrigen: '00000000001',
        cbuOrigen: '0000000000000000000001',
        cuitDestino: '00000000002',
        cbuDestino: '0000000000000000000002',
        importe: 1000
    );
    echo "Preview enviado:\n";
    echo json_encode($preview, JSON_PRETTY_PRINT) . "\n\n";

} catch (ApiException $e) {
    echo "Error de API [{$e->getHttpStatusCode()}]: " . $e->getMessage() . "\n";
    if ($e->getResponseBody()) {
        echo "Detalles: " . json_encode($e->getResponseBody(), JSON_PRETTY_PRINT) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
