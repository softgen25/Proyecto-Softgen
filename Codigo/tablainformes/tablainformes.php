<?php
// Página para mostrar la tabla de informes (documentos)

// Iniciar buffer de salida para evitar errores con headers
ob_start();

// Configuración de sesión segura
if (session_status() === PHP_SESSION_NONE) {
    // Configura los parámetros de la cookie de sesión para mayor seguridad
    session_set_cookie_params([
        'lifetime' => 3600, // Tiempo de vida de la cookie en segundos (ej. 1 hora)
        'path' => '/', // La cookie estará disponible en toda la aplicación
        'domain' => $_SERVER['HTTP_HOST'], // El dominio actual
        'secure' => true, // La cookie solo se enviará a través de conexiones HTTPS
        'httponly' => true, // La cookie solo será accesible a través del protocolo HTTP(S), no por scripts del lado del cliente (JavaScript)
        'samesite' => 'Strict' // Protege contra ataques CSRF (Strict, Lax, None)
    ]);
    session_start();
}

// Incluir archivos necesarios
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

// Verificar autenticación (Implementa tu lógica de autenticación aquí)
// if (!isset($_SESSION['id_usuario'])) {
//     // Si el usuario no está autenticado, redirigir a la página de inicio de sesión
//     header("Location: ../loggin/iniciosesion.html"); // Ajusta la ruta a tu página de login
//     exit();
// }

// Validar token CSRF (se genera en este archivo y se usa en el formulario de subida)
// Si no hay un token en la sesión, genera uno nuevo
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// --- Lógica para mostrar alertas ---
// Muestra mensajes de alerta (éxito o error) almacenados en la sesión
if (isset($_SESSION['alert'])) {
    $alert_type = htmlspecialchars($_SESSION['alert']['type']); // 'success' o 'danger'
    $alert_message = htmlspecialchars($_SESSION['alert']['message']); // El mensaje a mostrar
    ?>
    <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
        <?php echo $alert_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
    // Elimina la alerta de la sesión para que no se muestre de nuevo al recargar
    unset($_SESSION['alert']);
}

// --- Conexión a la base de datos y recuperación de documentos ---
$conn = null; // Inicializa la conexión a null
$documentos = []; // Array para almacenar los documentos recuperados

try {
    // Establece la conexión a la base de datos utilizando las constantes de config.php
    $conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Verifica si la conexión fue exitosa
    if ($conn->connect_error) {
        // Registra el error de conexión
        Logger::logError('Error de conexión a la base de datos al cargar informes', ['db_error' => $conn->connect_error]);
        // En lugar de morir, puedes mostrar un mensaje de error al usuario
        throw new Exception("Error al conectar con la base de datos.");
    }

    // Prepara la consulta SQL para seleccionar los documentos
    // Ordena por fecha de subida descendente (los más recientes primero)
    // Puedes añadir una cláusula WHERE para filtrar por id_usuario si quieres que cada usuario vea solo sus documentos
    $sql = "SELECT id, nombre, ruta_archivo, fecha_subida FROM documentos ORDER BY fecha_subida DESC";

    // Si quieres filtrar por usuario (descomenta y ajusta si tienes autenticación)
        $id_usuario_actual = $_SESSION['id_usuario'] ?? null;
        if ($id_usuario_actual !== null) {
        $sql = "SELECT id, nombre, ruta_archivo, fecha_subida FROM documentos WHERE id_usuario = ? ORDER BY fecha_subida DESC";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            Logger::logError('Error al preparar la consulta de documentos por usuario', ['db_error' => $conn->error]);
            throw new Exception("Error interno al preparar la consulta.");
        }
        $stmt->bind_param("i", $id_usuario_actual);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
   // Si no hay usuario o no se filtra, ejecuta la consulta general
    $result = $conn->query($sql);
    }


    // Verifica si la consulta fue exitosa y si hay resultados
    if ($result) {
        if ($result->num_rows > 0) {
            // Almacena los resultados en un array para usarlos más tarde en el HTML
            while($row = $result->fetch_assoc()) {
                $documentos[] = $row;
            }
        }
         // Libera el conjunto de resultados
        $result->free();
    } else {
         // Si la consulta falló
        Logger::logError('Error al ejecutar la consulta de documentos', ['db_error' => $conn->error, 'sql' => $sql]);
        throw new Exception("Error al recuperar los documentos de la base de datos.");
    }

} catch (Exception $e) {
    // Captura cualquier excepción y muestra un mensaje de error
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error al cargar los documentos: ' . $e->getMessage()];
    // Continúa cargando la página, pero la tabla estará vacía o con el mensaje de error
} finally {
    // Asegura que la conexión a la base de datos se cierre si se abrió y es válida
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

// Limpiar buffer antes de enviar headers y contenido HTML
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla informes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/plantilas.css">
    <link rel="stylesheet" href="css/plantillas.css">

    <link rel="icon" type="image/png" sizes="16x16" href="../img/Logo Favicon 16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/Logo favicon 1.0.png">
    <link rel="icon" type="image/png" sizes="180x180" href="../img/Logo Favicon 180x180.png">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid pt-2 sm">
            <a class="navbar-brand" href="index.html">
                <img src="../img/SOFTGEN 3.0.png" alt="SOFTGEN Logo" width="150">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span><i class="bi bi-grid-fill"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li><a class="nav-link" href="../loggin/index.html">Inicio</a></li>
                    <li><a class="nav-link" href="creacion.html">Creación</a></li>
                    <li><a class="nav-link" href="visualizacion.html">Visualización</a></li>
                    <li><a class="nav-link" href="tablainformes.php">Informes</a></li>
                    <li><a class="nav-link" href="plantillas.html">Plantillas</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Ajustes</a></li>
                            <li><a class="dropdown-item" href="../loggin/iniciosesion.html">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container mt-5 mb-3" id="formulariodocs">
    <h2>Gestión de Documentos</h2>
    <p>Sube, visualiza, edita y elimina tus informes aquí.</p>

    <form action="upload_handler.php" method="POST" enctype="multipart/form-data" class="mb-4 p-4 bg-light rounded">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombreDocumento" class="form-label visually-hidden">Nombre del documento</label>
                <input type="text" id="nombreDocumento" name="nombre" placeholder="Nombre del documento" class="form-control" required>
            </div>
            <div class="col-md-4">
                 <label for="archivoDocumento" class="form-label visually-hidden">Seleccionar archivo</label>
                <input type="file" id="archivoDocumento" name="archivo" accept=".pdf,.docx,.xlsx,.txt" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-upload"></i> Subir
                </button>
            </div>
        </div>
         <small class="form-text text-muted mt-2">Tipos de archivo permitidos: PDF, DOCX, XLSX, TXT. Tamaño máximo: <?= MAX_FILE_SIZE / 1024 / 1024 ?>MB.</small>
    </form>

    <h3>Documentos Subidos</h3>
    <table class="table table-hover mb-4">
        <thead>
            <tr>
                <th style="background-color: #135787; color: white;" scope="col">ID</th>
                <th style="background-color: #135787; color: white;" scope="col">Nombre</th>
                <th style="background-color: #135787; color: white;" scope="col">Fecha de Subida</th>
                <th style="background-color: #135787; color: white;" scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Itera sobre el array de documentos recuperados
            if (!empty($documentos)) {
                foreach($documentos as $row) {
                    // Asegúrate de que la ruta_archivo sea correcta y accesible vía HTTP
                    // Usamos DOCS_BASE_URL definido en config.php
                    $nombre_archivo = basename($row['ruta_archivo']);
                    $ruta_descarga = DOCS_BASE_URL . $nombre_archivo;

                    echo "<tr>";
                    echo "<th scope='row'>".htmlspecialchars($row['id'])."</th>";
                    echo "<td>".htmlspecialchars($row['nombre'])."</td>";
                    echo "<td>".htmlspecialchars($row['fecha_subida'])."</td>"; // Muestra la fecha tal cual de la BD
                    echo "<td>";
                    // Botón/Enlace para ver/descargar
                    echo "<a href='".htmlspecialchars($ruta_descarga)."'
                               target='_blank'
                               class='btn btn-sm btn-info me-2'
                               title='Ver/Descargar Documento'>"; // Añadido título
                    // Determinar icono basado en la extensión (simple)
                    $extension = strtolower(pathinfo($row['ruta_archivo'], PATHINFO_EXTENSION));
                    $icono_archivo = 'bi-file-earmark'; // Icono por defecto
                    switch ($extension) {
                        case 'pdf': $icono_archivo = 'bi-filetype-pdf'; break;
                        case 'docx': $icono_archivo = 'bi-filetype-docx'; break;
                        case 'xlsx': $icono_archivo = 'bi-filetype-xlsx'; break;
                        case 'txt': $icono_archivo = 'bi-filetype-txt'; break;
                    }
                    echo "<i class='bi {$icono_archivo}'></i> Ver/Descargar";
                    echo "</a>";

                    // Botón/Enlace para editar
                    // Este enlace debe apuntar a editor.php pasando el ID del documento
                    echo "<a href='editor.php?id=".htmlspecialchars($row['id'])."'
                                class='btn btn-sm btn-warning me-2'
                              title='Editar Documento'>"; // Añadido título
                    echo "<i class='bi bi-pencil'></i> Editar";
                    echo "</a>";

                    // Botón/Enlace para eliminar
                    // Este enlace debe apuntar a eliminardocs.php pasando el ID y la ruta del archivo
                    // Se incluye la ruta para que eliminardocs.php sepa qué archivo físico borrar
                    echo "<a href='eliminardocs.php?id=".htmlspecialchars($row['id'])."&ruta=".urlencode($row['ruta_archivo'])."'
                            class='btn btn-sm btn-danger'
                            onclick='return confirm(\"¿Estás seguro de eliminar este documento? Esta acción no se puede deshacer.\")'
                              title='Eliminar Documento'>"; // Añadido título y confirmación
                    echo "<i class='bi bi-trash'></i> Eliminar";
                    echo "</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                // Si no hay documentos, muestra un mensaje en la tabla
                echo "<tr><td colspan='4' class='text-center text-muted'>No hay documentos registrados</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script para resaltar el enlace de navegación activo
    document.addEventListener('DOMContentLoaded', function() {
        const currentPagePath = window.location.pathname;
        // Extrae el nombre del archivo, asume 'index.html' o 'tablainformes.php' si es la raíz del submódulo
        const currentPageFilename = currentPagePath.substring(currentPagePath.lastIndexOf('/') + 1) || 'tablainformes.php';

        const navLinks = document.querySelectorAll('.navbar-nav.me-auto .nav-link');

        navLinks.forEach(link => {
            link.classList.remove('active');
            link.removeAttribute('aria-current');

            const linkHref = link.getAttribute('href');
             // Extrae el nombre del archivo del href del enlace
            const linkFilename = linkHref.substring(linkHref.lastIndexOf('/') + 1);

            // Compara el nombre del archivo del enlace con el de la página actual
            if (linkFilename === currentPageFilename) {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            }
             // Caso especial para la raíz del submódulo que debe coincidir con 'tablainformes.php'
            else if (currentPagePath.endsWith('/tablainformes/') && linkFilename === 'tablainformes.php') {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            }
        });
    });

    // La función abrirEditor ahora se maneja en el modal en tablainformes.php si es necesario,
    // o directamente por el enlace a editor.php.
    // La función verDocumento ya no es necesaria si usamos target="_blank" directamente en el enlace.
</script>


<footer class="text-white mt-5" id="piePagina">
    <div class="container py-5">
        <div class="row">

            <div class="col-md-4 mb-4">
                <h5>Enlaces</h5>
                <ul class="list-unstyled">
                    <li><a href="../loggin/index.html" class="text-white text-decoration-none">Inicio</a></li>
                    <li><a href="creacion.html" class="text-white text-decoration-none">Creación</a></li>
                    <li><a href="../cliente/visualizacion.html" class="text-white text-decoration-none">Visualización</a></li>
                    <li><a href="tablainformes.php" class="text-white text-decoration-none">Informes</a></li>
                    <li><a href="plantillas.html" class="text-white text-decoration-none">Plantillas</a></li>
                    <li><a href="soporte.html" class="text-white text-decoration-none">Soporte</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-4">
                <h5> Información de Contacto</h5>
                <ul class="list-unstyled">
                    <li><i class="bi bi-geo-alt"></i> Dirección: Calle 90 #12-35, Bogotá, Colombia</li>
                    <li><i class="bi bi-telephone"></i> Teléfono: +57 312 857 7856</li>
                    <li><i class="bi bi-envelope"></i> Correo: softgen14@gmail.com</li>
                </ul>
            </div>

            <div class="col-md-4 mb-4">
                <h5>Síguenos</h5>
                <ul class="list-unstyled">
                    <li><a href="https://www.facebook.com/profile.php?id=61574843906898" class="text-white text-decoration-none"><i class="bi bi-facebook"></i>Facebook</a></li>
                    <li><a href="https://x.com/Softgen291521" class="text-white text-decoration-none"><i class="bi bi-twitter-x"></i>Twitter</a></li>
                    <li><a href="https://www.instagram.com/softg_en?igsh=amp3dmF3dzdqbWtq" class="text-white text-decoration-none"><i class="bi bi-instagram"></i>Instagram</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="text-center py-3" id="derechos">
        <p class="mb-0">&copy; 2025 SoftGen. Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>
