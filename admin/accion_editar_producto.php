<?php
require_once 'verificar_admin.php'; // ¡GUARDIA DE SEGURIDAD!

// (El guardia ya inicia la sesión)

// Incluir la conexión
require '../config/conexion.php';

// 3. Verificar que los datos lleguen por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Recoger TODOS los datos del formulario
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];
    $marca = $_POST['marca'];
    $tipo = $_POST['tipo'];
    $serie = $_POST['serie'];
    $consumo = $_POST['consumo'];
    
    // --- ¡NUEVO! Capturamos el checkbox de oferta ---
    // Si está marcado vale 1, si no vale 0
    $en_oferta = isset($_POST['en_oferta']) ? 1 : 0;

    // El nombre de la imagen que ya estaba en la BD
    $nombre_imagen_db = $_POST['imagen_anterior'];

    // --- 5. Lógica de la Imagen ---
    // Verificar si se subió una NUEVA imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0 && $_FILES['imagen']['size'] > 0) {
        
        $directorio_subida = '../uploads/';
        $nombre_archivo_original = basename($_FILES['imagen']['name']);
        $extension = pathinfo($nombre_archivo_original, PATHINFO_EXTENSION);
        $nombre_imagen_unico = uniqid() . '_' . time() . '.' . $extension;
        $ruta_objetivo = $directorio_subida . $nombre_imagen_unico;

        // Intentar mover la NUEVA imagen
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_objetivo)) {
            
            // BORRAR LA IMAGEN ANTERIOR (si existía)
            if (!empty($nombre_imagen_db) && file_exists($directorio_subida . $nombre_imagen_db)) {
                unlink($directorio_subida . $nombre_imagen_db);
            }
            
            // Actualizamos la variable con el nombre de la NUEVA imagen
            $nombre_imagen_db = $nombre_imagen_unico;

        } else {
            $_SESSION['mensaje'] = "Error: Hubo un problema al subir la nueva imagen.";
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: editar_producto.php?id=' . $id);
            exit;
        }
    }

    // --- 6. Actualizar la Base de Datos (UPDATE) ---
    try {
        // Añadimos 'en_oferta' a la lista de campos a actualizar
        $sql = "UPDATE productos SET 
                    nombre = ?, 
                    descripcion = ?, 
                    precio = ?, 
                    stock = ?, 
                    categoria = ?, 
                    marca = ?, 
                    tipo = ?, 
                    serie = ?, 
                    consumo = ?, 
                    imagen_url = ?,
                    en_oferta = ? 
                WHERE id = ?"; 
        
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta con todos los valores en orden (incluyendo $en_oferta)
        $stmt->execute([
            $nombre,
            $descripcion,
            $precio,
            $stock,
            $categoria,
            $marca,
            $tipo,
            $serie,
            $consumo,
            $nombre_imagen_db,
            $en_oferta, // <--- NUEVO DATO
            $id
        ]);

        $_SESSION['mensaje'] = "Producto actualizado exitosamente.";
        $_SESSION['tipo_mensaje'] = 'exito';
        header('Location: index.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al actualizar el producto: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: editar_producto.php?id=' . $id);
        exit;
    }

} else {
    header('Location: index.php');
    exit;
}
?>