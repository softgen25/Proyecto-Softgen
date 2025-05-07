<?php
// Este script maneja la solicitud inicial de restablecimiento de contraseña.
// Recibe el email del usuario, verifica si existe, genera un token y código,
// los guarda en la base de datos y envía un correo electrónico usando PHPMailer.

// --- INICIO: Configuración para Depuración (Eliminar en Producción) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIN: Configuración para Depuración ---

// Incluir el autoloader de Composer (si usaste Composer)
// Ajusta la ruta si tu carpeta 'vendor' no está en el directorio padre de 'php'
// Asegúrate de que 'vendor/autoload.php' exista y la ruta sea correcta.
require '../vendor/autoload.php';

// Incluir las clases de PHPMailer en el espacio de nombres global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// --- Configuración de la Base de Datos ---
// Reemplaza estos valores con tus credenciales de base de datos de XAMPP
$servername = "localhost";
$username = "root"; // Usuario común en XAMPP
$password = "98042662429";     // Contraseña común en XAMPP (a menudo vacía)
$dbname = "db_softgen"; // El nombre de tu base de datos

// --- Configuración de la URL de Restablecimiento ---
// Reemplaza con la URL base de tu aplicación LOCAL en XAMPP
// ESTA URL es la que va en el CORREO ELECTRÓNICO, apuntando a la página HTML que recibe el token.
$reset_page_url = "http://localhost/recuperar_contraseña.html"; // CORREGIDO: Apunta a la página HTML, no al script PHP

// --- Configuración del Servidor SMTP para PHPMailer ---
// ####################################################################
// ### DEBES REEMPLAZAR ESTOS VALORES CON LOS DETALLES DE TU SERVIDOR SMTP ###
// ####################################################################
// Verifica estos datos con tu proveedor de correo (Gmail, Outlook, Mailtrap, etc.)
$smtp_host = 'live.smtp.mailtrap.io'; // EJEMPLO: 'smtp.gmail.com' para Gmail, o los de Mailtrap
$smtp_username = 'smtp@mailtrap.io'; // EJEMPLO: Tu dirección de correo SMTP (ej. tu cuenta de Gmail o Mailtrap)
$smtp_password = '07bc6499d1ad98b0f224f7091a734303'; // EJEMPLO: Tu contraseña del correo SMTP o contraseña de aplicación (si usas Gmail con 2FA)
$smtp_port = 587; // EJEMPLO: Puerto SMTP (587 para TLS, 465 para SSL)
$smtp_encryption = PHPMailer::ENCRYPTION_STARTTLS; // EJEMPLO: Cifrado (PHPMailer::ENCRYPTION_STARTTLS para TLS, PHPMailer::ENCRYPTION_SMTPS para SSL)
// ####################################################################
// ### FIN DE LA SECCIÓN DE CONFIGURACIÓN SMTP ###
// ####################################################################


// --- Configuración del Remitente del Correo ---
$from_email = $smtp_username; // Idealmente, usa el mismo email que el usuario SMTP
$from_name = "Equipo de SoftGen"; // Nombre del remitente

// --- Conexión a la Base de Datos ---
// Verifica que las credenciales y el servidor MySQL sean correctos y accesibles.
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    error_log("Error de conexión a la base de datos: " . $conn->connect_error);
    $error_message = "Hubo un problema técnico. Inténtalo de nuevo más tarde.";
     // Asegúrate de que no haya salida antes de esta redirección.
    header("Location: ../olvide_contrasena.html?error=" . urlencode($error_message));
    exit();
}

// --- Procesar el formulario si se envió por POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email'] ?? '');

    // --- Validación del lado del servidor ---
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Por favor, ingresa una dirección de correo electrónico válida.";
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../olvide_contrasena.html?error=" . urlencode($error_message));
        exit();
    }

    // --- Verificar si el email existe en la base de datos de usuarios ---
    // Asegúrate de que la tabla 'usuarios' y las columnas 'email', 'id_usuario', 'nombre' existan.
    $stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $stmt->close();
        $conn->close();
        // Mensaje genérico por seguridad (no revelamos si el email existe o no)
        $success_message = "Si el email está registrado, te hemos enviado un correo con instrucciones para restablecer tu contraseña.";
        // Mantenemos la redirección a olvide_contrasena.html en este caso (email no encontrado)
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../olvide_contrasena.html?exito=" . urlencode($success_message));
        exit();
    }

    $stmt->bind_result($user_id, $user_name);
    $stmt->fetch();
    $stmt->close();

    // --- Si el email existe, generar token y código y guardarlos ---
    $token = bin2hex(random_bytes(32));
    try {
        $codigo = random_int(100000, 999999);
    } catch (Exception $e) {
        // Fallback para versiones de PHP sin random_int
        $codigo = mt_rand(100000, 999999);
    }
    $creado_en = date("Y-m-d H:i:s");

    // --- Guardar/Actualizar el token y código en la tabla password_resets ---
    // Asegúrate de que la tabla 'password_resets' y las columnas 'email', 'token', 'codigo', 'creado_en' existan.
    // REPLACE INTO inserta si no existe, o actualiza si ya existe una fila con la misma clave primaria/única (en este caso, 'email').
    $stmt = $conn->prepare("REPLACE INTO password_resets (email, token, codigo, creado_en) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $token, $codigo, $creado_en);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close(); // Cerrar conexión a DB antes de enviar correo (opcional pero a veces recomendado)

        // --- Preparar y Enviar correo electrónico al usuario usando PHPMailer ---
        $mail = new PHPMailer(true); // Pasar 'true' habilita excepciones

        try {
            // Configuración del servidor SMTP
            // Descomenta la siguiente línea para ver detalles de depuración (útil para diagnosticar problemas SMTP)
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Habilitar salida de depuración detallada
            $mail->isSMTP(); // Enviar usando SMTP
            $mail->Host = $smtp_host; // Configurar el servidor SMTP principal
            $mail->SMTPAuth = true; // Habilitar autenticación SMTP
            $mail->Username = $smtp_username; // Nombre de usuario SMTP
            $mail->Password = $smtp_password; // Contraseña SMTP
            $mail->SMTPSecure = $smtp_encryption; // Habilitar cifrado TLS o SSL
            $mail->Port = $smtp_port; // Puerto TCP para conectarse

            // Remitente y destinatarios
            $mail->setFrom($from_email, $from_name); // De quién es el correo
            $mail->addAddress($email, htmlspecialchars($user_name)); // Agregar un destinatario (el email del usuario)

            // Contenido del correo
            $mail->isHTML(true); // Establecer formato de correo a HTML
            $mail->Subject = "Restablecer tu contraseña de SoftGen";

            // Construir el enlace de restablecimiento con el URL y el token
            // ESTE ENLACE APUNTA A LA PÁGINA HTML recuperar_contraseña.html
            $reset_link = $reset_page_url . "?token=" . urlencode($token);

            $mail->Body = "<html><body>";
            $mail->Body .= "<p>Hola " . htmlspecialchars($user_name) . ",</p>";
            $mail->Body .= "<p>Has solicitado restablecer la contraseña de tu cuenta en SoftGen.</p>";
            $mail->Body .= "<p>Tu código de verificación es: <strong>" . htmlspecialchars($codigo) . "</strong></p>";
            $mail->Body .= "<p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>";
            $mail->Body .= "<p><a href='" . htmlspecialchars($reset_link) . "'>" . htmlspecialchars($reset_link) . "</a></p>";
            $mail->Body .= "<p>Este enlace y código expirarán en 1 hora.</p>";
            $mail->Body .= "<p>Si no solicitaste un restablecimiento de contraseña, por favor ignora este correo.</p>";
            $mail->Body .= "<p>Gracias,<br>El equipo de SoftGen</p>";
            $mail->Body .= "</body></html>";

            // Contenido alternativo en texto plano
            $mail->AltBody = "Hola " . $user_name . ",\n\nHas solicitado restablecer la contraseña de tu cuenta en SoftGen.\n";
            $mail->AltBody .= "Tu código de verificación es: " . $codigo . "\n";
            $mail->AltBody .= "Haz clic en el siguiente enlace para restablecer tu contraseña:\n";
            $mail->AltBody .= $reset_link . "\n\n";
            $mail->AltBody .= "Este enlace y código expirarán en 1 hora.\n\n";
            $mail->AltBody .= "Si no solicitaste un restablecimiento de contraseña, por favor ignora este correo.\n\n";
            $mail->AltBody .= "Gracias,\nEl equipo de SoftGen";


            $mail->send(); // Enviar el correo

            // Si el correo se envía sin excepciones
            $success_message_redirect = "Si el email está registrado, te hemos enviado un correo con instrucciones para restablecer tu contraseña. Revisa tu bandeja de entrada.";
            // ##############################################################
            // ### CAMBIO AQUÍ: Redirigir a recuperar_contraseña.html ###
            // ##############################################################
             // Asegúrate de que no haya salida antes de esta redirección.
            header("Location: ../recuperar_contraseña.html?exito=" . urlencode($success_message_redirect));
            exit();

        } catch (Exception $e) {
            // Capturar errores de PHPMailer
            error_log("Error al enviar correo de restablecimiento a " . $email . ": " . $mail->ErrorInfo);
            // Aún así, redirigimos con un mensaje genérico por seguridad,
            // pero en este caso, como el email sí existe, redirigimos a la página de restablecimiento
            // para que el usuario espere el correo o vea el mensaje de éxito genérico allí.
             $success_message_redirect = "Si el email está registrado, te hemos enviado un correo con instrucciones para restablecer tu contraseña. Revisa tu bandeja de entrada. (Hubo un problema técnico con el envío del correo, pero el token se generó)"; // Mensaje más específico para depuración si depuras logs
             // ##############################################################
             // ### CAMBIO AQUÍ: Redirigir a recuperar_contraseña.html ###
             // ##############################################################
              // Asegúrate de que no haya salida antes de esta redirección.
            header("Location: ../recuperar_contraseña.html?exito=" . urlencode($success_message_redirect));
            exit();
        }

    } else {
        // Error al guardar el token/código en la base de datos
        error_log("Error al guardar token de restablecimiento para " . $email . ": " . $conn->error);
        $error_message = "Hubo un problema al generar el enlace de restablecimiento. Inténtalo de nuevo.";
        $conn->close();
        // Mantenemos la redirección a olvide_contrasena.html en caso de error de DB
         // Asegúrate de que no haya salida antes de esta redirección.
        header("Location: ../olvide_contrasena.html?error=" . urlencode($error_message));
        exit();
    }

} else {
    // Si la solicitud no fue POST, redirigir a la página del formulario
     // Asegúrate de que no haya salida antes de esta redirección.
    header("Location: ../olvide_contrasena.html");
    exit();
}

// Cerrar la conexión si aún está abierta (aunque los exit() la cierran)
$conn->close();
?>
