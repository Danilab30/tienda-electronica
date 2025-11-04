<?php
// Iniciar sesión (¡siempre primero!)
session_start();

// Incluir la conexión a la BD
require '../config/conexion.php';

// --- INICIO DEL READ (R) ---
// Variable para guardar los productos
$productos = [];
try {
    // 1. Preparar la consulta SQL para LEER todos los productos
    $sql = "SELECT * FROM productos ORDER BY id DESC"; // 'ORDER BY' los muestra del más nuevo al más viejo
    $stmt = $pdo->prepare($sql);
    
    // 2. Ejecutar la consulta
    $stmt->execute();
    
    // 3. Obtener todos los resultados
    $productos = $stmt->fetchAll(); // fetchAll() nos da un array con todos los productos

} catch (PDOException $e) {
    // Manejar el error si la consulta falla
    $_SESSION['mensaje'] = "Error al cargar los productos: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'error';
    // (No usamos 'exit' para que la página pueda cargar de todos modos)
}
// --- FIN DEL READ (R) ---

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    
    <link rel="stylesheet" href="../css/estilos.css">

</head>

<body class="page-content">

    <div class="container admin-container" style="max-width: 1200px;">
        <h1>Panel de Administrador</h1>

        <?php
        // Mostrar mensajes de error o éxito
        if (isset($_SESSION['mensaje'])) {
            $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
            echo "<div class='mensaje $tipo_mensaje'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
            
            // Borrar el mensaje después de mostrarlo
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        }
        ?>

        <div class="form-container">
            <h2>Añadir Nuevo Producto (Create)</h2>
            <form action="accion_crear_producto.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="nombre">Nombre del Producto</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion"></textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="number" id="precio" name="precio" step="0.01" required placeholder="Ej: 1499.99">
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" required placeholder="Ej: 50">
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <input type="text" id="categoria" name="categoria" placeholder="Ej: Procesadores">
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" placeholder="Ej: Intel">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo</label>
                        <input type="text" id="tipo" name="tipo" placeholder="Ej: Gamer">
                    </div>
                    <div class="form-group">
                        <label for="serie">Serie</label>
                        <input type="text" id="serie" name="serie" placeholder="Ej: Core i9">
                    </div>
                    <div class="form-group">
                        <label for="consumo">Consumo</label>
                        <input type="text" id="consumo" name="consumo" placeholder="Ej: 95W">
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagen">Imagen del Producto</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png, image/webp">
                </div>

                <button type="submit" class="btn btn-primary" style="background-color: #ffc107; color: #333;">Guardar Producto</button>
            </form>
        </div>

        <div class="list-container">
            <h2>Inventario Actual (Read)</h2>
            <div style="overflow-x: auto;"> 
                <table class="tabla-inventario">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="8">No hay productos registrados todavía.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['id']); ?></td>
                                    <td>
                                        <?php if ($producto['imagen_url']): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="Imagen">
                                        <?php else: ?>
                                            <span>N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td>$<?php echo htmlspecialchars($producto['precio']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['marca']); ?></td>
                                    <td class="acciones">
                                        <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-editar">Editar</a>
                                        <a href="accion_borrar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-borrar" onclick="return confirm('¿Estás seguro de que quieres borrar este producto?');">Borrar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>