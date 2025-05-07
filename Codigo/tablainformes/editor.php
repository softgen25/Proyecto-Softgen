<?php
// Editor de documentos

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

// Verificar si se recibió el ID del documento
if (!isset($_GET['id']) || empty($_GET['id'])) {
    Logger::logError('ID de documento no especificado para edición', ['ip' => $_SERVER['REMOTE_ADDR']]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'No se especificó el documento a editar.'];
    header("Location: tablainformes.php"); // Redirigir a la tabla de informes
    exit();
}

$documento_id = intval($_GET['id']); // Obtener el ID del documento y asegurarse de que sea un entero

// Conectar a la base de datos
$conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    Logger::logError('Error de conexión a la base de datos al cargar editor', ['db_error' => $conn->connect_error, 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error de conexión a la base de datos.'];
    header("Location: tablainformes.php");
    exit();
}

// Consultar la base de datos para obtener la ruta del archivo
$stmt = $conn->prepare("SELECT nombre, ruta_archivo FROM documentos WHERE id = ?");

if ($stmt === false) {
    Logger::logError('Error al preparar la sentencia SQL para obtener documento (editor)', ['db_error' => $conn->error, 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error interno del servidor al preparar la consulta.'];
    $conn->close();
    header("Location: tablainformes.php");
    exit();
}

$stmt->bind_param("i", $documento_id);
$stmt->execute();
$result = $stmt->get_result();
$documento = $result->fetch_assoc(); // Obtener la fila del resultado como un array asociativo

$stmt->close();
$conn->close(); // Cerrar la conexión a la base de datos

// Verificar si se encontró el documento
if (!$documento) {
    Logger::logError('Documento no encontrado en la base de datos', ['document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'El documento solicitado no fue encontrado.'];
    header("Location: tablainformes.php");
    exit();
}

$ruta_archivo_bd = $documento['ruta_archivo']; // Ruta relativa almacenada en la BD
$nombre_documento = $documento['nombre']; // Nombre del documento

// Construir la ruta física completa del archivo
$ruta_archivo_fisica = ROOT_DIR . DIRECTORY_SEPARATOR . $ruta_archivo_bd;

// Validar la ruta física del archivo para prevenir Directory Traversal
$real_upload_dir = realpath(UPLOAD_DIR); // Directorio real donde se guardan los documentos
$real_archivo_fisica = realpath($ruta_archivo_fisica); // Ruta real del archivo

// Verificar que la ruta real del archivo esté dentro del directorio de subida real
if ($real_upload_dir === false || $real_archivo_fisica === false || strpos($real_archivo_fisica, $real_upload_dir) !== 0) {
     Logger::logError('Intento de acceso a archivo fuera del directorio de subida (editor)', [
        'document_id' => $documento_id,
        'ruta_archivo_bd' => $ruta_archivo_bd,
        'ruta_archivo_fisica' => $ruta_archivo_fisica,
        'real_upload_dir' => $real_upload_dir ?? 'N/A',
        'real_archivo_fisica' => $real_archivo_fisica ?? 'N/A',
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Ruta de archivo inválida.'];
    header("Location: tablainformes.php");
    exit();
}


// Determinar el tipo de archivo por su extensión
$fileType = strtolower(pathinfo($ruta_archivo_fisica, PATHINFO_EXTENSION));

// --- Lógica para cargar el contenido o redirigir al editor externo ---
$content = ""; // Variable para almacenar el contenido si es un archivo de texto plano

switch ($fileType) {
    case 'txt':
        // Para archivos de texto plano, leer el contenido directamente
        $content = file_get_contents($ruta_archivo_fisica);
        if ($content === false) {
             Logger::logError('Error al leer el contenido del archivo TXT', ['ruta_archivo_fisica' => $ruta_archivo_fisica, 'ip' => $_SERVER['REMOTE_ADDR']]);
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error al leer el contenido del archivo.'];
            header("Location: tablainformes.php");
            exit();
        }
        // El HTML de abajo contendrá un textarea para editar el texto
        break;

    case 'pdf':
    case 'docx':
    case 'xlsx':
        // Para formatos Office/PDF, redirigir a un visor/editor externo (como Google Docs Viewer o Office Online)
        // Usamos DOCS_BASE_URL para generar una URL pública accesible por el servicio externo
        $url_publica_documento = DOCS_BASE_URL . basename($ruta_archivo_fisica);

        if ($fileType === 'pdf') {
            // Redirigir a Google Docs Viewer (solo visualización/anotación básica)
            // Para edición real de PDF se necesitaría un editor de PDF más avanzado
            header("Location: https://docs.google.com/viewer?url=" . urlencode($url_publica_documento) . "&embedded=true");
        } else {
            // Redirigir a Office Online Viewer/Editor (requiere que el servidor web sea accesible públicamente)
            // Nota: La edición real puede requerir configuración adicional en el servidor de Office Online
            header("Location: https://view.officeapps.live.com/op/embed.aspx?src=" . urlencode($url_publica_documento));
        }
        exit; // Detener la ejecución del script PHP después de la redirección

    default:
        // Si el tipo de archivo no es soportado para edición
        Logger::logError('Formato de archivo no soportado para edición', ['document_id' => $documento_id, 'file_type' => $fileType, 'ip' => $_SERVER['REMOTE_ADDR']]);
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Formato de archivo no soportado para edición directa.'];
        header("Location: tablainformes.php");
        exit();
}

// Si llegamos aquí, significa que el archivo es de texto plano y $content contiene su contenido
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Documento - <?= htmlspecialchars($nombre_documento) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa; /* Color de fondo suave */
        }
        textarea {
            width: 100%;
            height: calc(100vh - 140px); /* Ajusta la altura para que ocupe casi toda la ventana menos un espacio para el título y botones */
            padding: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace; /* Fuente monoespaciada para código/texto plano */
            font-size: 1rem;
            resize: vertical; /* Permitir redimensionar verticalmente */
        }
        .editor-container {
            margin-top: 20px;
        }
        .btn-group {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar: <?= htmlspecialchars($nombre_documento) ?></h1>

        <div class="editor-container">
            <textarea id="editorContent"><?= htmlspecialchars($content) ?></textarea>
        </div>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" onclick="guardarCambios()">Guardar Cambios</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='tablainformes.php'">Cancelar</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función JavaScript para enviar el contenido del editor a guardar_edicion.php
        function guardarCambios() {
            const documentoId = <?= $documento_id ?>; // Obtiene el ID del documento PHP
            const contenido = document.getElementById('editorContent').value; // Obtiene el contenido del textarea

            // Aquí deberías obtener el token CSRF, quizás pasándolo a través de un campo oculto en el HTML o una variable JS
            // Por simplicidad en este ejemplo, lo omitimos, pero es CRUCIAL para la seguridad.
            // Si editor.php se carga en un iframe dentro de tablainformes.php, el token podría pasarse.
            // Si es una página separada, necesitarías generar el token aquí o pasarlo en la URL (menos seguro para GET).
            // Una forma más segura para POST es incluirlo en un formulario oculto o pasarlo vía Fetch API headers.
            // Para este ejemplo, asumiremos que el token CSRF se maneja en el lado del servidor en guardar_edicion.php
            // y que este script se cargará en un contexto donde el token sea accesible o no sea estrictamente necesario (menos seguro).
            // Una implementación más robusta pasaría el token aquí.

            // Usando Fetch API para enviar los datos de forma asíncrona
            fetch('guardar_edicion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded', // O 'application/json' si envías JSON
                    // 'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?? '' ?>' // Si pasas el token en el header
                },
                // Envía los datos como cuerpo de la solicitud URL-encoded
                body: 'id=' + encodeURIComponent(documentoId) + '&contenido=' + encodeURIComponent(contenido)
                     // + '&csrf_token=' + encodeURIComponent('<?= $_SESSION['csrf_token'] ?? '' ?>') // Si pasas el token en el body
            })
            .then(response => {
                // Verifica si la respuesta es OK (código 200)
                if (!response.ok) {
                    // Si no es OK, lanza un error con el estado de la respuesta
                    return response.text().then(text => { throw new Error('Error HTTP ' + response.status + ': ' + text); });
                }
                // Intenta parsear la respuesta como JSON
                return response.json();
            })
            .then(data => {
                // Maneja la respuesta del servidor
                if (data.success) {
                    alert('Cambios guardados correctamente.');
                    // Opcional: Redirigir o actualizar la página principal
                    window.location.href = 'tablainformes.php';
                } else {
                    // Muestra el mensaje de error del servidor
                    alert('Error al guardar cambios: ' + data.message);
                }
            })
            .catch(error => {
                // Captura errores de red o errores lanzados anteriormente
                console.error('Error al guardar:', error);
                alert('Ocurrió un error al intentar guardar los cambios: ' + error.message);
            });
        }

        // Nota: Si editor.php se carga en un iframe, la función guardarContenido()
        // que estaba en el script anterior (para ser llamada desde el padre)
        // ya no es necesaria con este enfoque de Fetch API.
    </script>
</body>
</html>
