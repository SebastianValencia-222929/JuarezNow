document.addEventListener('DOMContentLoaded', () => {
    const reporteForm = document.getElementById('reporteForm');
    const fotoInput = document.getElementById('foto');
    const fotoError = document.getElementById('fotoError');

    if (reporteForm && fotoInput) {
        reporteForm.addEventListener('submit', (event) => {
            let isValid = true;
            fotoError.style.display = 'none';

            // Validar archivo de imagen si se selecciona uno
            if (fotoInput.files.length > 0) {
                const file = fotoInput.files[0];
                const fileSize = file.size;
                const fileName = file.name;
                const fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
                
                // 1. Validar tamaño (máx 2MB)
                const maxSizeBytes = 2 * 1024 * 1024;
                if (fileSize > maxSizeBytes) {
                    fotoError.style.display = 'block';
                    isValid = false;
                }

                // 2. Validar extensión
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (!allowedExtensions.includes(fileExtension)) {
                    fotoError.style.display = 'block';
                    isValid = false;
                }
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});
