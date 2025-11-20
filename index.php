<?php
// Iniciar la sesión
session_start();

// Incluir la conexión
require 'config/conexion.php';

// --- LÓGICA PARA LEER PRODUCTOS ---
// Vamos a buscar los 3 productos más recientes
$productos_recientes = [];
try {
    // Consulta SQL para traer 3 productos, ordenados del más nuevo al más viejo
    $sql = "SELECT * FROM productos WHERE stock > 0 ORDER BY id DESC LIMIT 3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos_recientes = $stmt->fetchAll();

} catch (PDOException $e) {
    // Manejar error
    echo "Error al cargar productos: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Tienda de Electrónica</title>
    
    <link rel="stylesheet" href="css/estilos.css">
    
    
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
                    <a href="mi_cuenta.php">Mi Cuenta</a> 
                    <a href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php">Cuenta</a>
                <?php endif; ?>
                
                <a href="carrito.php">Carrito</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="hero">
            <h1>Componentes de Alta Gama</h1>
            <p>Arma la PC de tus sueños con la última tecnología en hardware.</p>
            <a href="categorias.php" class="btn-comprar">Ver Todas las Categorías</a>
        </div>

        <h2>Productos Recientes</h2>

        <div class="productos-grid">
            
            <?php if (empty($productos_recientes)): ?>
                <p>No hay productos disponibles por el momento.</p>
            <?php else: ?>
                <?php foreach ($productos_recientes as $producto): ?>
                    
                    <a href="producto.php?id=<?php echo $producto['id']; ?>" class="producto-card">
                        
                        <?php if (!empty($producto['imagen_url'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x250.png?text=Sin+Imagen" alt="Sin Imagen">
                        <?php endif; ?>
                        
                        <div class="producto-info">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <div class="producto-precio">$<?php echo htmlspecialchars($producto['precio']); ?></div>
                            <span class="btn-ver">Ver Detalles</span>
                        </div>
                    </a>

                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>

    <?php
    // --- ¡BLOQUE NUEVO PARA MOSTRAR MENSAJES! ---
    if (isset($_SESSION['mensaje'])) {
        $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
        // Añadimos un estilo para que se alinee con el contenido
        echo "<div class='mensaje $tipo_mensaje' style='max-width: 1200px; margin: 1rem auto; width: 90%;'>";
        echo htmlspecialchars($_SESSION['mensaje']);
        echo "</div>";

        // Borrar el mensaje después de mostrarlo
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
    }
    ?>
    
</body>
</html>