<?php
session_start();
require 'config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
if (!isset($_GET['id']) || empty($_GET['id'])) { header('Location: mi_cuenta.php'); exit; }

$pedido_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

try {
    $sql_pedido = "SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([$pedido_id, $usuario_id]);
    $pedido = $stmt_pedido->fetch();

    if (!$pedido) { header('Location: mi_cuenta.php'); exit; }

    $sql_detalles = "SELECT dp.*, p.nombre, p.imagen_url FROM detalles_pedido dp JOIN productos p ON dp.producto_id = p.id WHERE dp.pedido_id = ?";
    $stmt_detalles = $pdo->prepare($sql_detalles);
    $stmt_detalles->execute([$pedido_id]);
    $detalles = $stmt_detalles->fetchAll();
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>

<?php include 'includes/header.php'; ?>

    <main>
        <div class="detalle-container">
            <div class="detalle-header">
                <div>
                    <h1>Pedido #<?php echo $pedido['id']; ?></h1>
                    <span class="fecha-pedido">Realizado el: <?php echo date("d/m/Y H:i", strtotime($pedido['fecha_pedido'])); ?></span>
                </div>
                <div style="text-align: right;">
                    <span style="display:block; color:var(--color-gris-claro); font-size:0.9rem;">Estado:</span>
                    <span style="color:var(--color-verde-neon); font-weight:bold;"><?php echo strtoupper($pedido['estado_pago']); ?></span>
                </div>
            </div>

            <div class="lista-items">
                <?php foreach ($detalles as $item): ?>
                    <div class="item-pedido">
                        <img src="uploads/<?php echo htmlspecialchars($item['imagen_url']); ?>" alt="">
                        <div class="item-info">
                            <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                            <span><?php echo $item['cantidad']; ?> x $<?php echo number_format($item['precio_unitario'], 2); ?></span>
                        </div>
                        <div class="item-total">$<?php echo number_format($item['cantidad'] * $item['precio_unitario'], 2); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="resumen-total">Total Pagado: <span>$<?php echo number_format($pedido['total'], 2); ?></span></div>
            <a href="mi_cuenta.php" class="btn-volver">← Volver a mis pedidos</a>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>