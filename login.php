<?php
session_start();
require 'config/conexion.php';

// Si ya estás logueado, te manda al inicio
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
            <h1>Iniciar Sesión</h1>
            
            <form action="accion_login.php" method="POST">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="********">
                </div>
                
                <button type="submit" class="btn btn-primary">Entrar</button>
                
                <div style="margin-top: 1.5rem; text-align: center;">
                    <p style="color: var(--color-gris-claro); margin-bottom: 0.5rem;">¿Aún no tienes cuenta?</p>
                    <a href="registro.php" style="color: var(--color-verde-neon); font-weight: bold; text-decoration: none; border-bottom: 1px solid var(--color-verde-neon);">¡Regístrate gratis aquí!</a>
                </div>
            </form>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>