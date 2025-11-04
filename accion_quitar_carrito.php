<?php
session_start();
require 'config/conexion.php';

// Guardia (solo usuarios logueados)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Verificamos que nos pasen el ID del *item del carrito* (no del producto)
if (isset($_GET['id'])) {
    $carrito_item_id = $_GET['id'];

    try {
        // Borramos el item, PERO solo si también pertenece al usuario logueado
        // (Esto evita que un usuario borre el carrito de otro)
        $sql = "DELETE FROM carrito WHERE id = ? AND usuario_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$carrito_item_id, $usuario_id]);

        $_SESSION['mensaje'] = "Producto quitado del carrito.";
        $_SESSION['tipo_mensaje'] = 'exito';
        
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al quitar el producto: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
    }
}

// Siempre regresamos al carrito
header('Location: carrito.php');
exit;
?>