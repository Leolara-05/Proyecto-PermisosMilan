document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario
    const cedulaInput = document.getElementById('cedula');
    const nombreInput = document.getElementById('nombre');
    const cargoInput = document.getElementById('cargo');
    const emailInput = document.getElementById('correo_electronico');
    const firmaInput = document.getElementById('firma_trabajador');
    const mensajeDiv = document.getElementById('mensaje-busqueda');
    
    // Función para buscar empleado cuando se pierde el foco del campo cédula
    if (cedulaInput) {
        cedulaInput.addEventListener('blur', function() {
            buscarEmpleado();
        });
        
        // También buscar cuando se presiona Enter
        cedulaInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarEmpleado();
            }
        });
    }
    
    // Función para buscar empleado
    function buscarEmpleado() {
        const cedula = cedulaInput.value.trim();
        
        if (!cedula) {
            return; // No hacer nada si el campo está vacío
        }
        
        mostrarMensaje('Buscando empleado...', 'info');
        
        // Realizar la petición AJAX
        fetch(`/buscar-datos/${cedula}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Llenar los campos automáticamente
                nombreInput.value = data.nombre || '';
                cargoInput.value = data.cargo || '';
                emailInput.value = data.correo_electronico || data.email || '';
                firmaInput.value = data.firma_trabajador || data.nombre || '';
                
                mostrarMensaje('Empleado encontrado exitosamente', 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('No se encontró el empleado con esa cédula', 'danger');
            });
    }
    
    // Función para mostrar mensajes
    function mostrarMensaje(mensaje, tipo) {
        if (mensajeDiv) {
            mensajeDiv.textContent = mensaje;
            mensajeDiv.className = `alert alert-${tipo}`;
            mensajeDiv.style.display = 'block';
            
            // Ocultar el mensaje después de 3 segundos
            setTimeout(() => {
                mensajeDiv.style.display = 'none';
            }, 3000);
        } else {
            console.log(mensaje);
        }
    }
});