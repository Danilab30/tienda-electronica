<?php
// 1. Iniciar la sesión
session_start();

// 2. Incluir la conexión
require '../config/conexion.php';

// 3. Verificar si se recibió un ID por GET
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = "Error: No se especificó el producto a borrar.";
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: index.php');
    exit;
}

$id_producto = $_GET['id'];

try {
    // --- PASO 1: Borrar la imagen del servidor ---
    
    // Primero, necesitamos saber el nombre de la imagen para borrarla
    $sql_select = "SELECT imagen_url FROM productos WHERE id = ?";
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->execute([$id_producto]);
    $producto = $stmt_select->fetch();

    if ($producto && $producto['imagen_url']) {
        // Si el producto existe y tiene una imagen...
        $ruta_imagen = '../uploads/' . $producto['imagen_url'];
        
        // Verificar que el archivo exista antes de intentar borrarlo
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen); // ¡Borra el archivo de la carpeta 'uploads'!
        }
    }

    // --- PASO 2: Borrar el producto de la base de datos ---
    
    // Preparamos la consulta SQL para BORRAR (DELETE)
    $sql_delete = "DELETE FROM productos WHERE id = ?";
    $stmt_delete = $pdo->prepare($sql_delete);
    
    // Ejecutamos la consulta
    $stmt_delete->execute([$id_producto]);

    // 4. Redirigir de vuelta con mensaje de éxito
    $_SESSION['mensaje'] = "Producto (ID: $id_producto) borrado exitosamente.";
    $_SESSION['tipo_mensaje'] = 'exito';
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    // 5. Manejar errores
    // El error 1451 es común (Integridad referencial):
    // Significa que no puedes borrar un producto si ya está en un pedido de un cliente.
    if ($e->getCode() == '23000') {
         $_SESSION['mensaje'] = "Error: No se puede borrar el producto (ID: $id_producto) porque está asociado a un pedido existente.";
    } else {
         $_SESSION['mensaje'] = "Error al borrar el producto: " . $e->getMessage();
    }
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: index.php');
    exit;
}
?>