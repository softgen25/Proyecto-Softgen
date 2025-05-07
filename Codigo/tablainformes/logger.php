<?php
// php/Logger.php - Sistema de registro de eventos y errores

// Asegurarse de que la sesión esté iniciada para obtener el usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Logger {
    // Directorio donde se guardarán los archivos de log
    // Asume que el directorio 'logs' está en la raíz del proyecto
    private static $logDir = __DIR__ . '/../logs/';

    /**
     * Registra una operación exitosa o un evento del sistema.
     *
     * @param string $action Descripción de la acción realizada.
     * @param array $details Detalles adicionales relevantes para la operación.
     */
    public static function logOperation($action, $details = []) {
        // Nombre del archivo de log basado en la fecha actual
        $logFile = self::$logDir . 'operaciones_' . date('Y-m-d') . '.log';
        // Formatear el mensaje de log
        $message = self::formatMessage('OPERACION', $action, $details);
        // Escribir el mensaje en el archivo de log
        self::writeLog($logFile, $message);
    }

    /**
     * Registra un error o una excepción.
     *
     * @param string $error Descripción del error.
     * @param array $context Contexto o detalles adicionales sobre el error.
     */
    public static function logError($error, $context = []) {
        // Nombre del archivo de log de errores basado en la fecha actual
        $logFile = self::$logDir . 'errores_' . date('Y-m-d') . '.log';
        // Formatear el mensaje de log de error
        $message = self::formatMessage('ERROR', $error, $context);
        // Escribir el mensaje en el archivo de log
        self::writeLog($logFile, $message);
    }

    /**
     * Formatea el mensaje de log con información contextual.
     *
     * @param string $type Tipo de log (OPERACION o ERROR).
     * @param string $message El mensaje principal.
     * @param array $context El contexto o detalles adicionales.
     * @return string El mensaje de log formateado.
     */
    private static function formatMessage($type, $message, $context) {
        // Convertir el contexto a una cadena JSON (con opciones para mejor legibilidad y manejo de caracteres)
        $contextStr = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        // Si la codificación JSON falla (ej. por datos binarios), usar print_r
        if ($contextStr === false) {
            $contextStr = print_r($context, true);
        }


        // Obtener información del usuario y la IP
        $usuario = $_SESSION['usuario'] ?? 'SIN-USUARIO'; // Asume que el nombre de usuario está en $_SESSION['usuario']
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A'; // Obtiene la IP del cliente

        // Formato final del mensaje de log
        return sprintf(
            "[%s] [%s] Usuario: %s IP: %s Mensaje: %s Contexto: %s",
            date('Y-m-d H:i:s'), // Fecha y hora
            $type,               // Tipo de log (OPERACION/ERROR)
            $usuario,            // Nombre del usuario o 'SIN-USUARIO'
            $ip,                 // Dirección IP del cliente
            $message,            // Mensaje principal
            $contextStr          // Detalles adicionales en formato JSON o print_r
        );
    }

    /**
     * Escribe el mensaje formateado en el archivo de log especificado.
     *
     * @param string $file La ruta completa al archivo de log.
     * @param string $message El mensaje a escribir.
     */
    private static function writeLog($file, $message) {
        // Verificar si el directorio de log existe, si no, intentar crearlo
        if (!file_exists(self::$logDir)) {
            // Intentar crear el directorio de forma recursiva y con permisos adecuados
            if (!mkdir(self::$logDir, 0775, true)) {
                // Si falla la creación del directorio, loggearlo en el log de errores del sistema (si es posible)
                error_log("Error: No se pudo crear el directorio de log: " . self::$logDir);
                return; // No se puede escribir el log si el directorio no existe
            }
        }

        // Escribir el mensaje en el archivo, añadiendo al final (FILE_APPEND)
        // Usar LOCK_EX para evitar que otros procesos escriban al mismo tiempo
        if (file_put_contents($file, $message . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
             // Si falla la escritura del archivo de log, loggearlo en el log de errores del sistema
            error_log("Error: No se pudo escribir en el archivo de log: " . $file);
        }
    }
}
?>
