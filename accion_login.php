<?php
// 1. Iniciar la sesión (siempre primero)
session_start();

// 2. Incluir el archivo de conexión
require 'config/conexion.php';

// 3. Verificar si los datos fueron enviados por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Recoger los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 5. Buscar al usuario en la base de datos por su email
    try {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        
        $usuario = $stmt->fetch(); // Obtener la fila del usuario

        // 6. Verificar si el usuario existe Y si la contraseña es correcta
        if ($usuario && password_verify($password, $usuario['password'])) {
            // ¡Contraseña correcta!
            
            // 7. Guardar los datos del usuario en la SESIÓN
            // Esto es lo que "mantiene al usuario logueado"
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombres'] = $usuario['nombres'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            // 8. Redirigir a la página principal (index.php)
            header('Location: index.php'); // Aún no la creamos, pero será el siguiente paso
            exit;

        } else {
            // Usuario no encontrado o contraseña incorrecta
            $_SESSION['mensaje'] = "Correo o contraseña incorrectos.";
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: login.php');
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al intentar iniciar sesión: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: login.php');
        exit;
    }

} else {
    // Si alguien intenta acceder a accion_login.php directamente
    header('Location: login.php');
    exit;
}
?>