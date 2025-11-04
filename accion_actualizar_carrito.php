<?php
session_start();
require 'config/conexion.php';

// Guardia
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $carrito_item_id = $_POST['id'];
    $nueva_cantidad = $_POST['cantidad'];

    // Validar
    if ($nueva_cantidad <= 0) {
        
        header('Location: accion_quitar_carrito.php?id=' . $carrito_item_id);
        exit;
    }

    try {
        //Falta validar contra el stock
      
        $sql = "UPDATE carrito SET cantidad = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nueva_cantidad, $carrito_item_id, $usuario_id]);
        
    } catch (PDOException $e) {
        // Manejar error si se necesita
    }
}


header('Location: carrito.php');
exit;
?>