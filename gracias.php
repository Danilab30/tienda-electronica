<?php
// 1. Iniciar sesión
session_start();

// 2. Verificamos si vienen de un pago exitoso (revisando la sesión)
if (!isset($_SESSION['ultimo_pedido_id'])) {
    // Si intentan entrar a 'gracias.php' directo, los mandamos al inicio
    header('Location: index.php');
    exit;
}

// 3. Obtenemos el ID del pedido y lo limpiamos de la sesión
// (Esto evita que puedan recargar la página y ver el mensaje de nuevo)
$pedido_id = $_SESSION['ultimo_pedido_id'];
unset($_SESSION['ultimo_pedido_id']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu compra! - Tienda de Electrónica</title>
    <link rel="stylesheet" href="css/estilos.css">
    
    <style>
        .gracias-container {
            max-width: 700px;
            margin: 4rem auto;
            background-color: var(--color-gris-carbon);
            padding: 3rem;
            border-radius: 8px;
            border: 1px solid var(--color-verde-neon);
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 255, 132, 0.1);
        }
        .gracias-container h1 {
            color: var(--color-verde-neon);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            border: none;
        }
        .gracias-container p {
            font-size: 1.2rem;
            color: var(--color-gris-claro);
            line-height: 1.6;
        }
        .numero-pedido {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-blanco);
            background-color: var(--color-negro);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            display: inline-block;
            margin: 1.5rem 0;
        }
        .btn-volver {
            width: auto;
            padding-left: 2rem;
            padding-right: 2rem;
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
        </nav>
    </header>

    <main>
        <div class="gracias-container">
            <h1>¡Pago Exitoso!</h1>
            <p>Muchas gracias por tu compra, carnal. Hemos recibido tu pago y tu pedido está siendo procesado.</p>
            
            <p>Tu número de pedido es:</p>
            <div class="numero-pedido">
                #<?php echo htmlspecialchars($pedido_id); ?>
            </div>
            
            <p>Recibirás una confirmación por correo (eventualmente ).</p>
            
            <a href="index.php" class="btn btn-primary btn-volver">Seguir Comprando</a>
        </div>
    </main>

</body>
</html>