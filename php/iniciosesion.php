<?php
// Este script maneja el inicio de sesión de usuarios.

// --- INICIO: Configuración para Depuración (Eliminar en Producción) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIN: Configuración para Depuración ---


// Iniciar la sesión PHP
session_start();

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
    // En un entorno de producción, no muestres detalles del error de conexión
    die("Conexión fallida: " . $conn->connect_error);
}

// --- Procesar el formulario si se envió por POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanear los datos del formulario
    $rol = htmlspecialchars($_POST['rol'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $contrasena = $_POST['contrasena'] ?? ''; // No sanear la contraseña antes de verificar

    // --- Validación del lado del servidor ---
    // Verificar que los campos obligatorios no estén vacíos
    if (empty($rol) || empty($email) || empty($contrasena)) {
        $error_message = "Por favor, selecciona tu rol e ingresa email y contraseña.";
        // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../iniciosesion.html?error=" . urlencode($error_message));
        exit();
    }

    // --- Buscar al usuario en la base de datos por email y rol ---
    // Usar sentencias preparadas para prevenir inyección SQL
    // Seleccionamos id_usuario en lugar de id
    // Asegúrate de que la tabla 'usuarios' y las columnas 'email', 'rol', 'id_usuario', 'nombre', 'contrasena' existan.
    $stmt = $conn->prepare("SELECT id_usuario, nombre, contrasena FROM usuarios WHERE email = ? AND rol = ?");
    $stmt->bind_param("ss", $email, $rol);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si se encontró un usuario con ese email y rol
    if ($stmt->num_rows == 1) {
        // Vincular los resultados de la consulta a variables
        // Vinculamos a $user_id para que coincida con id_usuario
        $stmt->bind_result($user_id, $nombre_usuario, $hashed_password);
        $stmt->fetch();

        // --- Verificar la contraseña hasheada ---
        if (password_verify($contrasena, $hashed_password)) {
            // Contraseña correcta, iniciar sesión
            // Regenerar el ID de sesión para prevenir ataques de fijación de sesión
            session_regenerate_id(true);

            // Almacenar información del usuario en la sesión
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user_id; // Usamos $user_id que viene de id_usuario
            $_SESSION['user_nombre'] = $nombre_usuario;
            $_SESSION['user_rol'] = $rol;

            // Redirigir al usuario a la página de inicio o a un dashboard según su rol
            // Puedes personalizar las redirecciones aquí
            switch ($rol) {
                case 'administrador':
                    header("Location: ../administrador.html"); // Ejemplo de dashboard de admin
                    break;
                case 'tecnico':
                    header("Location: ../creacion.html"); // Ejemplo de dashboard de técnico
                    break;
                case 'cliente':
                    header("Location: ../visualizacion.html"); // Ejemplo de dashboard de cliente
                    break;
                default:
                    // Si el rol no coincide con ninguno esperado, redirigir a una página por defecto
                    header("Location: ../index.html");
            }
            $stmt->close();
            $conn->close();
            exit();

        } else {
            // Contraseña incorrecta
            $error_message = "Email, rol o contraseña incorrectos."; // Mensaje genérico por seguridad
            $stmt->close();
            $conn->close();
             // Asegúrate de que no haya salida antes de esta redirección.
            header("Location: ../iniciosesion.html?error=" . urlencode($error_message));
            exit();
        }
    } else {
        // No se encontró usuario con ese email y rol
        $error_message = "Email, rol o contraseña incorrectos."; // Mensaje genérico por seguridad
        $stmt->close();
        $conn->close();
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../iniciosesion.html?error=" . urlencode($error_message));
        exit();
    }

} else {
    // Si la solicitud no fue POST, redirigir a la página de inicio de sesión
     // Asegúrate de que no haya salida antes de esta redirección.
    header("Location: ../iniciosesion.html");
    exit();
}

// Cerrar la conexión si aún está abierta
$conn->close();
?>
