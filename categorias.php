<?php
// 1. Iniciar sesión y conexión
session_start();
require 'config/conexion.php';

// --- 2. LÓGICA DE FILTROS ---
$marcas = [];
$categorias = [];
$tipos = [];
$series = [];

$condiciones = [];
$parametros = [];

// 2a. Obtener todas las opciones para los filtros
try {
    $marcas = $pdo->query("SELECT DISTINCT marca FROM productos WHERE marca IS NOT NULL AND marca != '' ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);
    $categorias = $pdo->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
    $tipos = $pdo->query("SELECT DISTINCT tipo FROM productos WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
    $series = $pdo->query("SELECT DISTINCT serie FROM productos WHERE serie IS NOT NULL AND serie != '' ORDER BY serie")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error al cargar opciones de filtro: " . $e->getMessage());
}

// 2b. Construir la consulta SQL principal
$sql = "SELECT * FROM productos WHERE stock > 0";

// --- REVISAMOS TODOS LOS FILTROS POSIBLES ---

// Filtro de Búsqueda (q = query)
if (!empty($_GET['q'])) {
    $condiciones[] = "nombre LIKE ?";
    $parametros[] = '%' . $_GET['q'] . '%';
}
// Filtro de Marca
if (!empty($_GET['marca'])) {
    $condiciones[] = "marca = ?";
    $parametros[] = $_GET['marca'];
}
// Filtro de Categoría
if (!empty($_GET['categoria'])) {
    $condiciones[] = "categoria = ?";
    $parametros[] = $_GET['categoria'];
}
// Filtro de Tipo
if (!empty($_GET['tipo'])) {
    $condiciones[] = "tipo = ?";
    $parametros[] = $_GET['tipo'];
}
// Filtro de Serie
if (!empty($_GET['serie'])) {
    $condiciones[] = "serie = ?";
    $parametros[] = $_GET['serie'];
}
// Filtro de Precio Mínimo
if (!empty($_GET['precio_min'])) {
    $condiciones[] = "precio >= ?";
    $parametros[] = $_GET['precio_min'];
}
// Filtro de Precio Máximo
if (!empty($_GET['precio_max'])) {
    $condiciones[] = "precio <= ?";
    $parametros[] = $_GET['precio_max'];
}

// 2c. Unir todas las condiciones a la consulta SQL
if (count($condiciones) > 0) {
    $sql .= " AND " . implode(" AND ", $condiciones);
}

// 2d. Añadir orden
$orden_sql = ' ORDER BY nombre ASC'; // Por defecto
if (!empty($_GET['orden'])) {
    if ($_GET['orden'] == 'precio_asc') {
        $orden_sql = ' ORDER BY precio ASC';
    } elseif ($_GET['orden'] == 'precio_desc') {
        $orden_sql = ' ORDER BY precio DESC';
    }
}
$sql .= $orden_sql;

// --- 3. EJECUTAR LA CONSULTA ---
$productos = [];
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al buscar productos: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Tienda de Electrónica</title>
    <link rel="stylesheet" href="css/estilos.css">
    
    <style>
        .categorias-container {
            display: grid;
            grid-template-columns: 250px 1fr; /* Sidebar | Grid */
            gap: 2rem;
            max-width: 1200px;
            margin: 2rem auto;
            width: 90%;
        }

        /* --- Sidebar de Filtros --- */
        .filtro-sidebar {
            background-color: var(--color-gris-carbon);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #333;
            height: fit-content; /* Se ajusta al contenido */
        }
        .filtro-sidebar h2 {
            text-align: left;
            margin-top: 0;
            border: none;
            color: var(--color-verde-neon);
            font-size: 1.5rem;
        }
        .filtro-grupo { margin-bottom: 1.5rem; }
        .filtro-grupo label {
            font-weight: 700;
            color: var(--color-blanco);
            display: block;
            margin-bottom: 0.5rem;
        }
        .filtro-grupo select,
        .filtro-grupo input[type="number"] {
            width: 100%;
            background-color: var(--color-negro);
        }
        .filtro-grupo-precio {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }
        .btn-filtrar { width: 100%; }
        
        /* ¡AQUÍ ESTÁ EL BOTÓN DE LIMPIAR! */
        .btn-limpiar {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            background-color: #333; /* Gris oscuro */
            color: var(--color-gris-claro);
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-limpiar:hover {
            background-color: #444;
            color: var(--color-blanco);
        }

        /* Contenedor de la cuadrícula */
        .productos-grid-container {
            width: 100%;
        }
        .productos-grid-container h2 {
            text-align: left;
            margin-top: 0;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #333;
        }

        /* Estilos para el buscador del header */
        .nav-search input[type="search"] {
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            border: 1px solid var(--color-gris-claro);
            background-color: var(--color-negro);
            color: var(--color-blanco);
            min-width: 250px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .categorias-container {
                grid-template-columns: 1fr; /* Una columna en móvil/tablet */
            }
            .filtro-sidebar {
                grid-row: 1; /* El filtro va primero */
            }
            .nav-search { display: none; } /* Ocultamos buscador en nav */
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
            
            <form action="categorias.php" method="GET" class="nav-search">
                <input type="search" name="q" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
            </form>

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
        <div class="categorias-container">
            
            <aside class="filtro-sidebar">
                <h2>Filtrar</h2>
                <form action="categorias.php" method="GET">
                    
                    <?php if (!empty($_GET['q'])): ?>
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($_GET['q']); ?>">
                    <?php endif; ?>

                    <div class="filtro-grupo">
                        <label for="marca">Marca</label>
                        <select id="marca" name="marca">
                            <option value="">Todas</option>
                            <?php foreach ($marcas as $marca): ?>
                                <option value="<?php echo htmlspecialchars($marca); ?>" <?php echo (isset($_GET['marca']) && $_GET['marca'] == $marca) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($marca); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label for="categoria">Categoría</label>
                        <select id="categoria" name="categoria">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo htmlspecialchars($categoria); ?>" <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $categoria) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filtro-grupo">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <?php foreach ($tipos as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == $tipo) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label for="serie">Serie</label>
                        <select id="serie" name="serie">
                            <option value="">Todas</option>
                            <?php foreach ($series as $serie): ?>
                                <option value="<?php echo htmlspecialchars($serie); ?>" <?php echo (isset($_GET['serie']) && $_GET['serie'] == $serie) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($serie); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label>Precio</label>
                        <div class="filtro-grupo-precio">
                            <input type="number" name="precio_min" placeholder="Mín" min="0" value="<?php echo htmlspecialchars($_GET['precio_min'] ?? ''); ?>">
                            <input type="number" name="precio_max" placeholder="Máx" min="0" value="<?php echo htmlspecialchars($_GET['precio_max'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="filtro-grupo">
                        <label for="orden">Ordenar por</label>
                        <select id="orden" name="orden">
                            <option value="nombre_asc" <?php echo (isset($_GET['orden']) && $_GET['orden'] == 'nombre_asc') ? 'selected' : ''; ?>>Relevancia</option>
                            <option value="precio_asc" <?php echo (isset($_GET['orden']) && $_GET['orden'] == 'precio_asc') ? 'selected' : ''; ?>>Precio (Menor a Mayor)</option>
                            <option value="precio_desc" <?php echo (isset($_GET['orden']) && $_GET['orden'] == 'precio_desc') ? 'selected' : ''; ?>>Precio (Mayor a Menor)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-filtrar">Filtrar</button>
                    <a href="categorias.php" class="btn-limpiar">Limpiar filtros</a>
                </form>
            </aside>

            <div class="productos-grid-container">
                <h2>Resultados (<?php echo count($productos); ?>)</h2>
                
                <div class="productos-grid">
                    <?php if (empty($productos)): ?>
                        <div class="reseña-login" style="grid-column: 1 / -1; text-align: center;"> <p>No se encontraron productos que coincidan con tu búsqueda.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                            <a href="producto.php?id=<?php echo $producto['id']; ?>" class="producto-card">
                                <?php if (!empty($producto['imagen_url'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <?php else: ?>
                                    <img src="httpsa://via.placeholder.com/300x250.png?text=Sin+Imagen" alt="Sin Imagen">
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
            </div>

        </div>
    </main>

</body>
</html>