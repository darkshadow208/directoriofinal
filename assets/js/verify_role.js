function updateProfileLink() {
    fetch('php/check_user_role.php')
        .then(response => response.json())
        .then(data => {
            const profileLink = document.getElementById('profile-link');
            if (data.is_business) {
                profileLink.href = 'profile-bussines.html';
            } else {
                profileLink.href = 'profile-user.html';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // En caso de error, redirigir al perfil de usuario por defecto
            document.getElementById('profile-link').href = 'profile-user.html';
        });
}

// Ejecutar la verificación cuando se carga la página
document.addEventListener('DOMContentLoaded', updateProfileLink);
