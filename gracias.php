<?php
session_start();
if (!isset($_SESSION['ultimo_pedido_id'])) { header('Location: index.php'); exit; }
$pedido_id = $_SESSION['ultimo_pedido_id'];
unset($_SESSION['ultimo_pedido_id']);
?>

<?php include 'includes/header.php'; ?>

    <main>
        <div class="gracias-container">
            <h1>¡Pago Exitoso!</h1>
            <p>Muchas gracias por tu compra, carnal. Hemos recibido tu pago y tu pedido está siendo procesado.</p>
            
            <p>Tu número de pedido es:</p>
            <div class="numero-pedido">#<?php echo htmlspecialchars($pedido_id); ?></div>
            
            <p>Recibirás una confirmación por correo (eventualmente 😉).</p>
            <a href="index.php" class="btn btn-primary btn-volver">Seguir Comprando</a>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>