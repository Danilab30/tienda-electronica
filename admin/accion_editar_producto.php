<?php

require_once 'verificar_admin.php'; // ¡GUARDIA AQUÍ!

// (session_start() ya no es necesario)

// 2. Incluir la conexión
require '../config/conexion.php';

// 1. Iniciar sesión
session_start();

// 2. Incluir la conexión
require '../config/conexion.php';

// 3. Verificar que los datos lleguen por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Recoger TODOS los datos del formulario, INCLUYENDO LOS OCULTOS
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
    
    // El nombre de la imagen que ya estaba en la BD
    $nombre_imagen_db = $_POST['imagen_anterior'];

    // --- 5. Lógica de la Imagen ---
    // Verificar si se subió una NUEVA imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0 && $_FILES['imagen']['size'] > 0) {
        
        $directorio_subida = '../uploads/';
        
        // Crear un nombre único para la NUEVA imagen
        $extension = pathinfo(basename($_FILES['imagen']['name']), PATHINFO_EXTENSION);
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
            // Si falla la subida de la nueva imagen, avisamos y detenemos
            $_SESSION['mensaje'] = "Error: Hubo un problema al subir la nueva imagen.";
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: editar_producto.php?id=' . $id);
            exit;
        }
    }
    // Si no se subió una nueva imagen, $nombre_imagen_db simplemente conserva su valor original

    // --- 6. Actualizar la Base de Datos (UPDATE) ---
    try {
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
                    imagen_url = ? 
                WHERE id = ?"; // La clave es el 'WHERE'
        
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta con todos los valores en orden
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
            $nombre_imagen_db, // El nombre de la imagen (nueva o la anterior)
            $id                 // El ID para el 'WHERE'
        ]);

        // 7. Redirigir de vuelta al panel con mensaje de éxito
        $_SESSION['mensaje'] = "Producto actualizado exitosamente.";
        $_SESSION['tipo_mensaje'] = 'exito';
        header('Location: index.php');
        exit;

    } catch (PDOException $e) {
        // 8. Manejar errores de la base de datos
        $_SESSION['mensaje'] = "Error al actualizar el producto: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: editar_producto.php?id=' . $id);
        exit;
    }

} else {
    // Si no es POST, redirigir al panel
    header('Location: index.php');
    exit;
}
?>