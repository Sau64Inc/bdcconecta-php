<?php

namespace BDCConecta\Http;

use BDCConecta\Configuration;
use BDCConecta\Exceptions\NetworkException;
use BDCConecta\Exceptions\ApiException;
use BDCConecta\Exceptions\AuthenticationException;
use BDCConecta\Exceptions\ValidationException;

class Client
{
    private ?\CurlHandle $curlHandle = null;
    private ?DebugInfo $lastDebugInfo = null;

    public function __construct(
        private readonly Configuration $config
    ) {}

    public function get(string $uri, array $query = [], array $headers = []): Response
    {
        return $this->request('GET', $uri, [
            'query' => $query,
            'headers' => $headers
        ]);
    }

    public function post(string $uri, array $data = [], array $headers = []): Response
    {
        return $this->request('POST', $uri, [
            'json' => $data,
            'headers' => $headers
        ]);
    }

    public function patch(string $uri, array $data = [], array $headers = []): Response
    {
        return $this->request('PATCH', $uri, [
            'json' => $data,
            'headers' => $headers
        ]);
    }

    public function put(string $uri, array $data = [], array $headers = []): Response
    {
        return $this->request('PUT', $uri, [
            'json' => $data,
            'headers' => $headers
        ]);
    }

    public function delete(string $uri, array $headers = []): Response
    {
        return $this->request('DELETE', $uri, [
            'headers' => $headers
        ]);
    }

    public function request(string $method, string $uri, array $options = []): Response
    {
        $ch = $this->getCurlHandle();

        // Build full URL
        $url = $this->buildUrl($uri, $options['query'] ?? []);

        // Get payload if exists
        $payload = $options['json'] ?? [];

        // Initialize response headers array
        $responseHeaders = [];

        // Set base options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => $this->config->getTimeout(),
            CURLOPT_CONNECTTIMEOUT => $this->config->getConnectTimeout(),
            CURLOPT_SSL_VERIFYPEER => $this->config->shouldVerifySSL(),
            CURLOPT_SSL_VERIFYHOST => $this->config->shouldVerifySSL() ? 2 : 0,
            CURLOPT_HEADERFUNCTION => $this->getHeaderCallback($responseHeaders)
        ]);

        // Build headers with X-SIGNATURE if payload exists
        $headers = $this->buildHeaders($uri, $payload, $options['headers'] ?? []);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Set body for POST/PUT/PATCH
        if (!empty($payload) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $body = json_encode($payload);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        // Build cURL command for debug before executing
        $isDebug = $this->config->isDebug();
        if ($isDebug) {
            $this->lastDebugInfo = $this->buildDebugInfo(
                $method,
                $url,
                $headers,
                $payload,
                in_array($method, ['POST', 'PUT', 'PATCH'])
            );
        }

        // Execute request
        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);

            if ($isDebug) {
                $this->lastDebugInfo->setError("CURL error ({$errno}): {$error}");
                $this->printDebug();
            }

            throw new NetworkException("CURL error: {$error}", $errno);
        }

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = new Response(
            httpStatusCode: $httpStatusCode,
            headers: $responseHeaders,
            body: $responseBody
        );

        if ($isDebug) {
            $this->lastDebugInfo->setResponse($httpStatusCode, $responseHeaders, $responseBody);
            $this->printDebug();
        }

        // First check HTTP status code
        if (!$response->isHttpSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        // Then check API status code from response body
        if (!$response->isApiSuccessful()) {
            $this->throwSpecificException($response);
        }

        return $response;
    }

    private function getCurlHandle(): \CurlHandle
    {
        if ($this->curlHandle === null) {
            $this->curlHandle = curl_init();
        }
        return $this->curlHandle;
    }

    private function buildUrl(string $uri, array $query): string
    {
        $url = $this->config->getBaseUrl() . '/' . ltrim($uri, '/');

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    private function buildHeaders(string $uri, array $payload, array $customHeaders): array
    {
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        // Add custom headers first
        foreach ($customHeaders as $key => $value) {
            $headers[] = "{$key}: {$value}";
        }

        // Generate X-SIGNATURE if payload exists
        if (!empty($payload)) {
            $signature = $this->generateSignature($uri, $payload);
            $headers[] = "X-SIGNATURE: {$signature}";
        }

        return $headers;
    }

    /**
     * Generate X-SIGNATURE header
     * Format: HMAC-SHA256 of '[uriPath]' + JSON payload
     */
    private function generateSignature(string $uri, array $payload): string
    {
        // Remove leading slash from URI
        $uriPath = ltrim($uri, '/');

        // Build signature data: [uriPath] + JSON payload
        $data = '[' . $uriPath . ']' . json_encode($payload);

        // Generate HMAC SHA256
        return hash_hmac('sha256', $data, $this->config->getSecretKey());
    }

    private function getHeaderCallback(array &$headers): callable
    {
        $headers = [];

        return function ($ch, $header) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header, 2);

            if (count($header) < 2) {
                return $len;
            }

            $headers[strtolower(trim($header[0]))] = trim($header[1]);

            return $len;
        };
    }

    /**
     * Throw specific exception based on API error type
     */
    private function throwSpecificException(Response $response): never
    {
        $apiException = ApiException::fromResponse($response);

        // Throw more specific exceptions based on error type
        if ($apiException->isAuthError()) {
            throw new AuthenticationException(
                $apiException->getMessage(),
                $apiException->getCode(),
                $apiException->getContext()
            );
        }

        if ($apiException->isValidationError()) {
            throw new ValidationException(
                $apiException->getMessage(),
                $apiException->getCode(),
                $apiException->getContext()
            );
        }

        // Default to generic ApiException
        throw $apiException;
    }

    public function getLastDebugInfo(): ?DebugInfo
    {
        return $this->lastDebugInfo;
    }

    private function buildDebugInfo(string $method, string $url, array $headers, array $payload, bool $hasBody): DebugInfo
    {
        $curlParts = ['curl -X ' . $method];

        foreach ($headers as $header) {
            $curlParts[] = "-H " . escapeshellarg($header);
        }

        if ($hasBody && !empty($payload)) {
            $curlParts[] = "-d " . escapeshellarg(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        if (!$this->config->shouldVerifySSL()) {
            $curlParts[] = '--insecure';
        }

        $curlParts[] = escapeshellarg($url);

        return new DebugInfo(
            curlCommand: implode(" \\\n  ", $curlParts),
            method: $method,
            url: $url,
            requestHeaders: $headers,
            requestBody: !empty($payload) ? $payload : null
        );
    }

    private function printDebug(): void
    {
        if ($this->lastDebugInfo === null) {
            return;
        }

        $output = $this->lastDebugInfo->toString();

		error_log($output);
    }

    public function __destruct()
    {
        if ($this->curlHandle !== null) {
            curl_close($this->curlHandle);
        }
    }
}
