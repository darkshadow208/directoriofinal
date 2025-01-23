document.addEventListener('DOMContentLoaded', function() {
    // Verificar sesión de negocio
    checkBusinessSession();
    loadBusinessData();

    // Event listeners para las diferentes secciones
    document.getElementById('addProductBtn').addEventListener('click', showAddProductModal);
    document.getElementById('addPromotionBtn').addEventListener('click', showAddPromotionModal);
    document.getElementById('addEventBtn').addEventListener('click', showAddEventModal);
    document.getElementById('editProfileBtn').addEventListener('click', showEditProfileModal);
});

function checkBusinessSession() {
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            if (!data.is_business) {
                window.location.href = 'profile-user.html';
            }
        })
        .catch(error => console.error('Error:', error));
}

function loadBusinessData() {
    fetch('get_business_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBusinessProfile(data.data.business);
                updateProductsList(data.data.products);
                updatePromotionsList(data.data.promotions);
                updateEventsList(data.data.events);
                updateReviewsList(data.data.reviews);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Funciones de actualización de UI
function updateBusinessProfile(business) {
    document.getElementById('businessName').textContent = business.business_name;
    document.getElementById('businessCategory').textContent = business.category;
    document.getElementById('businessImage').src = business.photo || 'assets/default-business.png';
    // ... más actualizaciones de UI
}

// Funciones para mostrar modales
function showAddProductModal() {
    // Implementación del modal para agregar productos
}

function showAddPromotionModal() {
    // Implementación del modal para agregar promociones
}

function showAddEventModal() {
    // Implementación del modal para agregar eventos
}

function showEditProfileModal() {
    // Implementación del modal para editar perfil
}
