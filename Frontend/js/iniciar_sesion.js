document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    if (loginForm) {
        loginForm.addEventListener('submit', (event) => {
            let isValid = true;

            // Limpiar errores previos
            emailError.style.display = 'none';
            passwordError.style.display = 'none';

            // Validar correo
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                emailError.style.display = 'block';
                isValid = false;
            }

            // Validar longitud de contraseña
            if (passwordInput.value.length < 6) {
                passwordError.style.display = 'block';
                isValid = false;
            }

            // Si hay algún error, evitar envío del formulario
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});
