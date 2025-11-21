<?php
session_start();
require 'config/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Error: Debes iniciar sesión para ver tu carrito.";
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: login.php'); exit;
}

$usuario_id = $_SESSION['usuario_id'];
$items_carrito = [];
$subtotal = 0.00;

try {
    $sql = "SELECT c.*, p.nombre, p.precio, p.imagen_url, p.stock FROM carrito c JOIN productos p ON c.producto_id = p.id WHERE c.usuario_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $items_carrito = $stmt->fetchAll();
    foreach ($items_carrito as $item) { $subtotal += $item['precio'] * $item['cantidad']; }
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>

<?php include 'includes/header.php'; ?>

    <main>
        <?php if (isset($_SESSION['mensaje'])): ?>
            <?php 
                $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
                echo "<div class='mensaje $tipo_mensaje' style='max-width: 1000px; margin: 1rem auto; width:90%;'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
                unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']);
            ?>
        <?php endif; ?>

        <div class="carrito-container">
            <div class="carrito-header">
                <h1>Carrito de Compra</h1>
                <a href="categorias.php">← Seguir comprando</a>
            </div>

            <div class="carrito-lista">
                <?php if (empty($items_carrito)): ?>
                    <div class="reseña-login" style="text-align: center;">
                        <p>Tu carrito está vacío.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($items_carrito as $item): ?>
                        <div class="carrito-item">
                            <div class="carrito-item-img">
                                <img src="uploads/<?php echo htmlspecialchars($item['imagen_url']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                            </div>
                            <div class="carrito-item-info">
                                <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                                <span class="stock">Disponibles: <?php echo htmlspecialchars($item['stock']); ?></span>
                            </div>
                            <div class="carrito-item-precio">$<?php echo htmlspecialchars($item['precio']); ?></div>
                            <div class="carrito-item-cantidad">
                                <form action="accion_actualizar_carrito.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" max="<?php echo $item['stock']; ?>" onchange="this.form.submit()">
                                </form>
                            </div>
                            <div class="carrito-item-quitar">
                                <a href="accion_quitar_carrito.php?id=<?php echo $item['id']; ?>" onclick="return confirm('¿Quitar este producto?');">Quitar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($items_carrito)): ?>
                <div class="carrito-resumen">
                    <div class="resumen-linea"><span>Subtotal:</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
                    <div class="resumen-linea"><span>Envío:</span><span>Gratis</span></div>
                    <div class="resumen-linea total"><span>Total:</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
                    <a href="checkout.php" class="btn btn-primary btn-pagar">Ir a pagar</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>