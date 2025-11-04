<?php
// 1. Iniciar sesión
session_start();

// 2. Incluir conexión
require 'config/conexion.php';

// --- 3. EL GUARDIA DE SEGURIDAD ---
// El usuario DEBE estar logueado para ver su carrito.
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Error: Debes iniciar sesión para ver tu carrito.";
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$items_carrito = [];
$subtotal = 0.00;

// --- 4. LÓGICA PARA LEER EL CARRITO ---
try {
    // Esta consulta es la más compleja hasta ahora:
    // 1. Selecciona todo del carrito (c.*) y campos específicos de productos (p.nombre, p.precio, p.imagen_url)
    // 2. Une (JOIN) carrito (c) con productos (p)
    // 3. DONDE el 'c.producto_id' sea igual al 'p.id'
    // 4. Y DONDE el 'c.usuario_id' sea el del usuario logueado.
    $sql = "SELECT c.*, p.nombre, p.precio, p.imagen_url, p.stock
            FROM carrito c
            JOIN productos p ON c.producto_id = p.id
            WHERE c.usuario_id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $items_carrito = $stmt->fetchAll();

    // 5. Calcular el subtotal
    foreach ($items_carrito as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
    }

} catch (PDOException $e) {
    // Manejar error
    die("Error al cargar el carrito: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compra - Tienda de Electrónica</title>
    <link rel="stylesheet" href="css/estilos.css">
    
    <style>
        .carrito-container {
            width: 100%;
            max-width: 1000px;
            margin: 2rem 0;
        }
        .carrito-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--color-verde-neon);
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .carrito-header h1 {
            color: var(--color-blanco);
            text-align: left;
            margin: 0;
            border: none;
        }
        .carrito-header a {
            color: var(--color-gris-claro);
            text-decoration: none;
        }
        .carrito-header a:hover {
            color: var(--color-verde-neon);
        }

        /* Estilos para cada item */
        .carrito-item {
            display: grid;
            grid-template-columns: 100px 1fr 100px 120px 50px; /* img | info | precio | cantidad | quitar */
            gap: 1.5rem;
            align-items: center;
            background-color: var(--color-gris-carbon);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #333;
        }
        .carrito-item-img img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            background: var(--color-negro);
        }
        .carrito-item-info h3 {
            margin: 0;
            color: var(--color-blanco);
            font-size: 1.2rem;
        }
        .carrito-item-info .stock {
            font-size: 0.9rem;
            color: var(--color-gris-claro);
        }
        .carrito-item-precio {
            font-size: 1.2rem;
            font-weight: 700;
        }
        .carrito-item-cantidad input {
            width: 60px;
            text-align: center;
            padding: 0.5rem;
        }
        .carrito-item-quitar a {
            color: #dc3545; /* Rojo */
            text-decoration: none;
            font-weight: 700;
        }
        .carrito-item-quitar a:hover {
            text-decoration: underline;
        }

        /* Resumen del Pedido */
        .carrito-resumen {
            background-color: var(--color-gris-carbon);
            border: 1px solid var(--color-verde-neon);
            padding: 2rem;
            border-radius: 8px;
            margin-top: 2rem;
            width: 300px;
            margin-left: auto; /* Alinea a la derecha */
        }
        .resumen-linea {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .resumen-linea.total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-verde-neon);
            border-top: 1px solid #333;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .btn-pagar { /* Botón de pagar */
            width: 100%;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .carrito-item {
                grid-template-columns: 80px 1fr; /* 2 columnas: img | info */
                grid-template-rows: auto auto auto auto;
                gap: 0.5rem 1rem;
            }
            .carrito-item-img {
                grid-row: 1 / span 4;
            }
            .carrito-item-info {
                grid-column: 2 / 3;
            }
            .carrito-item-precio,
            .carrito-item-cantidad,
            .carrito-item-quitar {
                grid-column: 2 / 3;
                text-align: left;
            }
            .carrito-resumen {
                width: 100%;
                margin-left: 0;
                box-sizing: border-box;
            }
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
                    <a href="#">Mi Cuenta</a>
                    <a href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php">Cuenta</a>
                <?php endif; ?>
                <a href="carrito.php">Carrito</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="carrito-container">
            <div class="carrito-header">
                <h1>Carrito de Compra</h1>
                <a href="categorias.php">← Seguir comprando</a>
            </div>

            <div class="carrito-lista">
                <?php if (empty($items_carrito)): ?>
                    <div class="reseña-login" style="text-align: center;"> <p>Tu carrito está vacío.</p>
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
                            <div class="carrito-item-precio">
                                $<?php echo htmlspecialchars($item['precio']); ?>
                            </div>
                            <div class="carrito-item-cantidad">
                                <form action="accion_actualizar_carrito.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" max="<?php echo $item['stock']; ?>" onchange="this.form.submit()">
                                </form>
                            </div>
                            <div class="carrito-item-quitar">
                                <a href="accion_quitar_carrito.php?id=<?php echo $item['id']; ?>" onclick="return confirm('¿Quitar este producto del carrito?');">Quitar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($items_carrito)): ?>
                <div class="carrito-resumen">
                    <div class="resumen-linea">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="resumen-linea">
                        <span>Envío:</span>
                        <span>Gratis</span> </div>
                    <div class="resumen-linea total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn btn-primary btn-pagar">Ir a pagar</a>
                </div>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>