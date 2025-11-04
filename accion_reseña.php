<?php
// 1. Iniciar sesión
session_start();

// 2. Incluir conexión
require 'config/conexion.php';

// --- 3. EL GUARDIA DE SEGURIDAD ---
// Verificamos si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si no está logueado, lo sacamos.
    $_SESSION['mensaje'] = "Error: Debes iniciar sesión para dejar una reseña.";
    $_SESSION['tipo_mensaje'] = 'error';
    // Lo regresamos al producto del que vino (si tenemos el id)
    $producto_id = isset($_POST['producto_id']) ? $_POST['producto_id'] : 'index.php';
    header('Location: producto.php?id=' . $producto_id);
    exit;
}

// 4. Verificar que los datos lleguen por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 5. Recoger los datos del formulario
    $producto_id = $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario_id']; // El ID lo sacamos de la sesión
    $valoracion = $_POST['valoracion'];
    $comentario = $_POST['comentario'];

    // 6. Insertar la reseña en la BD
    try {
        // Usamos INSERT ... ON DUPLICATE KEY UPDATE
        // Esto aprovecha la llave ÚNICA (producto_id, usuario_id) que creamos.
        // Si el usuario YA había dejado una reseña, la actualiza.
        // Si no, la inserta como nueva.
        $sql = "INSERT INTO reseñas (producto_id, usuario_id, valoracion, comentario) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE valoracion = ?, comentario = ?";
        
        $stmt = $pdo->prepare($sql);
        // Pasamos los valores: [id_prod, id_user, val, com, val_update, com_update]
        $stmt->execute([$producto_id, $usuario_id, $valoracion, $comentario, $valoracion, $comentario]);

        // --- 7. RECALCULAR EL PROMEDIO DEL PRODUCTO ---
        // Después de insertar/actualizar, recalculamos el promedio y el conteo
        
        // 7a. Obtener el nuevo promedio y conteo
        $sql_avg = "SELECT AVG(valoracion) as promedio, COUNT(id) as conteo 
                    FROM reseñas 
                    WHERE producto_id = ?";
        $stmt_avg = $pdo->prepare($sql_avg);
        $stmt_avg->execute([$producto_id]);
        $stats = $stmt_avg->fetch();

        $nuevo_promedio = $stats['promedio'];
        $nuevo_conteo = $stats['conteo'];

        // 7b. Actualizar la tabla 'productos' con los nuevos valores
        $sql_update = "UPDATE productos SET 
                        valoracion_promedio = ?, 
                        valoracion_conteo = ? 
                       WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$nuevo_promedio, $nuevo_conteo, $producto_id]);

        // 8. Redirigir de vuelta al producto con mensaje de éxito
        $_SESSION['mensaje'] = "¡Gracias por tu reseña!";
        $_SESSION['tipo_mensaje'] = 'exito';
        header('Location: producto.php?id=' . $producto_id);
        exit;

    } catch (PDOException $e) {
        // 9. Manejar errores
        $_SESSION['mensaje'] = "Error al guardar tu reseña: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: producto.php?id=' . $producto_id);
        exit;
    }

} else {
    // Si no es POST, redirigir al inicio
    header('Location: index.php');
    exit;
}
?>