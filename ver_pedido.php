<?php
// 1. Iniciar sesión
session_start();
require 'config/conexion.php';

// 2. GUARDIA DE LOGIN
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// 3. VALIDAR ID DEL PEDIDO
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: mi_cuenta.php');
    exit;
}

$pedido_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

try {
    // 4. OBTENER LA INFO DEL PEDIDO (ENCABEZADO)
    // ¡OJO! Es VITAL el "AND usuario_id = ?". 
    // Esto evita que un usuario cambie el ID en la URL y vea el pedido de otra persona.
    $sql_pedido = "SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([$pedido_id, $usuario_id]);
    $pedido = $stmt_pedido->fetch();

    if (!$pedido) {
        // Si no encuentra el pedido o no es tuyo, te regresa.
        header('Location: mi_cuenta.php');
        exit;
    }

    // 5. OBTENER LOS PRODUCTOS DEL PEDIDO (DETALLES)
    $sql_detalles = "SELECT dp.*, p.nombre, p.imagen_url 
                     FROM detalles_pedido dp
                     JOIN productos p ON dp.producto_id = p.id
                     WHERE dp.pedido_id = ?";
    $stmt_detalles = $pdo->prepare($sql_detalles);
    $stmt_detalles->execute([$pedido_id]);
    $detalles = $stmt_detalles->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #<?php echo $pedido_id; ?> - Detalle</title>
    <link rel="stylesheet" href="css/estilos.css">
    
    <style>
        .detalle-container {
            max-width: 800px;
            margin: 2rem auto;
            background-color: var(--color-gris-carbon);
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid #333;
        }
        .detalle-header {
            border-bottom: 1px solid var(--color-verde-neon);
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .detalle-header h1 {
            margin: 0;
            font-size: 1.8rem;
            color: var(--color-blanco);
        }
        .fecha-pedido {
            color: var(--color-gris-claro);
        }
        
        /* Lista de productos */
        .item-pedido {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #333;
            align-items: center;
        }
        .item-pedido img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #444;
        }
        .item-info { flex: 1; }
        .item-info h3 { margin: 0 0 0.3rem 0; font-size: 1.1rem; color: var(--color-blanco); }
        .item-info span { font-size: 0.9rem; color: var(--color-gris-claro); }
        
        .item-total {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--color-verde-neon);
        }

        .resumen-total {
            margin-top: 2rem;
            text-align: right;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--color-blanco);
        }
        .resumen-total span { color: var(--color-verde-neon); }

        .btn-volver {
            display: inline-block;
            margin-top: 2rem;
            text-decoration: none;
            color: var(--color-gris-claro);
            border: 1px solid var(--color-gris-claro);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .btn-volver:hover {
            border-color: var(--color-verde-neon);
            color: var(--color-verde-neon);
        }
    </style>
</head>
<body class="page-content">

    <header>
        <nav>
            <div class="nav-links-main">
                <a href="index.php" class="logo">MiTienda</a>
                <a href="index.php">Inicio</a>
                <a href="categorias.php">Categorías</a>
            </div>
            <div class="nav-links-user">
                <a href="mi_cuenta.php">Mi Cuenta</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

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
                        <div class="item-total">
                            $<?php echo number_format($item['cantidad'] * $item['precio_unitario'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="resumen-total">
                Total Pagado: <span>$<?php echo number_format($pedido['total'], 2); ?></span>
            </div>

            <a href="mi_cuenta.php" class="btn-volver">← Volver a mis pedidos</a>

        </div>
    </main>

</body>
</html>