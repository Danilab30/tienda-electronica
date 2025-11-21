<?php
// (session_start lo hace el header)
require 'config/conexion.php';

// --- LÓGICA DE FILTROS ---
$marcas = [];
$categorias = [];
$tipos = [];
$series = [];
$condiciones = [];
$parametros = [];

// 2a. Obtener opciones
try {
    $marcas = $pdo->query("SELECT DISTINCT marca FROM productos WHERE marca IS NOT NULL AND marca != '' ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);
    $categorias = $pdo->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
    $tipos = $pdo->query("SELECT DISTINCT tipo FROM productos WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
    $series = $pdo->query("SELECT DISTINCT serie FROM productos WHERE serie IS NOT NULL AND serie != '' ORDER BY serie")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error opciones: " . $e->getMessage());
}

// 2b. Construir SQL
$sql = "SELECT * FROM productos WHERE stock > 0";

// Filtros
if (!empty($_GET['q'])) { $condiciones[] = "nombre LIKE ?"; $parametros[] = '%' . $_GET['q'] . '%'; }
if (!empty($_GET['marca'])) { $condiciones[] = "marca = ?"; $parametros[] = $_GET['marca']; }
if (!empty($_GET['categoria'])) { $condiciones[] = "categoria = ?"; $parametros[] = $_GET['categoria']; }
if (!empty($_GET['tipo'])) { $condiciones[] = "tipo = ?"; $parametros[] = $_GET['tipo']; }
if (!empty($_GET['serie'])) { $condiciones[] = "serie = ?"; $parametros[] = $_GET['serie']; }
if (!empty($_GET['precio_min'])) { $condiciones[] = "precio >= ?"; $parametros[] = $_GET['precio_min']; }
if (!empty($_GET['precio_max'])) { $condiciones[] = "precio <= ?"; $parametros[] = $_GET['precio_max']; }

if (count($condiciones) > 0) {
    $sql .= " AND " . implode(" AND ", $condiciones);
}

// Orden
$orden_sql = ' ORDER BY nombre ASC';
if (!empty($_GET['orden'])) {
    if ($_GET['orden'] == 'precio_asc') $orden_sql = ' ORDER BY precio ASC';
    elseif ($_GET['orden'] == 'precio_desc') $orden_sql = ' ORDER BY precio DESC';
}
$sql .= $orden_sql;

// Ejecutar
$productos = [];
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<?php include 'includes/header.php'; ?>

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
                                <?php if (isset($producto['en_oferta']) && $producto['en_oferta'] == 1): ?>
                                    <div class="badge-oferta">OFERTA</div>
                                <?php endif; ?>

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
            </div>

        </div>
    </main>

<?php include 'includes/footer.php'; ?>