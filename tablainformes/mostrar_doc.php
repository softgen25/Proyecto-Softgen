<?php
// Script para cargar y mostrar los documentos en la tabla (incluido en tablainformes.php)

// Incluir archivo de configuración
// Se asume que config.php ya fue incluido en el archivo padre (tablainformes.php)
// require_once __DIR__ . '/config.php';
// Incluir archivo del logger (opcional, para registrar errores de BD si ocurren aquí)
// Se asume que logger.php ya fue incluido en el archivo padre (tablainformes.php)
// require_once __DIR__ . '/logger.php';


// Establecer la conexión a la base de datos utilizando las constantes de config.php
// Se asume que las constantes DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME están definidas
$conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    // Registrar el error de conexión
    // Logger::logError('Error de conexión a la base de datos al mostrar documentos', ['db_error' => $conn->connect_error]);
    // Mostrar un mensaje de error en la tabla
    die("<tr><td colspan='5' class='text-danger text-center'>Error de conexión a la base de datos al cargar documentos.</td></tr>");
}

// Consulta SQL para obtener los documentos
// Selecciona los campos necesarios de la tabla 'documentos'
// Ordena los resultados por fecha de subida en orden descendente (los más recientes primero)
$sql = "SELECT id, nombre, ruta_archivo, fecha_subida FROM documentos ORDER BY fecha_subida DESC";
$result = $conn->query($sql);

// Verificar si la consulta se ejecutó correctamente y si hay resultados
if ($result) {
    if ($result->num_rows > 0) {
        // Iterar sobre cada fila de resultados
        while($row = $result->fetch_assoc()) {
            // Obtener el nombre del archivo de la ruta completa
            $nombre_archivo = basename($row['ruta_archivo']);
            // Construir la URL completa para descargar/ver el documento
            // Se asume que la constante DOCS_BASE_URL está definida en config.php
            $ruta_descarga = DOCS_BASE_URL . $nombre_archivo;

            // Determinar el icono basado en la extensión del archivo (opcional, si quieres mostrar iconos diferentes)
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            $icono = 'bi-file-earmark'; // Icono genérico por defecto
            switch ($extension) {
                case 'pdf':
                    $icono = 'bi-filetype-pdf';
                    break;
                case 'docx':
                    $icono = 'bi-filetype-docx';
                    break;
                case 'xlsx':
                    $icono = 'bi-filetype-xlsx';
                    break;
                // Añadir más casos si manejas otros tipos de archivos
            }

            // Mostrar una fila en la tabla HTML por cada documento
            ?>
            <tr>
                <th scope="row"><?= htmlspecialchars($row['id']) ?></th> <td><?= htmlspecialchars($row['nombre']) ?></td> <td>
                    <a href="#"
                    onclick="abrirEditor(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nombre']) ?>', '<?= htmlspecialchars($ruta_descarga) ?>'); return false;"
                    class="text-decoration-none">
                    <i class="bi <?= $icono ?> me-1"></i> Ver/Editar
                    </a>
                </td>
                <td><?= htmlspecialchars($row['fecha_subida']) ?></td> <td>
                    <a href="eliminardocs.php?id=<?= $row['id'] ?>&ruta=<?= urlencode($nombre_archivo) ?>"
                    class="btn btn-sm btn-danger me-2"
                    onclick="return confirm('¿Estás seguro de eliminar este documento?')"
                    title="Eliminar Documento">
                    <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
            <?php
        }
    } else {
        // Si no hay documentos en la base de datos, mostrar un mensaje
        echo "<tr><td colspan='5' class='text-center text-muted'>No hay documentos registrados</td></tr>";
    }
} else {
    // Si hubo un error en la consulta SQL
     // Logger::logError('Error al ejecutar la consulta SQL para cargar documentos', ['db_error' => $conn->error, 'sql' => $sql]);
    echo "<tr><td colspan='5' class='text-danger text-center'>Error al cargar los documentos.</td></tr>";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
