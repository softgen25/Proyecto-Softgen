<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contrasena = $_POST["contrasena"];
    $confirmarcontrasena = $_POST["confirmarcontrasena"];

    if ($contrasena === $confirmarcontrasena) {
        // Aquí iría la lógica para guardar la contraseña, por ejemplo, en una base de datos
        session_start();
        $_SESSION['mensaje'] = "Cambio de contraseña exitoso";
        header("Location: inicioSesion.html");
        exit();
    } else {
        echo "Las contraseñas no coinciden, por favor, inténtelo de nuevo.";
    }
} else {
    // Redireccionar si el formulario no fue enviado
    header("Location: inicioSesion.html");
    exit();
}
?>