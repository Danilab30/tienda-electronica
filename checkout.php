<?php
// 1. Iniciar sesión
session_start();

// 2. Incluir conexión
require 'config/conexion.php';

// --- 3. GUARDIA DE SEGURIDAD 1: ¿ESTÁ LOGUEADO? ---
// Si no hay 'usuario_id' en la sesión, lo mandamos al login
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Error: Debes iniciar sesión para proceder al pago.";
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: login.php');
    exit;
}

// Si está logueado, guardamos su ID
$usuario_id = $_SESSION['usuario_id'];
$items_carrito = [];
$subtotal = 0.00;

// --- 4. LÓGICA PARA LEER EL CARRITO (Igual que en carrito.php) ---
try {
    // Hacemos el JOIN para traer la info de los productos
    $sql = "SELECT c.*, p.nombre, p.precio, p.imagen_url
            FROM carrito c
            JOIN productos p ON c.producto_id = p.id
            WHERE c.usuario_id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $items_carrito = $stmt->fetchAll();

    // --- 5. GUARDIA DE SEGURIDAD 2: ¿EL CARRITO ESTÁ VACÍO? ---
    if (empty($items_carrito)) {
        // Si no hay items, no tiene sentido estar aquí
        $_SESSION['mensaje'] = "Tu carrito está vacío, no puedes proceder al pago.";
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: carrito.php');
        exit;
    }

    // 6. Calcular el subtotal
    foreach ($items_carrito as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
    }

} catch (PDOException $e) {
    // Si algo falla, detenemos la página
    die("Error al cargar la información del carrito: ". $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout (Pagar) - Tienda de Electrónica</title>
    <link rel="stylesheet" href="css/estilos.css">
    
    <style>
        .checkout-container {
            display: grid;
            /* 2/3 para el formulario, 1/3 para el resumen */
            grid-template-columns: 2fr 1fr; 
            gap: 3rem;
            max-width: 1100px;
            margin: 2rem 0;
            width: 90%;
        }
        
        /* Columna Izquierda: "Simulación" */
        .checkout-form {
            background-color: var(--color-gris-carbon);
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid #333;
        }
        .checkout-form h2 {
            text-align: left;
            margin-top: 0;
            border: none;
            color: var(--color-blanco);
        }
        .checkout-form p {
            color: var(--color-gris-claro);
            line-height: 1.6;
        }
        .btn-simular-pago {
            width: 100%;
        }

        /* Columna Derecha: Resumen (reutilizamos estilos de carrito.php) */
        .checkout-resumen {
            background-color: var(--color-gris-carbon);
            border: 1px solid var(--color-verde-neon);
            padding: 2rem;
            border-radius: 8px;
            /* Para que no se estire si la otra columna es larga */
            height: fit-content; 
        }
        .checkout-resumen h2 {
            text-align: left;
            margin-top: 0;
            border: none;
            color: var(--color-verde-neon);
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
        
        /* Lista de items en el resumen */
        .resumen-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
        }
        .resumen-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .resumen-item-info { flex: 1; }
        .resumen-item-info h3 { margin: 0; font-size: 1rem; color: var(--color-blanco); }
        .resumen-item-info span { font-size: 0.9rem; color: var(--color-gris-claro); }
        .resumen-item-precio { font-weight: 700; }

        /* Responsive para móvil */
        @media (max-width: 768px) {
            .checkout-container {
                /* Una columna en móvil */
                grid-template-columns: 1fr; 
            }
            .checkout-resumen {
                /* El resumen va primero en móvil */
                grid-row: 1; 
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
        <?php
        if (isset($_SESSION['mensaje'])) {
            $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
            echo "<div class='mensaje $tipo_mensaje' style='max-width: 1100px; margin: 1rem auto; width: 90%;'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        }
        ?>

        <div class="checkout-container">
            
            <div class="checkout-form">
                <h2>Simulación de Pago</h2>
                <p>En un sitio real, aquí es donde iría el formulario para la tarjeta de crédito (integrado con Stripe) o el botón de PayPal.</p>
                <p>Para este proyecto, haremos clic en el botón de abajo para simular que el pago fue aprobado.</p>
                
                <form action="accion_pago.php" method="POST">
                    
                    <input type="hidden" name="total_pagado" value="<?php echo $subtotal; ?>">
                    
                    <button type="submit" class="btn btn-primary btn-simular-pago">Simular Pago Exitoso</button>
                </form>
            </div>

            <div class="checkout-resumen">
                <h2>Resumen de tu Pedido</h2>

                <div class="resumen-lista-items">
                    <?php foreach ($items_carrito as $item): ?>
                        <div class="resumen-item">
                            <img src="uploads/<?php echo htmlspecialchars($item['imagen_url']); ?>" alt="">
                            <div class="resumen-item-info">
                                <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                                <span>Cantidad: <?php echo htmlspecialchars($item['cantidad']); ?></span>
                            </div>
                            <div class="resumen-item-precio">
                                $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="resumen-linea">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="resumen-linea">
                    <span>Envío:</span>
                    <span>Gratis</span>
                </div>
                <div class="resumen-linea total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
            </div>

        </div>
    </main>

</body>
</html>