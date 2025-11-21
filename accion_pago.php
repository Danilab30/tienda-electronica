<?php
// 1. Iniciar sesión
session_start();

// 2. Incluir conexión
require 'config/conexion.php';

// --- 3. DOBLE GUARDIA DE SEGURIDAD ---
// (Aunque checkout.php ya los tiene, este archivo es el que maneja el dinero,
// así que debe ser extra seguro)

// Guardia 1: ¿Está logueado?
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Guardia 2: ¿Viene de un formulario POST?
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: index.php');
    exit;
}

// 4. Recoger datos
$usuario_id = $_SESSION['usuario_id'];
$total_pagado_form = $_POST['total_pagado']; // Este es el total que nos mandó el checkout

// --- NUEVA LÓGICA DE DIRECCIÓN ---

$calle = $_POST['calle'];
$num_ext = $_POST['num_ext'];
$num_int = isset($_POST['num_int']) ? "Int. " . $_POST['num_int'] : ""; // Solo si hay num int
$colonia = $_POST['colonia'];
$municipio = $_POST['municipio'];
$estado = $_POST['estado'];
$cp = $_POST['cp'];
$referencias = isset($_POST['referencias']) ? "Ref: " . $_POST['referencias'] : "";


$direccion = "$calle #$num_ext $num_int, Col. $colonia, $municipio, $estado, CP: $cp. $referencias";


// --- 5. INICIAMOS LA TRANSACCIÓN ---
// Una "transacción" le dice a MySQL: "Vas a hacer 4-5 cosas.
// Si UNA SOLA falla, cancela (rollback) TODO.
// Si TODAS salen bien, guarda (commit) TODO."
// Esto evita que te quedes sin stock pero no se cree el pedido,
// o que se cree el pedido pero no se vacíe el carrito.

try {
    // Iniciar la transacción
    $pdo->beginTransaction();

    // --- PASO A: Obtener los items del carrito y verificar el total REAL ---
    // (No confiamos en el total del formulario, lo recalculamos por seguridad)
    $sql_carrito = "SELECT c.*, p.precio, p.stock 
                    FROM carrito c
                    JOIN productos p ON c.producto_id = p.id
                    WHERE c.usuario_id = ?";
    $stmt_carrito = $pdo->prepare($sql_carrito);
    $stmt_carrito->execute([$usuario_id]);
    $items_carrito = $stmt_carrito->fetchAll();

    if (empty($items_carrito)) {
        // Si el carrito está vacío, cancelamos
        throw new Exception("El carrito está vacío.");
    }

    // Recalcular el total real en el backend
    $total_real = 0;
    foreach ($items_carrito as $item) {
        // Validar stock aquí es crucial
        if ($item['cantidad'] > $item['stock']) {
            throw new Exception("No hay suficiente stock para el producto: " . $item['nombre']);
        }
        $total_real += $item['precio'] * $item['cantidad'];
    }

    // --- PASO B: Crear el Pedido en la tabla 'pedidos' ---
    
    $sql_pedido = "INSERT INTO pedidos (usuario_id, total, direccion_envio, estado_pago, metodo_pago, estatus) 
               VALUES (?, ?, ?, 'pagado', 'simulado', 'Procesando')";

    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([$usuario_id, $total_real, $direccion]);

    // --- PASO C: Obtener el ID del pedido que acabamos de crear ---
    $pedido_id = $pdo->lastInsertId();

    // --- PASO D: Mover productos del carrito a 'detalles_pedido' y Actualizar Stock ---
    foreach ($items_carrito as $item) {

        // 1. Insertar en 'detalles_pedido'
        $sql_detalles = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) 
                         VALUES (?, ?, ?, ?)";
        $stmt_detalles = $pdo->prepare($sql_detalles);
        $stmt_detalles->execute([$pedido_id, $item['producto_id'], $item['cantidad'], $item['precio']]);

        // 2. Actualizar el stock en la tabla 'productos'
        $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt_stock = $pdo->prepare($sql_stock);
        $stmt_stock->execute([$item['cantidad'], $item['producto_id']]);
    }

    // --- PASO E: Vaciar el carrito del usuario ---
    $sql_vaciar = "DELETE FROM carrito WHERE usuario_id = ?";
    $stmt_vaciar = $pdo->prepare($sql_vaciar);
    $stmt_vaciar->execute([$usuario_id]);

    // --- PASO F: ¡ÉXITO! Confirmar la transacción ---
    // Si llegamos aquí, todo salió bien. Hacemos los cambios permanentes.
    $pdo->commit();

    // --- PASO G: Redirigir a la página de "Gracias" ---
    // Guardamos el ID del pedido en la sesión para mostrarlo
    $_SESSION['ultimo_pedido_id'] = $pedido_id;
    header('Location: gracias.php');
    exit;
} catch (Exception $e) {
    // --- ¡FALLO! Revertir la transacción ---
    // Si algo falló (ej. no hay stock), cancelamos todos los cambios
    $pdo->rollBack();

    // Mandamos al usuario de vuelta al carrito con el error
    $_SESSION['mensaje'] = "Error al procesar el pago: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: carrito.php');
    exit;
}
