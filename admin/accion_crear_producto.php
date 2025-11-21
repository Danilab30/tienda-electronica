<?php
require_once 'verificar_admin.php'; // ¡GUARDIA DE SEGURIDAD!

// (El guardia ya inicia la sesión)

// 2. Incluir la conexión
require '../config/conexion.php';

// 3. Verificar que los datos lleguen por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Recoger los datos del formulario
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
    // Si el checkbox está marcado, $_POST['en_oferta'] existe. Si no, no existe.
    $en_oferta = isset($_POST['en_oferta']) ? 1 : 0;

    // Inicializar la variable de la imagen
    $nombre_imagen_db = null;

    // --- 5. Manejo de la subida de imagen ---
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorio_subida = '../uploads/';
        $nombre_archivo_original = basename($_FILES['imagen']['name']);
        $extension = pathinfo($nombre_archivo_original, PATHINFO_EXTENSION);
        $nombre_imagen_unico = uniqid() . '_' . time() . '.' . $extension;
        $ruta_objetivo = $directorio_subida . $nombre_imagen_unico;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_objetivo)) {
            $nombre_imagen_db = $nombre_imagen_unico;
        } else {
            $_SESSION['mensaje'] = "Error: Hubo un problema al subir la imagen.";
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: index.php');
            exit;
        }
    }

    // 6. Insertar los datos en la Base de Datos
    try {
        // Agregamos 'en_oferta' a la lista de columnas y un '?' al final
        $sql = "INSERT INTO productos 
                (nombre, descripcion, precio, stock, categoria, marca, tipo, serie, consumo, imagen_url, en_oferta) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta con todos los valores (incluyendo $en_oferta al final)
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
            $en_oferta // <--- ¡AQUÍ GUARDAMOS EL DATO DE LA OFERTA!
        ]);

        $_SESSION['mensaje'] = "Producto añadido exitosamente.";
        $_SESSION['tipo_mensaje'] = 'exito';
        header('Location: index.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al guardar el producto: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php');
        exit;
    }

} else {
    header('Location: index.php');
    exit;
}
?>