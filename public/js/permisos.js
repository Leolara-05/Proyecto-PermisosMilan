function actualizarEstado(id, estado) {
    if (!confirm(`¿Estás seguro de ${estado ? 'autorizar' : 'rechazar'} este permiso?`)) {
        return;
    }

    fetch(`/permisosmilan/${id}/autorizar`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ autorizado: estado })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Permiso ${estado ? 'autorizado' : 'rechazado'} correctamente.`);
            location.reload(); // Recargar la página para actualizar la vista
        } else {
            alert('Error al actualizar el permiso.');
        }
    })
    .catch(error => console.error('Error:', error));
}
