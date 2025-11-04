<?php
// 1. Iniciar la sesión (siempre primero)
session_start();

// 2. Incluir el archivo de conexión
// Usamos 'require' porque si este archivo falta, el script debe detenerse
require 'config/conexion.php';

// 3. Verificar si los datos fueron enviados por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Recoger los datos del formulario
    $email = $_POST['email'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $password = $_POST['password'];
    $password_repetir = $_POST['password_repetir'];

    // --- VALIDACIONES ---

    // 5. Validar que las contraseñas coincidan
    if ($password !== $password_repetir) {
        // Guardar mensaje de error en la sesión
        $_SESSION['mensaje'] = "Las contraseñas no coinciden.";
        $_SESSION['tipo_mensaje'] = 'error';
        // Redirigir de vuelta al formulario
        header('Location: registro.php');
        exit; // Detener el script
    }

    // 6. Validar que el email no exista ya
    try {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            // El email ya existe
            $_SESSION['mensaje'] = "El correo electrónico ya está registrado.";
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: registro.php');
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al verificar el email: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: registro.php');
        exit;
    }

    // --- FIN VALIDACIONES ---

    // 7. "Hashear" la contraseña 
    // Esto la convierte en un churro de texto largo e irreversible.
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 8. Insertar el nuevo usuario en la base de datos
    try {
        // Usamos "prepared statements" (con '?') para evitar Inyección SQL
        $sql = "INSERT INTO usuarios (nombres, apellidos, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta pasando los valores
        $stmt->execute([$nombres, $apellidos, $email, $password_hash]);

        // 9. Si todo salió bien, redirigir al login con mensaje de éxito
        $_SESSION['mensaje'] = "¡Registro exitoso! Ahora puedes iniciar sesión.";
        $_SESSION['tipo_mensaje'] = 'exito';
        header('Location: login.php'); // Aún no creamos login.php, pero lo haremos
        exit;

    } catch (PDOException $e) {
        // Si algo falla en la inserción
        $_SESSION['mensaje'] = "Error al registrar el usuario: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: registro.php');
        exit;
    }

} else {
    // Si alguien intenta acceder a accion_registro.php directamente
    header('Location: registro.php');
    exit;
}
?>