<?php
require_once 'verificar_admin.php'; // ¡GUARDIA AQUÍ!

// (El guardia ya inicia la sesión)

// Incluir la conexión a la BD
require '../config/conexion.php';

// --- INICIO DE LÓGICA DE EDICIÓN ---

// 1. Verificar si nos han pasado un ID por la URL (método GET)
if (!isset($_GET['id']) || empty($_GET['id'])) {
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto: <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body class="page-content">

    <div class="container admin-container">
        <h1>Editar Producto</h1>

        <?php
        if (isset($_SESSION['mensaje'])) {
            $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
            echo "<div class='mensaje $tipo_mensaje'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        }
        ?>

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

                <div class="form-group" style="display: flex; align-items: center; gap: 10px; background: #222; padding: 10px; border-radius: 4px; border: 1px solid #444; margin-top: 1rem;">
                    <input type="checkbox" id="en_oferta" name="en_oferta" value="1" style="width: 20px; height: 20px; accent-color: #00FF84;"
                        <?php echo (isset($producto['en_oferta']) && $producto['en_oferta'] == 1) ? 'checked' : ''; ?>>
                    
                    <label for="en_oferta" style="margin: 0; color: #fff; cursor: pointer; font-weight: bold;">¡Marcar este producto como OFERTA!</label>
                </div>

                <button type="submit" class="btn btn-primary" style="background-color: #ffc107; color: #333;">Actualizar Producto</button>
                <a href="index.php" class="btn btn-secondary" style="text-align:center; display:block; margin-top:0.5rem;">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>