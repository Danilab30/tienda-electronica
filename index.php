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
    
    <style>
        .productos-grid {
            display: grid;
            /* 3 columnas en desktop, 2 en tablet, 1 en móvil */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .producto-card {
            background-color: var(--color-gris-carbon);
            border: 1px solid #333;
            border-radius: 8px;
            overflow: hidden; 
            text-decoration: none;
            color: var(--color-gris-claro);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 255, 132, 0.15);
            border-color: var(--color-verde-neon);
        }
        .producto-card img {
            width: 100%;
            height: 250px;
            object-fit: cover; 
            background-color: var(--color-negro);
        }
        .producto-info {
            padding: 1.5rem;
        }
        .producto-info h3 {
            color: var(--color-blanco);
            margin: 0 0 0.5rem 0;
            font-size: 1.25rem;
            /* Para cortar texto muy largo */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .producto-precio {
            color: var(--color-verde-neon);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .btn-ver {
            background-color: var(--color-verde-neon);
            color: var(--color-negro);
            padding: 0.6rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
        }
        .btn-ver:hover {
            background-color: var(--color-blanco);
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
    
</body>
</html>