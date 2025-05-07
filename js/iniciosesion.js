document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');

    if (form) {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            let firstErrorField = null;

            // --- Obtener campos ---
            const rol = form.elements['rol'];
            const email = form.elements['email'];
            const contrasena = form.elements['contrasena'];

            // Función para mostrar error
            function showError(field, message) {
                 // Muestra alerta por ahora
                alert(message);
                isValid = false;
                if (!firstErrorField) {
                    firstErrorField = field;
                }
            }

            // --- Validaciones ---

            // 1. Rol seleccionado
            if (!rol || rol.value === "") {
                showError(rol, 'Por favor, selecciona tu rol.');
            }

            // 2. Email no vacío y formato básico
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || email.value.trim() === "") {
                showError(email, 'Por favor, ingresa tu correo electrónico.');
            } else if (!emailPattern.test(email.value.trim())) {
                showError(email, 'Por favor, ingresa un correo electrónico válido.');
            }


            // 3. Contraseña no vacía
            if (!contrasena || contrasena.value.trim() === "") {
                showError(contrasena, 'Por favor, ingresa tu contraseña.');
            }

            // --- Finalizar ---
            if (!isValid) {
                event.preventDefault(); // ¡IMPORTANTE! Detiene el envío si hay errores
                if (firstErrorField) {
                    firstErrorField.focus();
                }
            }
             // Si isValid es true, el formulario se envía al PHP.
        });
    } else {
        console.error("El formulario con id 'loginForm' no se encontró.");
    }
});