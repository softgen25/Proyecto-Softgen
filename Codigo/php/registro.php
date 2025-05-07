<?php
// Este script maneja el registro de nuevos usuarios.

// --- INICIO: Configuración para Depuración (Eliminar en Producción) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIN: Configuración para Depuración ---


// Incluir archivo de configuración de base de datos (si tienes uno separado)
// require_once 'db_config.php';

// --- Configuración de la Base de Datos ---
// Reemplaza estos valores con tus credenciales de base de datos
$servername = "localhost";
$username = "root"; // Tu nombre de usuario de la base de datos
$password = "98042662429"; // Tu contraseña de la base de datos
$dbname = "db_softgen"; // El nombre de tu base de datos (según tu SQL)


// --- Conexión a la Base de Datos ---
// Verifica que las credenciales y el servidor MySQL sean correctos y accesibles.
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    // En un entorno de producción, no muestres detalles del error de conexión directamente al usuario
    die("Conexión fallida: " . $conn->connect_error);
}

// --- Procesar el formulario si se envió por POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanear los datos del formulario
    // Usar htmlspecialchars para prevenir XSS al mostrar mensajes de error
    $nombre = htmlspecialchars($_POST['nombre'] ?? '');
    $apellido = htmlspecialchars($_POST['apellido'] ?? '');
    $telefono = htmlspecialchars($_POST['telefono'] ?? ''); // Teléfono no es obligatorio en el HTML, pero lo recogemos
    $email = htmlspecialchars($_POST['email'] ?? '');
    $contrasena = $_POST['contrasena'] ?? ''; // No sanear la contraseña antes de hashear
    $confirmar_contrasena = $_POST['confirmarcontrasena'] ?? ''; // No sanear antes de comparar

    // --- Validación del lado del servidor ---
    // Verificar que los campos obligatorios no estén vacíos
    if (empty($nombre) || empty($apellido) || empty($email) || empty($contrasena) || empty($confirmar_contrasena)) {
        $error_message = "Por favor, completa todos los campos obligatorios.";
        // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../registro.html?error=" . urlencode($error_message));
        exit();
    }

    // Verificar que las contraseñas coincidan
    if ($contrasena !== $confirmar_contrasena) {
        $error_message = "Las contraseñas no coinciden.";
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../registro.html?error=" . urlencode($error_message));
        exit();
    }

    // Validar formato de email (validación básica)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "El formato del email no es válido.";
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../registro.html?error=" . urlencode($error_message));
        exit();
    }

    // --- Verificar si el email ya existe en la base de datos ---
    // Usar sentencias preparadas para prevenir inyección SQL
    // Usamos id_usuario en la consulta SELECT para verificar si existe
    // Asegúrate de que la tabla 'usuarios' y la columna 'email' existan.
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // El email ya está registrado
        $error_message = "Este email ya está registrado. Intenta iniciar sesión o usa otro email.";
        $stmt->close();
        $conn->close();
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../registro.html?error=" . urlencode($error_message));
        exit();
    }
    $stmt->close();

    // --- Hashear la contraseña antes de guardarla ---
    // PASSWORD_DEFAULT utiliza el algoritmo de hashing más fuerte disponible (actualmente bcrypt)
    $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);

    // --- Insertar el nuevo usuario en la base de datos ---
    // Asignar un rol por defecto para los nuevos registros (ej. 'cliente')
    // Asegúrate de que la tabla 'usuarios' tenga las columnas 'rol', 'nombre', 'apellido', 'telefono', 'email', 'contrasena'.
    $rol = 'cliente';
    // Nota: No insertamos en la columna 'confirmar_contrasena'
    $stmt = $conn->prepare("INSERT INTO usuarios (rol, nombre, apellido, telefono, email, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
    // 'ssssss' indica que todos los parámetros son strings
    $stmt->bind_param("ssssss", $rol, $nombre, $apellido, $telefono, $email, $contrasena_hashed);

    if ($stmt->execute()) {
        // Registro exitoso
        $stmt->close();
        $conn->close();
        // Redirigir a la página de inicio de sesión con un mensaje de éxito
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../iniciosesion.html?registro=exitoso");
        exit();
    } else {
        // Error al insertar en la base de datos
        $error_message = "Error al registrar el usuario. Inténtalo de nuevo más tarde.";
        // En desarrollo, puedes mostrar el error de la base de datos: $conn->error
        $stmt->close();
        $conn->close();
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../registro.html?error=" . urlencode($error_message));
        exit();
    }

} else {
    // Si la solicitud no fue POST, redirigir a la página de registro
     // Asegúrate de que no haya salida antes de esta redirección.
    header("Location: ../registro.html");
    exit();
}

// Cerrar la conexión si aún está abierta (aunque los exit() la cierran en caso de éxito/error)
$conn->close();
?>
