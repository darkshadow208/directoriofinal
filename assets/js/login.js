// login.js
document.getElementById('btnlogin').addEventListener('click', function(event) {
    event.preventDefault();
    const email = document.getElementById('email').value;
    const contrasena = document.getElementById('contrasena').value;
    const formData = new FormData();
    formData.append('usario', document.getElementById('email').value);
    formData.append('contrasena', document.getElementById('contrasena').value);

fetch('php/acciones.php', {
        method: 'POST',
       
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
          
           
           
            Swal.fire({
                icon: "success",
                title: "Perfecto",
                text: "Ingreso exitoso",
              
              });
        } else {
            Swal.fire({
                icon: "error",
                title: "error",
                text: "correo o contraeseña incorrectos",
              
              });
            
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
