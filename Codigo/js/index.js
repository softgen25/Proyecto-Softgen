document.addEventListener('DOMContentLoaded', function() {
    // Script existente para resaltar el enlace activo
    const currentPagePath = window.location.pathname;
    const currentPageFilename = currentPagePath.substring(currentPagePath.lastIndexOf('/') + 1) || 'index.html';
    const navLinks = document.querySelectorAll('.navbar-nav.me-auto .nav-link');

    navLinks.forEach(link => {
        link.classList.remove('active');
        link.removeAttribute('aria-current');

        const linkHref = link.getAttribute('href');
        const linkFilename = linkHref.substring(linkHref.lastIndexOf('/') + 1);

        if (linkFilename === currentPageFilename) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        } else if (currentPagePath === '/' && linkFilename === 'index.html') {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });

    // --- Script para restringir acceso a módulos en el homepage y redirigir ---

    // Identificar si estamos en la página de inicio (index.html o '/')
    const isOnHomepage = currentPageFilename === 'index.html' || currentPagePath === '/';

    if (isOnHomepage) {
        // Seleccionar los enlaces a los módulos que requieren inicio de sesión
        const restrictedLinks = document.querySelectorAll(
            'a[href="creacion.html"], ' +
            'a[href="visualizacion.html"], ' +
            'a[href="soporte0.3.html"]'
        );

        // Añadir un event listener a cada uno de estos enlaces
        restrictedLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                // Prevenir la navegación por defecto
                event.preventDefault();

                // Mostrar una ventana de confirmación
                const userConfirmed = confirm('Para acceder a este módulo, debes iniciar sesión o crear una cuenta. ¿Quieres ir a la página de inicio de sesión?');

                // Si el usuario hizo clic en "OK" (confirmó), redirigir
                if (userConfirmed) {
                    window.location.href = 'iniciosesion.html'; // Redirige a la página de inicio de sesión
                }
                // Si el usuario hizo clic en "Cancelar", no hacemos nada
            });
        });
    }
    // --- Fin del script ---

});