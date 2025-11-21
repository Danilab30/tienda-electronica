<?php
require_once 'verificar_admin.php';
require '../config/conexion.php';

if (!isset($_GET['id'])) { header('Location: pedidos.php'); exit; }
$pedido_id = $_GET['id'];

// --- LÓGICA PARA CAMBIAR ESTATUS (Si se envió el formulario) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nuevo_estatus'])) {
    $nuevo_estatus = $_POST['nuevo_estatus'];
    try {
        $sql_update = "UPDATE pedidos SET estatus = ? WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$nuevo_estatus, $pedido_id]);
        // Recargamos la página para ver el cambio
        header("Location: ver_pedido.php?id=" . $pedido_id);
        exit;
    } catch (PDOException $e) {
        $error = "Error al actualizar: " . $e->getMessage();
    }
}

try {
    // 1. Info del Pedido y Cliente
    $sql_pedido = "SELECT p.*, u.nombres, u.apellidos, u.email 
                   FROM pedidos p 
                   JOIN usuarios u ON p.usuario_id = u.id 
                   WHERE p.id = ?";
    $stmt = $pdo->prepare($sql_pedido);
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch();

    if (!$pedido) { header('Location: pedidos.php'); exit; }

    // 2. Productos del Pedido
    $sql_detalles = "SELECT dp.*, p.nombre, p.imagen_url 
                     FROM detalles_pedido dp
                     JOIN productos p ON dp.producto_id = p.id
                     WHERE dp.pedido_id = ?";
    $stmt_detalles = $pdo->prepare($sql_detalles);
    $stmt_detalles->execute([$pedido_id]);
    $detalles = $stmt_detalles->fetchAll();

} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Venta #<?php echo $pedido['id']; ?></title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .detalle-admin { background: #222; padding: 2rem; border-radius: 8px; border: 1px solid #444; max-width: 800px; margin: auto; }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Dos columnas para info */
            gap: 2rem;
            border-bottom: 1px solid #444;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .info-bloque h3 { color: var(--color-verde-neon); margin-top: 0; font-size: 1.2rem; }
        .info-bloque p { margin: 0.5rem 0; color: #ccc; line-height: 1.5; }
        .info-bloque strong { color: #fff; }

        .item-lista { display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid #333; padding: 1rem 0; }
        .item-lista img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .total-final { text-align: right; font-size: 1.5rem; color: #00FF84; font-weight: bold; margin-top: 1rem; }

        /* Formulario de cambio de estatus */
        .status-form {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #1a1a1a;
            border-radius: 4px;
            border: 1px dashed #555;
        }
        .status-form select {
            padding: 0.5rem;
            background: #000;
            color: white;
            border: 1px solid #555;
            border-radius: 4px;
            margin-right: 0.5rem;
        }
        .btn-actualizar {
            background-color: #ffc107;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body class="page-content">

    <div class="container admin-container">
        <h1>Detalle de Venta #<?php echo $pedido['id']; ?></h1>

        <div class="detalle-admin">
            
            <div class="info-grid">
                <div class="info-bloque">
                    <h3>Datos del Cliente</h3>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['nombres'] . ' ' . $pedido['apellidos']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['email']); ?></p>
                    <p><strong>Fecha:</strong> <?php echo date("d/m/Y H:i", strtotime($pedido['fecha_pedido'])); ?></p>
                    <p><strong>Pago:</strong> <span style="color:#00FF84;"><?php echo strtoupper($pedido['estado_pago']); ?></span></p>
                </div>

                <div class="info-bloque">
                    <h3>Envío</h3>
                    <p><strong>Dirección:</strong><br>
                    <?php echo nl2br(htmlspecialchars($pedido['direccion_envio'] ?? 'No especificada')); ?>
                    </p>
                    
                    <div class="status-form">
                        <p style="margin:0 0 0.5rem 0;"><strong>Estatus Actual:</strong> 
                            <span style="color: var(--color-verde-neon);"><?php echo htmlspecialchars($pedido['estatus']); ?></span>
                        </p>
                        <form action="" method="POST">
                            <select name="nuevo_estatus">
                                <option value="Procesando" <?php if($pedido['estatus'] == 'Procesando') echo 'selected'; ?>>Procesando</option>
                                <option value="Enviado" <?php if($pedido['estatus'] == 'Enviado') echo 'selected'; ?>>Enviado</option>
                                <option value="Entregado" <?php if($pedido['estatus'] == 'Entregado') echo 'selected'; ?>>Entregado</option>
                                <option value="Cancelado" <?php if($pedido['estatus'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
                            </select>
                            <button type="submit" class="btn-actualizar">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>

            <h3>Productos</h3>
            <?php foreach ($detalles as $item): ?>
                <div class="item-lista">
                    <?php if ($item['imagen_url']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($item['imagen_url']); ?>">
                    <?php else: ?>
                        <div style="width:50px; height:50px; background:#333;"></div>
                    <?php endif; ?>
                    
                    <div style="flex: 1;">
                        <strong style="color: #fff;"><?php echo htmlspecialchars($item['nombre']); ?></strong><br>
                        <span style="color: #999;">$<?php echo number_format($item['precio_unitario'], 2); ?> x <?php echo $item['cantidad']; ?></span>
                    </div>
                    <div style="color: #fff; font-weight: bold;">
                        $<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2); ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total-final">
                Total: $<?php echo number_format($pedido['total'], 2); ?>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="pedidos.php" class="btn btn-secondary" style="display: inline-block; width: auto;">← Volver a Lista de Ventas</a>
        </div>
    </div>

</body>
</html>