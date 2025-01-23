document.getElementById('verifyCodeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const code = document.getElementById('code').value;
    const messageDiv = document.getElementById('message');

    try {
        const response = await fetch('php/verify_code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ code })
        });

        const data = await response.json();

        if (data.success) {
            messageDiv.innerHTML = '<p style="color: green;">Código verificado. Redirigiendo...</p>';
            setTimeout(() => {
                window.location.href = 'reset_password.html';
            }, 1500); // Redirigir después de 1.5 segundos
        } else {
            messageDiv.innerHTML = `<p style="color: red;">${data.message}</p>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<p style="color: red;">Error al enviar la solicitud. Inténtalo de nuevo.</p>';
    }
});
