# BDCConecta PHP SDK

SDK no oficial en PHP para la API de BDC Conecta (Banco de Comercio).

> **Nota:** Los metodos que reciben parametros con nombre (named arguments) fueron verificados contra la API. Los metodos que aun reciben `array $data` no fueron testeados, pero deberian funcionar correctamente. Para conocer los parametros que lleva cada uno, consultar la documentacion de la API en https://docs.bdcconecta.com

## Requisitos

- PHP 8.2 o superior
- ext-curl
- ext-json
- ext-mbstring

## Instalacion

```bash
composer require bdcconecta/php-sdk
```

## Uso Rapido

```php
<?php
require_once 'vendor/autoload.php';

use BDCConecta\BDCConectaClient;
use BDCConecta\Configuration;

$config = Configuration::production('tu-client-id', 'tu-client-secret', 'tu-secret-key');
$client = new BDCConectaClient($config);

// Verificar estado del servicio
$health = $client->healthcheck();

// Listar cuentas
$accounts = $client->accounts()->list();
```

## Configuracion

### Entornos

El SDK soporta dos entornos y requiere tres credenciales:

- **clientId**: ID del cliente
- **clientSecret**: Secret del cliente (para generar JWT)
- **secretKey**: Clave secreta (para generar X-SIGNATURE)

**Produccion:**
```php
$config = Configuration::production('client-id', 'client-secret', 'secret-key');
```

**Sandbox/Homologacion:**
```php
$config = Configuration::sandbox('client-id', 'client-secret', 'secret-key');
```

### Opciones de Configuracion

```php
$config = Configuration::production('client-id', 'client-secret', 'secret-key')
    ->setTimeout(60)           // Timeout en segundos (default: 30)
    ->setConnectTimeout(15)    // Connect timeout en segundos (default: 10)
    ->setVerifySSL(true);      // Verificar certificados SSL (default: true)
```

## Manejo de Errores

El SDK maneja automaticamente los codigos de error de la API. Cada respuesta incluye un `statusCode` donde:
- **0** = Operacion exitosa
- **Otros codigos** = Errores especificos (ver lista completa en `ErrorCodes.php`)

```php
use BDCConecta\Exceptions\ApiException;
use BDCConecta\Exceptions\AuthenticationException;
use BDCConecta\Exceptions\ValidationException;
use BDCConecta\Exceptions\NetworkException;

try {
    $accounts = $client->accounts()->list();
} catch (AuthenticationException $e) {
    // Errores de autenticacion (codigos 94, 96-103, 1000)
    echo "Error de autenticacion: " . $e->getMessage();
} catch (ValidationException $e) {
    // Errores de validacion (codigos 4, 5, 9, 10, etc.)
    echo "Error de validacion: " . $e->getMessage();
} catch (ApiException $e) {
    // Otros errores de API
    echo "Error: " . $e->getMessage();
    echo "HTTP Status: " . $e->getHttpStatusCode();
    echo "API Status Code: " . $e->getApiStatusCode();
} catch (NetworkException $e) {
    echo "Error de red: " . $e->getMessage();
}
```

### Codigos de Error Comunes

| Codigo | Descripcion |
|--------|-------------|
| 0 | Solicitud Procesada con Exito |
| 1 | Cuenta Invalida |
| 4 | Parametro(s) Invalido(s) |
| 34 | Cuenta no habilitada |
| 70 | CBU no valido |
| 96 | Usuario Invalido, No autorizado |
| 101 | Token expirado |
| 810 | Saldo Insuficiente |
| 1000 | X-SIGNATURE no valido |

Ver la lista completa de codigos en `src/ErrorCodes.php`.

## Servicios Disponibles

### Cuentas

```php
// Listar todas las cuentas - GET /accounts
$response = $client->accounts()->list();

// Obtener informacion de una cuenta - GET /accounts/info/{CBU_CVU_ALIAS}
// Acepta CBU, CVU o Alias
$response = $client->accounts()->getInfo('0000000000000000000000');
$response = $client->accounts()->getInfo('ALIAS.DE.CUENTA');
```

### Subcuentas CVU

```php
// Crear subcuenta - POST /sub-account
$response = $client->cvuAccounts()->createSubAccount(
    originId: 'SUBCTA-' . time(),
    cbu: '4320001010003138730019',
    label: 'subcuenta.ejemplo',    // opcional
    currency: '032'                // 032=ARS (default), 840=USD
);

// Listar subcuentas CVU - POST /accounts/get-cvu-accounts
$response = $client->cvuAccounts()->getCvuAccounts(
    cbu: '4320001010003138730019',
    pageOffset: 0,                      // opcional
    pageSize: 10,                       // opcional
    sortDirection: SortDirection::DESC,  // opcional
    sortField: 'fecha_creacion',        // opcional
    startCreatedDate: '2024-01-01',     // opcional
    endCreatedDate: '2024-12-31'        // opcional
);

// Obtener informacion de subcuenta - GET /sub-account/{originId}
$response = $client->cvuAccounts()->getSubAccount('SUBCTA-123456');

// Actualizar estado de subcuenta - PATCH /sub-account/{cvu}
// Transiciones validas: ACTIVE->SUSPENDED, ACTIVE->BLOCKED, SUSPENDED->ACTIVE, SUSPENDED->BLOCKED
$response = $client->cvuAccounts()->updateSubAccount(
    cvu: '0000003100012345678901',
    status: SubAccountStatus::SUSPENDED
);
```

### Movimientos

```php
// Consultar movimientos - POST /movements/{CBU_CVU_ALIAS}
$movements = $client->movements()->getMovements('0000003100012345678901', [
    'startDate' => '2024-01-01',
    'endDate' => '2024-01-31',
    'page' => 1,
    'limit' => 50
]);
```

### Transferencias

```php
// Crear solicitud de transferencia - POST /movements/transfer-request
$transfer = $client->transfers()->createTransferRequest([
    'originAccount' => '0000003100012345678901',
    'destinationAccount' => '0000003100099999999999',
    'amount' => 1000.50,
    'currency' => 'ARS',
    'concept' => 'Payment',
    'reference' => 'REF123'
]);

// Consultar estado de transferencia - GET /movements/transfer-request/{originId}
$status = $client->transfers()->getTransferRequest('originId');
```

### Webhooks

```php
// Listar webhooks - GET /webhook
$webhooks = $client->webhooks()->list();

// Crear webhook - POST /webhook
$webhook = $client->webhooks()->create(
    cbu: '4320001010003138730019',
    url: 'https://example.com/webhook/bdcconecta',
    frase: 'mi_frase_secreta'
);

// Actualizar webhook - PUT /webhook
$updated = $client->webhooks()->update(
    cbu: '4320001010003138730019',
    url: 'https://example.com/webhook/updated',
    frase: 'nueva_frase_secreta'
);

// Enviar preview de notificacion - POST /webhook/send-notification-preview
// La frase se envia como header X-Secret-Phrase
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
```

### DEBIN

```php
// Crear DEBIN - POST /debin-create
$debin = $client->debin()->createDebin([...]);

// Listar DEBINs - POST /debin-list
$debins = $client->debin()->listDebins([...]);

// Confirmar DEBIN - POST /confirm-debin
$confirmed = $client->debin()->confirmDebin([...]);

// Remover DEBIN - POST /debin-remove
$removed = $client->debin()->removeDebin([...]);

// Validar CUIT - POST /validate-cuit
$validation = $client->debin()->validateCuit([...]);

// Adjuntar comprador - POST /buyer-attachment
$buyer = $client->debin()->attachBuyer([...]);

// Obtener comprador por CUIT - POST /buyer-by-cuit
$buyer = $client->debin()->getBuyerByCuit([...]);

// Remover cuenta de comprador - POST /buyer-remove-account
$removed = $client->debin()->removeBuyerAccount([...]);

// Adjuntar vendedor - POST /seller-attachment
$seller = $client->debin()->attachSeller([...]);

// Obtener vendedor por CUIT - POST /seller-by-cuit
$seller = $client->debin()->getSellerByCuit([...]);

// Remover cuenta de vendedor - POST /seller-remove-account
$removed = $client->debin()->removeSellerAccount([...]);
```

### Bancos

```php
// Listar bancos - GET /banks
$banks = $client->banks()->list();
```

### Monedas

```php
// Obtener tasas de cambio de referencia - GET /global/data/currencies-rate-reference
$rates = $client->currency()->getRateReference();
```

### Alias CVU/CBU

```php
// Actualizar alias CVU - PATCH /cvu-alias
$updated = $client->cvuAlias()->update([...]);

// Crear alias CBU - POST /cbu-alias
$created = $client->cbuAlias()->create([...]);

// Obtener alias CBU - GET /cbu-alias/{cbu}
$alias = $client->cbuAlias()->getAlias('0000003100012345678901');

// Actualizar alias CBU - PATCH /cbu-alias
$updated = $client->cbuAlias()->update([...]);

// Eliminar alias CBU - DELETE /cbu-alias/{alias}
$deleted = $client->cbuAlias()->delete('mi.alias.cbu');
```

### Otros Servicios

```php
// Obtener datos de entidad - POST /global/data/get-entity
$entity = $client->entity()->getEntity([...]);

// Obtener conceptos SNP - GET /get-snp-concepts
$concepts = $client->snpConcepts()->getConcepts();

// Obtener datos adicionales COELSA - GET /get-coelsa-aditional-data/{idCoelsa}
$data = $client->coelsa()->getAdditionalData('idCoelsa');
```

## Seguridad

### Autenticacion JWT

El SDK maneja automaticamente la autenticacion mediante JWT:
1. Al hacer la primera request, obtiene un token JWT usando `clientId` y `clientSecret`
2. El token se almacena en memoria y se renueva automaticamente cuando expira
3. Todas las requests incluyen el token en el header `Authorization: Bearer {token}`

### X-SIGNATURE

Para requests con payload (POST/PUT/PATCH), el SDK genera automaticamente un header `X-SIGNATURE`:

```
X-SIGNATURE = HMAC-SHA256('[uriPath]' + JSON_payload, secretKey)
```

Ejemplo:
- URI: `/movements/transfer-request`
- Payload: `{"amount": 100}`
- Data a firmar: `[movements/transfer-request]{"amount":100}`

Este header se genera automaticamente.


## URLs de API

- **Produccion**: `https://api.bdcconecta.com`
- **Sandbox/Homologacion**: `https://apihomo.bdcconecta.com`

## Licencia

MIT License. Ver archivo `LICENSE` para mas detalles.
