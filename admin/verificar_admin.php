<?php
// Este es nuestro "Guardia de Seguridad"

// 1. Reanudamos la sesión (si no está iniciada ya)
// (Usamos 'session_status' para evitar errores si ya se inició)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. LA REVISIÓN DE SEGURIDAD:
// Verificamos si la variable 'usuario_rol' NO existe
// O si SÍ existe PERO NO es 'admin'
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    
    // 3. Si no es admin, preparamos un mensaje de error
    $_SESSION['mensaje'] = "Error: No tienes permiso para acceder a esta página.";
    $_SESSION['tipo_mensaje'] = 'error';
    
    // 4. Lo redirigimos FUERA de la carpeta admin.
    // Usamos '../index.php' para subir un nivel y mandarlo
    // a la página de inicio de la tienda (la pública).
    header('Location: ../index.php');
    exit;
}

// 5. Si el script llega hasta aquí, significa que el usuario
// SÍ está logueado Y SÍ es 'admin'. No hacemos nada
// y dejamos que la página (ej. index.php) continúe cargando.
?>