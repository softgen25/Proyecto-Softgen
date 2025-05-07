<?php
// Este script maneja el restablecimiento de contraseña después de que el usuario sigue el enlace del correo.

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
    // En un entorno de producción, no muestres detalles del error de conexión
    die("Conexión fallida: " . $conn->connect_error);
}

// --- Procesar el formulario si se envió por POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanear los datos del formulario
    $token = htmlspecialchars($_POST['token'] ?? '');
    $reset_code = htmlspecialchars($_POST['reset_code'] ?? '');
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? ''; // No sanear antes de hashear
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? ''; // No sanear antes de comparar


    // --- Validación del lado del servidor ---
    // Verificar que los campos obligatorios no estén vacíos
    if (empty($token) || empty($reset_code) || empty($nueva_contrasena) || empty($confirmar_contrasena)) {
        $error_message = "Por favor, completa todos los campos.";
        // Agregamos el token de vuelta a la URL si falta algún otro campo, para no perderlo
        $redirect_url = "../recuperar_contraseña.html?error=" . urlencode($error_message);
        if (!empty($token)) {
            $redirect_url .= "&token=" . urlencode($token);
        }
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: " . $redirect_url);
        exit();
    }

    // Verificar que las nuevas contraseñas coincidan
    if ($nueva_contrasena !== $confirmar_contrasena) {
        $error_message = "Las nuevas contraseñas no coinciden.";
        // Agregamos el token de vuelta a la URL
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../recuperar_contraseña.html?token=" . urlencode($token) . "&error=" . urlencode($error_message));
        exit();
    }

    // --- Verificar el token y el código de restablecimiento en la base de datos ---
    // Asumiendo una tabla 'password_resets' con columnas 'email', 'token', 'codigo', 'creado_en'
    // y que el token expira después de un tiempo (ej. 1 hora)
    $now = date("Y-m-d H:i:s");
    // Calcula la hora límite de expiración (hace 1 hora)
    // Asegúrate de que la tabla 'password_resets' y las columnas 'token', 'codigo', 'creado_en', 'email' existan.
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND codigo = ? AND creado_en >= DATE_SUB(?, INTERVAL 1 HOUR)");
    // O usando la variable de expiración calculada:
    // $expiration_limit = date("Y-m-d H:i:s", strtotime("-1 hour"));
    // $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND codigo = ? AND creado_en >= ?");
    $stmt->bind_param("sss", $token, $reset_code, $now); // Usamos $now para DATE_SUB

    $stmt->execute();
    $stmt->store_result();

    // Si $stmt->num_rows es 1, significa que encontró una entrada válida y no expirada.
    if ($stmt->num_rows == 1) {
        // Token y código válidos y no expirados
        $stmt->bind_result($user_email);
        $stmt->fetch();
        $stmt->close();

        // --- Hashear la nueva contraseña ---
        $nueva_contrasena_hashed = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

        // --- Actualizar la contraseña del usuario ---
        // Asegúrate de que la tabla 'usuarios' tenga las columnas 'contrasena' y 'email'.
        $update_stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $nueva_contrasena_hashed, $user_email);

        if ($update_stmt->execute()) {
            // Contraseña actualizada exitosamente

            // --- Invalidar el token de restablecimiento usado ---
            // Elimina la entrada de la tabla password_resets para que el token no pueda ser reutilizado.
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delete_stmt->bind_param("s", $token);
            $delete_stmt->execute();

            $update_stmt->close();
            $delete_stmt->close();
            $conn->close();

            // Redirigir a la página de inicio de sesión con un mensaje de éxito
             // Asegúrate de que no haya salida antes de esta redirección.
            header("Location: ../iniciosesion.html?exito=" . urlencode("Tu contraseña ha sido restablecida exitosamente. Ahora puedes iniciar sesión."));
            exit();

        } else {
            // Error al actualizar la contraseña
            error_log("Error al actualizar la contraseña para " . $user_email . ": " . $conn->error);
            $error_message = "Error al actualizar la contraseña. Inténtalo de nuevo.";
            $update_stmt->close();
            $conn->close();
             // Asegúrate de que no haya salida antes de esta redirección.
            header("Location: ../recuperar_contraseña.html?token=" . urlencode($token) . "&error=" . urlencode($error_message));
            exit();
        }

    } else {
        // No encontró una entrada válida (token/código incorrecto, expirado o ya usado)
        $error_message = "El código de verificación o el token son inválidos o han expirado. Solicita un nuevo restablecimiento.";
        $stmt->close();
        $conn->close();
        // Redirigir sin el token en la URL si el token fue el problema principal
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../recuperar_contraseña.html?error=" . urlencode($error_message));
        exit();
    }

} else {
    // Si la solicitud no fue POST, redirigir a la página de restablecimiento
     // Asegúrate de que no haya salida antes de esta redirección.
    header("Location: ../recuperar_contraseña.html");
    exit();
}

// Cerrar la conexión si aún está abierta
$conn->close();
?>
