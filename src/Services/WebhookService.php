<?php

namespace BDCConecta\Services;

class WebhookService extends AbstractService
{
    /**
     * List webhooks
     * GET /webhook
     */
    public function list(): array
    {
        return $this->get('/webhook');
    }

    /**
     * Create webhook
     * POST /webhook
     *
     * Creates a webhook to receive notifications for incoming transfers to the specified CBU.
     *
     * @param string $cbu CBU to receive notifications for (22 digits, required)
     * @param string $url URL where notifications will be sent (required)
     * @param string $frase Secret phrase for webhook validation (required)
     * @return array
     */
    public function create(
        string $cbu,
        string $url,
        string $frase
    ): array {
        return $this->post('/webhook', [
            'cbu' => $cbu,
            'url' => $url,
            'frase' => $frase
        ]);
    }

    /**
     * Update webhook
     * PUT /webhook
     *
     * Updates the webhook configuration for the specified CBU.
     *
     * @param string $cbu CBU of the webhook to update (22 digits, required)
     * @param string $url New URL where notifications will be sent (required)
     * @param string $frase New secret phrase for webhook validation (required)
     * @return array
     */
    public function update(
        string $cbu,
        string $url,
        string $frase
    ): array {
        return $this->put('/webhook', [
            'cbu' => $cbu,
            'url' => $url,
            'frase' => $frase
        ]);
    }

    /**
     * Send notification preview
     * POST /webhook/send-notification-preview
     *
     * @param string $frase Secret phrase sent as X-Secret-Phrase header
     * @param string $cbu CBU to send the notification preview to (22 digits)
     * @param string $idCoelsa Coelsa operation ID
     * @param string $cuitOrigen Origin CUIT
     * @param string $cbuOrigen Origin CBU
     * @param string $cuitDestino Destination CUIT
     * @param string $cbuDestino Destination CBU
     * @param float $importe Transfer amount
     * @return array
     */
    public function sendNotificationPreview(
        string $frase,
        string $cbu,
        string $idCoelsa,
        string $cuitOrigen,
        string $cbuOrigen,
        string $cuitDestino,
        string $cbuDestino,
        float $importe
    ): array {
        return $this->request('POST', '/webhook/send-notification-preview', [
            'headers' => [
                'X-Secret-Phrase' => $frase,
            ],
            'json' => [
                'cbu' => $cbu,
                'notification' => [
                    'idCoelsa' => $idCoelsa,
                    'cuitOrigen' => $cuitOrigen,
                    'cbuOrigen' => $cbuOrigen,
                    'cuitDestino' => $cuitDestino,
                    'cbuDestino' => $cbuDestino,
                    'importe' => $importe,
                ],
            ],
        ]);
    }
}
