<?php
// Este script parece ser una versión simplificada para cargar documentos,
// similar a la lógica principal en tablainformes.php.
// Si tablainformes.php ya muestra la tabla, este archivo podría ser redundante
// o usarse para una funcionalidad específica de carga asíncrona.
// Asumiendo que es para cargar datos para una parte específica de la UI
// o una versión más ligera, lo adapto para usar config.php y logger.php.

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

// Establecer la cabecera para responder en HTML (fragmento de tabla)
header('Content-Type: text/html; charset=utf-8');

$conn = null; // Inicializa la conexión a null

try {
    // Conectar a la base de datos
    // Asegúrate de que las constantes de conexión estén definidas en config.php
    $conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        Logger::logError('Error de conexión a la base de datos en cargardocs.php', ['db_error' => $conn->connect_error]);
        // Imprimir una fila de error en la tabla
        echo "<tr><td colspan='4' class='text-danger'>Error de conexión: " . htmlspecialchars($conn->connect_error) . "</td></tr>";
        // Lanzar excepción para ser capturada por el bloque catch
        throw new Exception("Error de conexión a la base de datos.");
    }

    // Consulta SQL para obtener documentos
    // Puedes añadir una cláusula WHERE para filtrar por id_usuario si es necesario
    $sql = "SELECT id, nombre, ruta_archivo, fecha_subida FROM documentos ORDER BY fecha_subida DESC";

    // Si quieres filtrar por usuario (descomenta y ajusta si tienes autenticación)
    // $id_usuario_actual = $_SESSION['id_usuario'] ?? null;
    // if ($id_usuario_actual !== null) {
    //     $sql = "SELECT id, nombre, ruta_archivo, fecha_subida FROM documentos WHERE id_usuario = ? ORDER BY fecha_subida DESC";
    //     $stmt = $conn->prepare($sql);
    //     if ($stmt === false) {
    //          Logger::logError('Error al preparar la consulta de documentos por usuario (cargardocs)', ['db_error' => $conn->error]);
    //          throw new Exception("Error interno al preparar la consulta.");
    //     }
    //     $stmt->bind_param("i", $id_usuario_actual);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $stmt->close();
    // } else {
         // Si no hay usuario o no se filtra, ejecuta la consulta general
        $result = $conn->query($sql);
    // }


    // Verificar si la consulta fue exitosa y si hay resultados
    if ($result) {
        if ($result->num_rows > 0) {
            // Iterar sobre los resultados y generar las filas de la tabla HTML
            while($row = $result->fetch_assoc()) {
                // Asegúrate de que la ruta_archivo sea correcta y accesible vía HTTP
                // Usamos DOCS_BASE_URL definido en config.php
                $nombre_archivo = basename($row['ruta_archivo']);
                $ruta_descarga = DOCS_BASE_URL . $nombre_archivo;

                echo "<tr>";
                echo "<th scope='row'>" . htmlspecialchars($row['id']) . "</th>";
                echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td>";
                // Enlace para ver/descargar
                echo "<a href='" . htmlspecialchars($ruta_descarga) . "' target='_blank' class='text-decoration-none'>";
                // Determinar icono basado en la extensión (simple)
                $extension = strtolower(pathinfo($row['ruta_archivo'], PATHINFO_EXTENSION));
                $icono_archivo = 'bi-file-earmark'; // Icono por defecto
                switch ($extension) {
                    case 'pdf': $icono_archivo = 'bi-filetype-pdf'; break;
                    case 'docx': $icono_archivo = 'bi-filetype-docx'; break;
                    case 'xlsx': $icono_archivo = 'bi-filetype-xlsx'; break;
                    case 'txt': $icono_archivo = 'bi-filetype-txt'; break;
                }
                echo "<i class='bi {$icono_archivo}'></i> Descargar";
                echo "</a>";
                echo "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_subida']) . "</td>";
                // Nota: Las acciones de Editar y Eliminar no estaban en el cargardocs.php original,
                // pero si este script se usa para llenar la tabla principal, deberías añadirlas aquí también.
                // echo "<td>... Acciones (Editar/Eliminar) ...</td>";
                echo "</tr>";
            }
        } else {
            // Si no hay documentos, muestra un mensaje en la tabla
            echo "<tr><td colspan='4' class='text-center text-muted'>No hay documentos registrados</td></tr>";
        }
         // Libera el conjunto de resultados
        $result->free();
    } else {
         // Si la consulta falló (y no se lanzó una excepción antes)
         Logger::logError('Error al ejecutar la consulta de documentos (cargardocs)', ['db_error' => $conn->error, 'sql' => $sql]);
         echo "<tr><td colspan='4' class='text-danger'>Error al recuperar los documentos.</td></tr>";
    }


} catch (Exception $e) {
    // La excepción ya fue loggeada y se mostró un mensaje en la tabla.
    // No se necesita hacer nada más aquí.
} finally {
    // Asegura que la conexión a la base de datos se cierre si se abrió y es válida
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
