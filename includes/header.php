<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- LÓGICA DEL CONTADOR DEL CARRITO ---
$cantidad_carrito = 0;
if (isset($_SESSION['usuario_id'])) {
    // Si hay usuario, conectamos y contamos
    // Usamos 'require_once' para no chocar si la página ya lo incluyó
    require_once __DIR__ . '/../config/conexion.php'; 
    
    try {
        // Sumamos la columna 'cantidad' para saber el total de artículos
        $sql_count = "SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?";
        $stmt_count = $pdo->prepare($sql_count);
        $stmt_count->execute([$_SESSION['usuario_id']]);
        $resultado = $stmt_count->fetch();
        
        // Si hay resultado, lo asignamos. Si es null, es 0.
        $cantidad_carrito = $resultado['total'] ? $resultado['total'] : 0;
    } catch (PDOException $e) {
        // Si falla, no rompemos la página, solo mostramos 0
        $cantidad_carrito = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberGipsy - Tienda de Electrónica</title>
    <link rel="stylesheet" href="css/estilos.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-content">

    <header>
        <nav>
            <div class="nav-left-section">
                <a href="index.php" class="logo-container">
                    <img src="img/Logo.png" alt="Logo" class="brand-icon-big">
                    <img src="img/Nombre.png" alt="CyberGipsy" class="brand-text-big">
                </a>

                <div class="nav-links-main">
                    <a href="index.php">Inicio</a>
                    <a href="categorias.php">Categorías</a>
                    <a href="ofertas.php">Ofertas</a>
                </div>
            </div>
            
            <form action="categorias.php" method="GET" class="nav-search">
                <input type="search" name="q" placeholder="Buscar hardware..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <div class="nav-icons">
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    
                    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin'): ?>
                        <a href="admin/index.php" class="icon-link admin-link" title="Panel Admin">
                            <i class="fa-solid fa-gear"></i>
                        </a>
                    <?php endif; ?>

                    <div class="user-menu">
                        <a href="mi_cuenta.php" class="icon-link">
                            <i class="fa-regular fa-user"></i>
                            <span>Hola, <?php echo htmlspecialchars(explode(' ', $_SESSION['usuario_nombres'])[0]); ?></span>
                        </a>
                        <a href="logout.php" class="logout-link" title="Salir"><i class="fa-solid fa-right-from-bracket"></i></a>
                    </div>

                <?php else: ?>
                    <a href="login.php" class="icon-link">
                        <i class="fa-regular fa-user"></i>
                        <span>Entrar</span>
                    </a>
                <?php endif; ?>
                
                <a href="carrito.php" class="icon-link cart-container">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if ($cantidad_carrito > 0): ?>
                        <span class="cart-badge"><?php echo $cantidad_carrito; ?></span>
                    <?php endif; ?>
                </a>

            </div>
        </nav>
    </header>