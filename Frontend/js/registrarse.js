document.addEventListener('DOMContentLoaded', () => {
    const registroForm = document.getElementById('registroForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const confirmError = document.getElementById('confirmError');

    // 1. Alternar visibilidad de contraseña
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePassword.querySelector('i').classList.toggle('fa-eye');
            togglePassword.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    // 2. Validación en cliente antes del envío del formulario
    if (registroForm) {
        registroForm.addEventListener('submit', (event) => {
            let isValid = true;

            // Limpiar errores previos
            emailError.style.display = 'none';
            passwordError.style.display = 'none';
            confirmError.style.display = 'none';

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

            // Validar que coincidan
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmError.style.display = 'block';
                isValid = false;
            }

            // Si hay algún error, evitar envío del formulario
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});
