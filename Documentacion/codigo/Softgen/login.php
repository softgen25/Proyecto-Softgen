<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $rol = $_POST["rol"];
    $nombre = $_POST["nombre"]; 
    $apellido = $_POST["apellido"]; 
    $email = $_POST["email"];
    $contrasena = $_POST["contrasena"];
    $confirmar_contrasena = $_POST["confirmar_contrasena"];


    if (empty($rol) || empty($nombre) || empty($apellido) || empty($email) || empty($contrasena) || empty($confirmar_contrasena)) {
        echo "Por favor, complete todos los campos.";
    } elseif ($contrasena != $confirmar_contrasena) {
        echo "Las contraseñas no coinciden.";
    } else {

        $servername = "localhost";
        $username = "tu_usuario";
        $password = "tu_contraseña";
        $dbname = "tu_base_de_datos";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (rol, nombre, apellido, email, contrasena) VALUES ('$rol', '$nombre', '$apellido', '$email', '$contrasena_hash')";

        if ($conn->query($sql) === TRUE) {
            echo "Registro exitoso.";

        } else {
            echo "Error al registrar: " . $sql . "<br>" . $conn->error;
        }     
    } 
    header("Location: inicioSesion.html");
    exit();
}
?>