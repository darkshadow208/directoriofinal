let currentStep = 1;
const totalSteps = 3;

function selectType(type) {
    document.querySelectorAll('.form-section').forEach(section => {
        section.classList.remove('active');
    });

    if (type === 'user') {
        document.getElementById('user-form').classList.add('active');
    } else {
        document.getElementById('business-form').classList.add('active');
    }
}
function updateSteps() {
    // Actualizar indicadores de paso
    document.querySelectorAll('.step-wizard-item').forEach(item => {
        const step = parseInt(item.dataset.step);
        if (step <= currentStep) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    // Mostrar/ocultar pasos del formulario
    document.querySelectorAll('.business-step').forEach(step => {
        step.classList.remove('active');
    });
    document.querySelector(`.business-step[data-step="${currentStep}"]`).classList.add('active');

    // Actualizar botones
    const prevButton = document.getElementById('prevStep');
    const nextButton = document.getElementById('nextStep');
    const submitButton = document.getElementById('submitBusiness');

    prevButton.style.display = currentStep > 1 ? 'block' : 'none';
    nextButton.style.display = currentStep < totalSteps ? 'block' : 'none';
    submitButton.style.display = currentStep === totalSteps ? 'block' : 'none';
}
document.getElementById('nextStep').addEventListener('click', () => {
    if (currentStep < totalSteps) {
        currentStep++;
        updateSteps();
    }
});

document.getElementById('prevStep').addEventListener('click', () => {
    if (currentStep > 1) {
        currentStep--;
        updateSteps();
    }
});

// Manejo de formularios
document.getElementById('userRegistrationForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    try {
        const response = await fetch('php/register-user.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            // Mostrar mensaje y redirigir inmediatamente
            Swal.fire({
                icon: 'success',
                title: '¡Registro exitoso!',
                text: 'Redirigiendo...',
                showConfirmButton: false,
                timer: 1500
            });
            
            setTimeout(() => {
                window.location.href = 'sign-in-screen.html';
            }, 1500);
        } else {
            throw new Error(data.message || 'Error en el registro');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    }
});

document.getElementById('businessRegistrationForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
  
    const virtualStoreYes = document.getElementById('virtualStoreYes');
    if (virtualStoreYes.checked) {
        formData.set('address', 'tienda virtual');
    }
    try {
        const response = await fetch('php/register_business.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            // Mostrar mensaje y redirigir inmediatamente
            Swal.fire({
                icon: 'success',
                title: '¡Registro exitoso!',
                text: 'Redirigiendo...',
                showConfirmButton: false,
                timer: 1500
            });
            
            setTimeout(() => {
                window.location.href = 'sign-in-screen.html';
            }, 1500);
        } else {
            throw new Error(data.message || 'Error en el registro');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    }
});


document.addEventListener('DOMContentLoaded', function() {
    // Obtener el select de categorías
    const categorySelect = document.getElementById('categorySelect');

    // Hacer una solicitud Fetch al endpoint PHP con la acción "opciones"
    fetch('php/get-options.php?action=opciones')
        .then(response => response.json())
        .then(categories => {
            // Iterar sobre las categorías y agregar opciones al select
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar las categorías:', error));

        const virtualStoreYes = document.getElementById('virtualStoreYes');
            const virtualStoreNo = document.getElementById('virtualStoreNo');
            const addressField = document.getElementById('addressField');
            const addressInput = addressField.querySelector('input[name="address"]');

            // Función para actualizar el campo de dirección
            function updateAddressField() {
                if (virtualStoreYes.checked) {
                    addressField.style.display = 'none';
                    addressInput.removeAttribute('required');
                } else {
                    addressField.style.display = 'block';
                    addressInput.setAttribute('required', 'required');
                }
            }

            // Agregar event listeners a los checkboxes
            virtualStoreYes.addEventListener('change', updateAddressField);
            virtualStoreNo.addEventListener('change', updateAddressField);

            // Llamar a la función inicialmente para establecer el estado correcto
            updateAddressField();
        });

