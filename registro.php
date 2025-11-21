<?php
session_start();
// Si ya estás logueado, no deberías ver esto
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>

<?php include 'includes/header.php'; ?>

    <main class="main-centered">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <?php 
                $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
                echo "<div class='mensaje $tipo_mensaje' style='max-width: 500px; margin: 1rem auto;'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
                unset($_SESSION['mensaje']);
                unset($_SESSION['tipo_mensaje']);
            ?>
        <?php endif; ?>

        <div class="container">
            <h1>Crear Cuenta</h1>

            <form action="accion_registro.php" method="POST">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required placeholder="tucorreo@email.com">
                </div>
                <div class="form-group">
                    <label for="nombres">Nombre</label>
                    <input type="text" id="nombres" name="nombres" required placeholder="Tu nombre">
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" required placeholder="Tus apellidos">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Crea una contraseña segura">
                </div>
                <div class="form-group">
                    <label for="password_repetir">Repetir contraseña</label>
                    <input type="password" id="password_repetir" name="password_repetir" required placeholder="Confirma tu contraseña">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Registrarse</button>
                
                <div style="margin-top: 1.5rem; text-align: center;">
                    <p style="color: var(--color-gris-claro); margin-bottom: 0.5rem;">¿Ya tienes cuenta?</p>
                    <a href="login.php" style="color: var(--color-verde-neon); font-weight: bold; text-decoration: none; border-bottom: 1px solid var(--color-verde-neon);">¡Inicia sesión aquí!</a>
                </div>
            </form>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>