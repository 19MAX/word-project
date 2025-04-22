function showAlert(type, message, position = 'center') {
    // Traducciones en español para los títulos según el tipo
    const titles = {
        success: "¡Éxito!",
        error: "¡Error!",
        warning: "¡Advertencia!",
        info: "¡Información!",
    };

    // Colores de los botones según el tipo
    const buttonColors = {
        success: '#4caf50', // Verde
        error: '#e74c3c', // Rojo
        warning: '#f39c12', // Amarillo
        info: '#3498db', // Azul
    };

    // Configuración base para estilos personalizados
    const customClass = {
        popup: `custom-popup swal2-${type}`,
        title: 'custom-title',
        htmlContainer: 'custom-html',
        confirmButton: 'custom-confirm-button',
    };

    // Notificaciones centradas
    if (position === 'center') {
        Swal.fire({
            title: titles[type] || "Notificación", // Título en español
            icon: type,
            html: `<div>${message}</div>`,
            showCloseButton: true,
            confirmButtonText: 'Entendido', // Botón genérico
            focusConfirm: true,
            customClass,
            background: type === 'success' ? '#f3fdf7' : '#ffffff', // Fondo dinámico
            color: '#333', // Texto oscuro
            confirmButtonColor: buttonColors[type] || '#4caf50', // Color dinámico del botón
        });
    }
    // Notificaciones tipo toast
    else {
        Swal.fire({
            icon: type,
            title: message,
            toast: true,
            position: position, // Posición dinámica
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            customClass: {
                popup: `custom-toast-popup swal2-${type}`,
                title: 'custom-toast-title',
            },
            background: '#fdfdfd', // Fondo neutro
            color: '#333', // Texto oscuro
        });
    }
}