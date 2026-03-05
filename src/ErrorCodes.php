<?php

namespace BDCConecta;

class ErrorCodes
{
    private const ERROR_MESSAGES = [
        0 => 'Solicitud Procesada con Éxito',
        1 => 'Cuenta Inválida.',
        2 => 'Sin datos que procesar.',
        3 => 'Solicitud con campo Origin Id duplicado.',
        4 => 'Parámetro(s) Inválido(s).',
        5 => 'El Campo originId es Obligatorio.',
        8 => 'Error tipo de cuenta del Comprador.',
        9 => 'CUIT mal formulado del vendedor.',
        10 => 'CUIT mal formulado del comprador.',
        11 => 'Moneda inexistente.',
        12 => 'Descripción inválida.',
        14 => 'CBU destino y origen idénticos.',
        15 => 'CUIT no Bancarizado.',
        20 => 'Servicios No Disponibles.',
        33 => 'Cuenta inexistente.',
        34 => 'Cuenta no habilitada.',
        35 => 'Rechazo por scoring alto.',
        36 => 'Garantías suficientes.',
        37 => 'Error banco crédito - datos incorrectos.',
        38 => 'Error banco crédito - cuenta inexistente.',
        39 => 'Error banco crédito - cuenta no habilitada.',
        48 => 'No posee ID PSP Generado en COELSA, comuníquese con su comercial.',
        49 => 'Error al solicitar token Bantotal.',
        50 => 'Error al procesar la solicitud.',
        51 => 'Longitud de CUIL inválido.',
        52 => 'Opción de sexo incorrecta.',
        53 => 'Formato de número de trámite incorrecto.',
        54 => 'No hay cuentas habilitadas para su usuario, por favor comunicarse con el Área Comercial.',
        55 => 'Cuenta no habilitada para su usuario, por favor comunicarse con el Área Comercial.',
        59 => 'Faltan datos para procesar la solicitud.',
        60 => 'Importe no permitido.',
        61 => 'Moneda no permitido.',
        64 => 'Error al obtener el UID de Persona.',
        65 => 'Longitud del Documento Incorrecto.',
        67 => 'Las cuentas no son de la misma moneda.',
        68 => 'CBU no pertenece a ninguna cuenta.',
        69 => 'CBU no habilitado, comuníquese con el Área Comercial.',
        70 => 'CBU no válido.',
        71 => 'CBU no pertenece a Banco de Comercio.',
        72 => 'CBU Origen y CBU Destino son iguales.',
        75 => 'Longitud del CBU incorrecto.',
        77 => 'Error en el CVU de Origen informado, no habilitado.',
        78 => 'Error en el CBU de Origen informado, no habilitado.',
        79 => 'Error en los datos enviados, revise el formato JSON.',
        80 => 'Solicitud sin datos que procesar.',
        81 => 'Error en los datos enviados, no son válidos.',
        82 => 'Error en el CVU, no se pudo generar.',
        87 => 'Error general Core Bantotal.',
        88 => 'Error general Cuenta Bantotal.',
        90 => 'Servicio no disponible.',
        91 => 'Could Not Connect DB.',
        92 => 'Error fatal, Comuníquese con el Área de Sistemas.',
        93 => 'Error de Comunicación con el servidor.',
        94 => 'Debe informar un token.',
        95 => 'Error comunicación cURL.',
        96 => 'Usuario Inválido, No autorizado.',
        97 => 'Usuario no habilitado para este endpoint, No autorizado.',
        98 => 'Usuario o contraseña incorrectos.',
        99 => 'Usuario no existe.',
        100 => 'Usuario Deshabilitado.',
        101 => 'Token expirado.',
        102 => 'Error al validar el Token.',
        103 => 'Usuario con Nombre de Fantasía Inválido.',
        109 => 'Alias Duplicado.',
        110 => 'Alias No Existe.',
        111 => 'Alias No se puede generar en este momento, reintente de nuevo.',
        120 => 'Error en guardar la factura.',
        121 => 'Error en consultar la factura.',
        203 => 'Error, está operativa no pertenece a esta transacción la misma no se puede realizar desde la misma entidad a través de COELSA.',
        404 => 'Recurso No Encontrado.',
        600 => 'Usuario Ya Existe.',
        601 => 'Error al crear Cuenta de Usuario.',
        800 => 'Datos CBU Débito Incorrecto.',
        801 => 'Datos CBU Crédito Incorrecto.',
        802 => 'La moneda de la cuenta de crédito no coincide con el CUIT Informado.',
        803 => 'El CUIT del titular de la cuenta de crédito no coincide con el CUIT informado.',
        804 => 'El CUIT del titular de la cuenta de débito no coincide con el CUIT informado, comuníquese con el Banco.',
        808 => 'Error en el CBU de Origen, no válido.',
        809 => 'Error en el CBU de Destino, no válido.',
        810 => 'Saldo Insuficiente.',
        1000 => 'X-SIGNATURE no válido.',
        1001 => 'Error de comunicación con COELSA al obtener el token.',
        1002 => 'Validación Fallida de COELSA.',
        1025 => 'API RENAPER no disponible en este momento.',
        2000 => 'Envío de Webhook Ha Fallado.',
        2001 => 'El webhook para del CBU no está habilitado.',
        2002 => 'El CBU no tiene webhook creado.',
        2003 => 'La creación del webhook ha fallado.',
        2004 => 'La edición del webhook ha fallado.',
        2005 => 'El CBU Ya Tiene Webhook Creado.',
        3010 => 'Cannot Set Suspended.',
        3011 => 'Cannot Set Blocked.',
        3012 => 'Cannot Set Active.',
        3013 => 'Cannot Set Suspended or Blocked.',
        3014 => 'Error Processing Patch Subaccount.',
        3024 => 'CVU mal formado.',
        3025 => 'CVU NO EXISTE.',
        4540 => 'Error, se reversó la operación ya que COELSA rechazó la misma.',
        9902 => 'CBU INCORRECTO.',
        9999 => 'Error, no se pudo establecer conexión con COELSA.',
    ];

    public static function getMessage(int $code): string
    {
        return self::ERROR_MESSAGES[$code] ?? "Error desconocido (código: {$code})";
    }

    public static function isSuccess(int $code): bool
    {
        return $code === 0;
    }

    public static function isAuthError(int $code): bool
    {
        return in_array($code, [94, 96, 97, 98, 99, 100, 101, 102, 103, 1000]);
    }

    public static function isValidationError(int $code): bool
    {
        return in_array($code, [4, 5, 9, 10, 12, 51, 52, 53, 59, 65, 70, 75, 79, 81, 800, 801, 808, 809, 3024, 9902]);
    }

    public static function isInsufficientFundsError(int $code): bool
    {
        return $code === 810;
    }

    public static function isAccountError(int $code): bool
    {
        return in_array($code, [1, 33, 34, 54, 55, 68, 69, 71, 77, 78]);
    }

    public static function isServiceUnavailable(int $code): bool
    {
        return in_array($code, [20, 90, 91, 92, 93, 1025, 9999]);
    }
}
