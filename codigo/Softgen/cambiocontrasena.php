<?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger mt-3">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success mt-3">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar y validar el correo electrónico
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Verificar si el campo está vacío
    if (empty($email)) {
        header('Location: cambiocontraseña.html?error=Por favor, complete todos los campos.');
        exit();
    }

    // Verificar si el correo electrónico es válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: cambiocontraseña.html?error=Por favor, ingrese un correo electrónico válido.');
        exit();
    }

    // Simular el envío de un correo electrónico (esto es solo un ejemplo)
    // En un caso real, aquí enviarías un correo con un enlace para restablecer la contraseña.
    $mensaje = "Se ha solicitado un cambio de contraseña para el correo: $email.";
    $mensaje .= "\nPor favor, haz clic en el siguiente enlace para restablecer tu contraseña:";
    $mensaje .= "\nhttp://tusitio.com/restablecer-contraseña.php?token=abc123"; // Enlace simulado

  
    header('Location: codigo.html');
    exit();
}
?>
