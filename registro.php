<?php
// Iniciar la sesión para poder manejar mensajes de error/éxito
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Registrarse - Tienda de Electrónica</title>

    <link rel="stylesheet" href="css/estilos.css">

</head>

<body class="centrado">

<div class="container">
 <h1>Registrarse</h1>

<?php
        // Mostrar mensajes de error o éxito si existen
        if (isset($_SESSION['mensaje'])) {
            $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
            // (Mejora: añadido htmlspecialchars por seguridad)
            echo "<div class='mensaje $tipo_mensaje'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
            
            // Borrar el mensaje después de mostrarlo
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        }
        ?>

<form action="accion_registro.php" method="POST">
<div class="form-group">
<label for="email">Correo electrónico</label>
<input type="email" id="email" name="email" required>
</div>
<div class="form-group">
<label for="nombres">Nombre</label>
<input type="text" id="nombres" name="nombres" required>
</div>
<div class="form-group">
<label for="apellidos">Apellidos</label>
<input type="text" id="apellidos" name="apellidos" required>
</div>
 <div class="form-group">
<label for="password">Contraseña</label>
<input type="password" id="password" name="password" required>
</div>
<div class="form-group">
<label for="password_repetir">Repetir contraseña</label>
<input type="password" id="password_repetir" name="password_repetir" required>
</div>
 
     <button type="submit" class="btn btn-primary">Registrarse</button>

<a href="login.php" class="btn btn-secondary">Iniciar sesión</a>
</form>
 </div>

</body>
</html>