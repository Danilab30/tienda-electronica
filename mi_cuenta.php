<?php
// 1. Iniciar sesión
session_start();
require 'config/conexion.php';

// 2. GUARDIA: ¿Está logueado?
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Debes iniciar sesión para ver tu cuenta.";
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$pedidos = [];

// 3. OBTENER PEDIDOS DEL USUARIO
try {
    // Traemos todos los pedidos de ESTE usuario, ordenados del más reciente al más antiguo
    $sql = "SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $pedidos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error al cargar los pedidos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Tienda de Electrónica</title>
    <link rel="stylesheet" href="css/estilos.css">
    
    <style>
        .cuenta-container {
            max-width: 1000px;
            margin: 2rem auto;
            width: 90%;
        }
        .cuenta-header {
            border-bottom: 1px solid var(--color-verde-neon);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
        }
        .cuenta-header h1 {
            text-align: left;
            color: var(--color-blanco);
            margin: 0;
            border: none;
        }
        
        /* Estilos de la tabla de pedidos */
        .tabla-pedidos {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--color-gris-carbon);
            border-radius: 8px;
            overflow: hidden;
        }
        .tabla-pedidos th, .tabla-pedidos td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        .tabla-pedidos th {
            background-color: #111;
            color: var(--color-verde-neon);
            font-weight: 700;
        }
        .tabla-pedidos tr:hover {
            background-color: #2a2a2a;
        }
        .estado-pagado {
            color: var(--color-verde-neon);
            font-weight: bold;
            border: 1px solid var(--color-verde-neon);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .btn-ver-pedido {
            background-color: transparent;
            border: 1px solid var(--color-gris-claro);
            color: var(--color-gris-claro);
            padding: 0.4rem 0.8rem;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .btn-ver-pedido:hover {
            border-color: var(--color-verde-neon);
            color: var(--color-verde-neon);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .tabla-pedidos { display: block; overflow-x: auto; }
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
                <a href="#">Ofertas</a>
            </div>
            <div class="nav-links-user">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span>¡Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombres']); ?>!</span>
                    <a href="mi_cuenta.php" style="color: var(--color-verde-neon);">Mi Cuenta</a>
                    <a href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php">Cuenta</a>
                <?php endif; ?>
                <a href="carrito.php">Carrito</a>
            </div>
        </nav>
    </header>

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
                            <th>ID Pedido</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>#<?php echo $pedido['id']; ?></td>
                                <td><?php echo date("d/m/Y", strtotime($pedido['fecha_pedido'])); ?></td>
                                <td style="font-weight: bold;">$<?php echo number_format($pedido['total'], 2); ?></td>
                                <td>
                                    <span class="estado-pagado">
                                        <?php echo strtoupper($pedido['estado_pago']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="ver_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn-ver-pedido">Ver Detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>