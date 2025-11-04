<?php
// Iniciar sesión
session_start();

// Incluir la conexión
require '../config/conexion.php';

// --- INICIO DE LÓGICA DE EDICIÓN ---

// 1. Verificar si nos han pasado un ID por la URL (método GET)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Si no hay ID, no podemos editar. Redirigir al panel.
    $_SESSION['mensaje'] = "Error: No se proporcionó un ID de producto.";
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: index.php');
    exit;
}

$producto_id = $_GET['id'];

// 2. Obtener los datos del producto de la BD
$producto = null;
try {
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

    // Si el ID no existe en la BD
    if (!$producto) {
        $_SESSION['mensaje'] = "Error: No se encontró ningún producto con ese ID.";
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php');
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error al cargar el producto: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: index.php');
    exit;
}

// --- FIN DE LÓGICA DE EDICIÓN ---
// Si llegamos aquí, la variable $producto tiene los datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto: <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 2rem; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h1, h2 { text-align: center; color: #333; }
        .form-container { border: 1px solid #ddd; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; background-color: #333; color: #fff; font-size: 1rem; text-decoration: none; }
        .btn-cancelar { background-color: #6c757d; margin-left: 0.5rem; }
        .imagen-actual { font-weight: bold; }
        .imagen-actual img { max-width: 100px; height: auto; border-radius: 4px; margin-top: 0.5rem; display: block; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Editar Producto</h1>

        <div class="form-container">
            <form action="accion_editar_producto.php" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                <input type="hidden" name="imagen_anterior" value="<?php echo htmlspecialchars($producto['imagen_url']); ?>">


                <div class="form-group">
                    <label for="nombre">Nombre del Producto</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($producto['categoria']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($producto['marca']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo</label>
                        <input type="text" id="tipo" name="tipo" value="<?php echo htmlspecialchars($producto['tipo']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="serie">Serie</label>
                        <input type="text" id="serie" name="serie" value="<?php echo htmlspecialchars($producto['serie']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="consumo">Consumo</label>
                        <input type="text" id="consumo" name="consumo" value="<?php echo htmlspecialchars($producto['consumo']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagen">Cambiar Imagen del Producto (Opcional)</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png, image/webp">
                    
                    <?php if ($producto['imagen_url']): ?>
                        <div class="imagen-actual">
                            Imagen Actual:
                            <img src="../uploads/<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="Imagen Actual">
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn">Actualizar Producto</button>
                <a href="index.php" class="btn btn-cancelar">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>