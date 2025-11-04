<?php
// 1. Iniciar la sesión (siempre primero)
session_start();

// 2. "Destruir" todas las variables de sesión
$_SESSION = array(); // Sobrescribe la sesión con un array vacío

// 3. Destruir la sesión completamente
session_destroy();

// 4. Redirigir al usuario a la página de login
header('Location: login.php?mensaje=Has cerrado sesión exitosamente.');
exit;
?>