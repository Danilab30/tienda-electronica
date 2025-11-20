<?php
// Iniciar la sesión
session_start();
require 'config/conexion.php';

// --- 1. OBTENER EL ID DEL PRODUCTO ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$producto_id = $_GET['id'];

// --- 2. BUSCAR EL PRODUCTO EN LA BD ---
$producto = null;
try {
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        header('Location: index.php');
        exit;
    }

    // --- ¡NUEVO! 3. BUSCAR LAS RESEÑAS DE ESTE PRODUCTO ---
    $reseñas = [];
    // Hacemos un JOIN con la tabla 'usuarios' para traernos el nombre
    $sql_reseñas = "SELECT r.*, u.nombres 
                    FROM reseñas r
                    JOIN usuarios u ON r.usuario_id = u.id
                    WHERE r.producto_id = ?
                    ORDER BY r.fecha DESC";
    $stmt_reseñas = $pdo->prepare($sql_reseñas);
    $stmt_reseñas->execute([$producto_id]);
    $reseñas = $stmt_reseñas->fetchAll();


} catch (PDOException $e) {
    die("Error al cargar el producto: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - Tienda de Electrónica</title>
    <link rel="stylesheet" href="css/estilos.css">
    
    <style>
        .producto-detalle-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            max-width: 1100px;
            margin-top: 2rem;
        }
        .producto-imagen img { width: 100%; border-radius: 8px; background-color: var(--color-gris-carbon); border: 1px solid #333; }
        .producto-info h1 { color: var(--color-blanco); text-align: left; margin: 0 0 1rem 0; font-size: 2.5rem; border: none; }
        .producto-info .precio { color: var(--color-verde-neon); font-size: 2.5rem; font-weight: 700; margin-bottom: 1.5rem; }
        .producto-info .descripcion { color: var(--color-gris-claro); font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem; }
        
        .carrito-form { display: flex; gap: 1.5rem; align-items: center; }
        .form-group-cantidad { display: flex; align-items: center; gap: 0.5rem; }
        .form-group-cantidad label { font-weight: 600; font-size: 1.1rem; }
        .form-group-cantidad input { width: 60px; padding: 0.75rem; text-align: center; }
        
        .btn-agregar { background-color: var(--color-verde-neon); color: var(--color-negro); padding: 0.85rem 2rem; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; font-weight: 700; transition: all 0.3s ease; }
        .btn-agregar:hover { background-color: var(--color-blanco); }

        /* --- ¡NUEVO! Estilos para Reseñas --- */
        .seccion-reseñas {
            max-width: 1100px;
            margin: 4rem auto;
            padding-top: 2rem;
            border-top: 1px solid #333;
        }
        .seccion-reseñas h2 {
            color: var(--color-verde-neon);
            text-align: left;
            border: none;
        }

        /* Estrellas de Valoración (Promedio) */
        .valoracion-promedio { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
        .estrellas-promedio { color: #ffc107; font-size: 1.5rem; }
        .conteo-reseñas { color: var(--color-gris-claro); font-size: 1rem; }
        
        /* Formulario de Reseña */
        .form-reseña {
            background-color: var(--color-gris-carbon);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #333;
            margin-bottom: 2rem;
        }
        .form-reseña textarea { min-height: 100px; resize: vertical; }
        .form-reseña .btn-primary { width: auto; /* El botón no ocupa todo el ancho */ }

        /* Lista de Reseñas */
        .lista-reseñas .reseña {
            background-color: var(--color-gris-carbon);
            border: 1px solid #333;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .reseña-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
        .reseña-header strong { color: var(--color-blanco); font-size: 1.1rem; }
        .reseña-header .estrellas { color: #ffc107; }
        .reseña p { color: var(--color-gris-claro); margin: 0; }

        /* Mensaje para iniciar sesión */
        .reseña-login {
            background-color: var(--color-gris-carbon);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            border: 1px dashed var(--color-verde-neon);
        }
        .reseña-login a { color: var(--color-verde-neon); font-weight: 700; }

        @media (max-width: 768px) {
            .producto-detalle-container { grid-template-columns: 1fr; }
            .producto-info h1 { text-align: center; font-size: 2rem; }
            .producto-info .precio { text-align: center; }
            .carrito-form { flex-direction: column; }
            .btn-agregar { width: 100%; }
            .seccion-reseñas h2 { text-align: center; }
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
                    <a href="mi_cuenta.php">Mi Cuenta</a>
                <?php endif; ?>
                <a href="carrito.php">Carrito</a>
            </div>
        </nav>
    </header>

    <main>
        <?php
        if (isset($_SESSION['mensaje'])) {
            $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
            echo "<div class='mensaje $tipo_mensaje' style='max-width: 1100px; margin: 1rem auto;'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        }
        ?>

        <div class="producto-detalle-container">
            <div class="producto-imagen">
                <img src="uploads/<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            </div>
            
            <div class="producto-info">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                
                <div class="valoracion-promedio">
                    <span class="estrellas-promedio">
                        <?php
                        $promedio_redondeado = round($producto['valoracion_promedio']);
                        // Imprime 5 estrellas (llenas o vacías)
                        for ($i = 1; $i <= 5; $i++) {
                            echo ($i <= $promedio_redondeado) ? '★' : '☆';
                        }
                        ?>
                    </span>
                    <span class="conteo-reseñas">
                        (<?php echo htmlspecialchars($producto['valoracion_conteo']); ?> reseñas)
                    </span>
                </div>
                
                <div class="precio">$<?php echo htmlspecialchars($producto['precio']); ?></div>
                
                <p class="descripcion">
                    <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?>
                </p>
                
                <form action="accion_carrito.php" method="POST" class="carrito-form">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <div class="form-group-cantidad">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" class="form-group-input">
                    </div>
                    <button type="submit" class="btn-agregar">Agregar al carrito</button>
                </form>
            </div>
        </div>
        
        <div class="seccion-reseñas">
            <h2>Opiniones del Producto</h2>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="form-reseña">
                    <h3>Deja tu reseña</h3>
                    <form action="accion_reseña.php" method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                        
                        <div class="form-group">
                            <label for="valoracion">Valoración (Estrellas)</label>
                            <select name="valoracion" id="valoracion" class="form-group-input" required>
                                <option value="5">5 Estrellas ★★★★★</option>
                                <option value="4">4 Estrellas ★★★★☆</option>
                                <option value="3">3 Estrellas ★★★☆☆</option>
                                <option value="2">2 Estrellas ★★☆☆☆</option>
                                <option value="1">1 Estrella ★☆☆☆☆</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="comentario">Comentario</label>
                            <textarea name="comentario" id="comentario" placeholder="Escribe tu opinión..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Reseña</button>
                    </form>
                </div>

            <?php else: ?>
                <div class="reseña-login">
                    <p>Debes <a href="login.php">iniciar sesión</a> para dejar una reseña.</p>
                </div>
            <?php endif; ?>


            <div class="lista-reseñas">
                <?php if (empty($reseñas)): ?>
                    <p>Este producto aún no tiene reseñas. ¡Sé el primero!</p>
                <?php else: ?>
                    <?php foreach ($reseñas as $reseña): ?>
                        <div class="reseña">
                            <div class="reseña-header">
                                <strong><?php echo htmlspecialchars($reseña['nombres']); ?></strong>
                                <span class="estrellas">
                                    <?php
                                    // Imprime las estrellas de ESTA reseña
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo ($i <= $reseña['valoracion']) ? '★' : '☆';
                                    }
                                    ?>
                                </span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($reseña['comentario'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </main>

</body>
</html>