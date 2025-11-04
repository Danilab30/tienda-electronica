<?php

session_start();


require 'config/conexion.php';


if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Error: Debes iniciar sesión para agregar productos al carrito.";
    $_SESSION['tipo_mensaje'] = 'error';
    
  
    $producto_id = isset($_POST['id']) ? $_POST['id'] : 'index.php';
    header('Location: producto.php?id=' . $producto_id);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $producto_id = $_POST['id'];
    $cantidad = $_POST['cantidad'];
    $usuario_id = $_SESSION['usuario_id']; 

    
    if ($cantidad <= 0) {
        $_SESSION['mensaje'] = "Error: La cantidad debe ser al menos 1.";
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: producto.php?id=' . $producto_id);
        exit;
    }
    
   
    try {
        
        $sql_check = "SELECT * FROM carrito WHERE usuario_id = ? AND producto_id = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$usuario_id, $producto_id]);
        $item_existente = $stmt_check->fetch();

        if ($item_existente) {
            
            $nueva_cantidad = $item_existente['cantidad'] + $cantidad;
            $sql = "UPDATE carrito SET cantidad = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nueva_cantidad, $item_existente['id']]);
        
        } else {
            
            $sql = "INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id, $producto_id, $cantidad]);
        }

        
        $_SESSION['mensaje'] = "¡Producto añadido al carrito exitosamente!";
        $_SESSION['tipo_mensaje'] = 'exito';
        header('Location: producto.php?id=' . $producto_id);
        exit;

    } catch (PDOException $e) {
       
        $_SESSION['mensaje'] = "Error al añadir al carrito: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: producto.php?id=' . $producto_id);
        exit;
    }

} else {
    
    header('Location: index.php');
    exit;
}
?>