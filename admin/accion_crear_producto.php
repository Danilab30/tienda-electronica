<?php
// 1. Iniciar la sesión (para mensajes de éxito/error)
session_start();

// 2. Incluir la conexión
// Estamos en 'admin', subimos un nivel a la raíz (../) y entramos a 'config'
require '../config/conexion.php';

// 3. Verificar que los datos lleguen por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Recoger los datos del formulario (los de texto)
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];
    $marca = $_POST['marca'];
    $tipo = $_POST['tipo'];
    $serie = $_POST['serie'];
    $consumo = $_POST['consumo'];

    // Inicializar la variable de la imagen
    $nombre_imagen_db = null;

    // --- 5. Manejo de la subida de imagen ---
    
    // Verificar si se subió un archivo y si no hubo errores
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        
        // Definir el directorio de subida (la carpeta que creamos)
        // Usamos '../uploads/' porque este script está en la carpeta 'admin'
        $directorio_subida = '../uploads/';
        
        // Obtener el nombre original del archivo
        $nombre_archivo_original = basename($_FILES['imagen']['name']);
        
        // Crear un nombre de archivo ÚNICO para evitar sobreescribir
        // Usamos time() o uniqid() para esto.
        $extension = pathinfo($nombre_archivo_original, PATHINFO_EXTENSION);
        $nombre_imagen_unico = uniqid() . '_' . time() . '.' . $extension;
        
        // La ruta completa donde se guardará el archivo
        $ruta_objetivo = $directorio_subida . $nombre_imagen_unico;

        // Intentar mover el archivo (de la carpeta temporal de PHP a nuestro directorio)
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_objetivo)) {
            // ¡Éxito! Guardamos el *nombre* del archivo en la variable
            $nombre_imagen_db = $nombre_imagen_unico;
        } else {
            // Error al mover el archivo
            $_SESSION['mensaje'] = "Error: Hubo un problema al subir la imagen.";
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: index.php');
            exit;
        }
    }
    // --- Fin del manejo de imagen ---

    // 6. Insertar los datos en la Base de Datos
    try {
        $sql = "INSERT INTO productos 
                (nombre, descripcion, precio, stock, categoria, marca, tipo, serie, consumo, imagen_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta con todos los valores
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
            $nombre_imagen_db // Este será el nombre único (ej: 653a...jpg) o NULL si no se subió imagen
        ]);

        // 7. Redirigir de vuelta al admin con mensaje de éxito
        $_SESSION['mensaje'] = "Producto añadido exitosamente.";
        $_SESSION['tipo_mensaje'] = 'exito';
        header('Location: index.php');
        exit;

    } catch (PDOException $e) {
        // 8. Manejar errores de la base de datos
        $_SESSION['mensaje'] = "Error al guardar el producto: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php');
        exit;
    }

} else {
    // Si no es POST, redirigir al formulario
    header('Location: index.php');
    exit;
}
?>