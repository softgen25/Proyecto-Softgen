// Espera a que el contenido del DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroForm');

    if (form) {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            let firstErrorField = null;

            // --- Obtener los campos del formulario ---
            const rol = form.elements['rol'];
            const nombre = form.elements['nombre'];
            const apellido = form.elements['apellido'];
            const telefono = form.elements['telefono']; // Teléfono puede ser opcional
            const email = form.elements['email'];
            const contrasena = form.elements['contrasena'];
            const confirmarContrasena = form.elements['confirmarcontrasena'];

            // Función simple para mostrar alerta y marcar como inválido
            function showError(field, message) {
                // Muestra alerta por ahora para asegurar que la validación se ejecuta
                alert(message);
                isValid = false;
                if (!firstErrorField) {
                    firstErrorField = field;
                }
            }

            // --- Realizar Validaciones ---


            // 2. Nombre no vacío
            if (!nombre || nombre.value.trim() === "") {
                showError(nombre, 'Por favor, ingresa tu nombre.');
            }

            // 3. Apellido no vacío
            if (!apellido || apellido.value.trim() === "") {
                showError(apellido, 'Por favor, ingresa tu apellido.');
            }

            // 4. Email no vacío y formato básico
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || email.value.trim() === "") {
                showError(email, 'Por favor, ingresa tu correo electrónico.');
            } else if (!emailPattern.test(email.value.trim())) {
                showError(email, 'Por favor, ingresa un correo electrónico válido.');
            }

            // 5. Contraseña no vacía
            if (!contrasena || contrasena.value.trim() === "") {
                showError(contrasena, 'Por favor, ingresa una contraseña.');
            }
            // Validacion simple de longitud
            else if (contrasena.value.trim().length < 6) {
                showError(contrasena, 'La contraseña debe tener al menos 6 caracteres.');
            }


            // 6. Confirmar contraseña no vacía
            if (!confirmarContrasena || confirmarContrasena.value.trim() === "") {
                showError(confirmarContrasena, 'Por favor, confirma tu contraseña.');
            }

            // 7. Contraseñas coinciden (solo si ambas tienen valor y la primera es válida)
            if (isValid && contrasena.value.trim() !== "" && confirmarContrasena.value.trim() !== "" && contrasena.value !== confirmarContrasena.value) {
                showError(confirmarContrasena, 'Las contraseñas no coinciden.');
            }

            // --- Finalizar Validación ---
            if (!isValid) {
                event.preventDefault(); // ¡IMPORTANTE! Detiene el envío del formulario si hay errores
                if (firstErrorField) {
                    firstErrorField.focus(); // Pone el foco en el primer campo con error
                }
            }
            // Si isValid sigue siendo true, el formulario se enviará al PHP.
        });
    } else {
        console.error("El formulario con id 'registroForm' no se encontró.");
    }
});