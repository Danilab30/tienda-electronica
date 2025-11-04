<?php
// Iniciar la sesión para poder manejar mensajes
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Tienda de Electrónica</title>
    
    <link rel="stylesheet" href="css/estilos.css">
    
</head>
<body class="centrado"> <div class="container">
        <h1>Iniciar Sesión</h1>

        <?php
        // Mostrar mensajes de error o éxito si existen (como el de "Registro exitoso")
        if (isset($_SESSION['mensaje'])) {
            // Se usa htmlspecialchars para más seguridad al mostrar mensajes
            $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
            echo "<div class='mensaje $tipo_mensaje'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
            
            // Borrar el mensaje después de mostrarlo
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        }
        ?>

        <form action="accion_login.php" method="POST">
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
            <a href="registro.php" class="btn btn-