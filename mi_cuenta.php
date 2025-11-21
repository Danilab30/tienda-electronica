<?php
session_start();
require 'config/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Debes iniciar sesión para ver tu cuenta.";
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: login.php'); exit;
}

$usuario_id = $_SESSION['usuario_id'];
$pedidos = [];
try {
    $sql = "SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $pedidos = $stmt->fetchAll();
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>

<?php include 'includes/header.php'; ?>

    <main>
        <div class="cuenta-container">
            <div class="cuenta-header">
                <h1>Historial de Pedidos</h1>
                <p style="color: var(--color-gris-claro);">Aquí puedes ver todas las compras que has realizado.</p>
            </div>

            <?php if (empty($pedidos)): ?>
                <div class="reseña-login" style="text-align: center;">
                    <p>Aún no has realizado ninguna compra.</p>
                    <a href="index.php" class="btn btn-primary" style="display: inline-block; width: auto; margin-top: 1rem;">Ir a comprar</a>
                </div>
            <?php else: ?>
               <table class="tabla-pedidos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado Pago</th>
                            <th>Estatus Envío</th> <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>#<?php echo $pedido['id']; ?></td>
                                <td><?php echo date("d/m/Y", strtotime($pedido['fecha_pedido'])); ?></td>
                                <td style="font-weight: bold;">$<?php echo number_format($pedido['total'], 2); ?></td>
                                
                                <td><span class="estado-pagado"><?php echo strtoupper($pedido['estado_pago']); ?></span></td>
                                
                                <td>
                                    <span style="color: #fff; background: #333; padding: 2px 8px; border-radius: 10px; font-size: 0.85rem;">
                                        <?php echo htmlspecialchars($pedido['estatus']); ?>
                                    </span>
                                </td>

                                <td><a href="ver_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn-ver-pedido">Ver Detalles</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>