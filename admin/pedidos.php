<?php
require_once 'verificar_admin.php'; // Guardia
require '../config/conexion.php';

// Obtener todos los pedidos con el nombre del usuario
$ventas = [];
try {
    $sql = "SELECT p.*, u.nombres, u.apellidos, u.email 
            FROM pedidos p 
            JOIN usuarios u ON p.usuario_id = u.id 
            ORDER BY p.fecha_pedido DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ventas = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al cargar ventas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas - Admin</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body class="page-content">

    <div class="container admin-container" style="max-width: 1200px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Reporte de Ventas</h1>
            <a href="index.php" class="btn btn-secondary" style="width: auto; border-color: #ffc107; color: #ffc107;">← Volver a Productos</a>
        </div>

        <div class="list-container">
            <div style="overflow-x: auto;"> 
                <table class="tabla-inventario">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ventas)): ?>
                            <tr>
                                <td colspan="7">No hay ventas registradas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ventas as $venta): ?>
                                <tr>
                                    <td>#<?php echo $venta['id']; ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($venta['fecha_pedido'])); ?></td>
                                    <td><?php echo htmlspecialchars($venta['nombres'] . ' ' . $venta['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['email']); ?></td>
                                    <td style="font-weight: bold;">$<?php echo number_format($venta['total'], 2); ?></td>
                                    <td>
                                        <span style="color: var(--color-verde-neon); border: 1px solid var(--color-verde-neon); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem;">
                                            <?php echo strtoupper($venta['estado_pago']); ?>
                                        </span>
                                    </td>
                                    <td class="acciones">
                                        <a href="ver_pedido.php?id=<?php echo $venta['id']; ?>" class="btn btn-editar" style="background-color: #00FF84; color: #000;">Ver Detalles</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>