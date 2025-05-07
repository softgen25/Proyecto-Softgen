<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el código ingresado por el usuario
    $codigo_ingresado = $_POST['codigo'];

    // Obtener el código guardado en la sesión
    $codigo_verificacion = $_SESSION['codigo_verificacion'];

    // Verificar si el código es correcto
    if ($codigo_ingresado == $codigo_verificacion) {
        // Código correcto
        echo json_encode(['success' => true]);
    } else {
        // Código incorrecto
        echo json_encode(['success' => false]);
    }
} else {
    // Método no permitido
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);

}   header("location: confirmacion.html");
    exit();


?>
