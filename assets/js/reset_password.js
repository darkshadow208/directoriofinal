document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const newPassword = document.getElementById('newPassword').value;
    const messageDiv = document.getElementById('message');

    try {
        const response = await fetch('php/reset_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ newPassword })
        });

        const data = await response.json();

        if (data.success) {
            messageDiv.innerHTML = '<p style="color: green;">Contraseña restablecida. Redirigiendo...</p>';
            setTimeout(() => {
                window.location.href = 'sign-in-screen.html';
            }, 1500); // Redirigir después de 1.5 segundos
        } else {
            messageDiv.innerHTML = `<p style="color: red;">${data.message}</p>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<p style="color: red;">Error al enviar la solicitud. Inténtalo de nuevo.</p>';
    }
});
