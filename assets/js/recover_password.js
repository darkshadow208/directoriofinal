document.getElementById('recoverPasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('Email').value;
    const messageDiv = document.getElementById('message');
    const loadingSlider = document.getElementById('loadingSlider');
    const progressBar = document.getElementById('progressBar');

    // Mostrar el slider de carga
    loadingSlider.style.display = 'block';
    let progress = 0;
    const interval = setInterval(() => {
        progress += 1;
        progressBar.style.width = progress + '%';
        progressBar.innerText = progress + '%';
        if (progress >= 100) {
            clearInterval(interval);
        }
    }, 50); // Ajusta la velocidad del slider según tus necesidades

    const formData = new FormData();
    formData.append('email', email);

    try {
        console.log('Enviando solicitud de recuperación de contraseña...');
        const response = await fetch('php/recover_pass.php', {
            method: 'POST',
            body: formData
        });

        console.log('Respuesta recibida:', response);

        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }

        const data = await response.json();
        console.log('Datos recibidos:', data);

        // Ocultar el slider de carga
        loadingSlider.style.display = 'none';
        progressBar.style.width = '0%';
        progressBar.innerText = '0%';

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'Código de recuperación enviado. Revisa tu correo electrónico.',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'verify_code.html';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
            });
        }
    } catch (error) {
        console.error('Error al enviar la solicitud:', error);

        // Ocultar el slider de carga
        loadingSlider.style.display = 'none';
        progressBar.style.width = '0%';
        progressBar.innerText = '0%';

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al enviar la solicitud. Inténtalo de nuevo.',
        });
    }
});
