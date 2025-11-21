<?php
session_start();
require 'config/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Error: Debes iniciar sesión.";
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: login.php'); exit;
}

$usuario_id = $_SESSION['usuario_id'];
$items_carrito = [];
$subtotal = 0.00;

try {
    $sql = "SELECT c.*, p.nombre, p.precio, p.imagen_url FROM carrito c JOIN productos p ON c.producto_id = p.id WHERE c.usuario_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $items_carrito = $stmt->fetchAll();

    if (empty($items_carrito)) {
        $_SESSION['mensaje'] = "Tu carrito está vacío.";
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: carrito.php'); exit;
    }
    foreach ($items_carrito as $item) { $subtotal += $item['precio'] * $item['cantidad']; }
} catch (PDOException $e) { die("Error: ". $e->getMessage()); }
?>

<?php include 'includes/header.php'; ?>

    <main>
        <div class="checkout-container">
            <div class="checkout-form">
                <h2>Finalizar Compra</h2>
                <p style="margin-bottom: 1.5rem;">Por favor ingresa los datos de envío y confirma el pago.</p>
                
                <form action="accion_pago.php" method="POST">
                    
                    <input type="hidden" name="total_pagado" value="<?php echo $subtotal; ?>">

                    <h3 style="color: var(--color-verde-neon); margin-bottom: 1rem;">¿Dónde vives?</h3>

                    <style>
                        .address-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr; /* Dos columnas */
                            gap: 1rem;
                            margin-bottom: 2rem;
                        }
                        .address-group {
                            display: flex;
                            flex-direction: column;
                        }
                        .address-group.full-width {
                            grid-column: 1 / -1; /* Ocupa todo el ancho */
                        }
                        .address-group label {
                            font-size: 0.9rem;
                            color: var(--color-gris-claro);
                            margin-bottom: 0.3rem;
                        }
                        .address-group input, .address-group select {
                            background-color: #222;
                            border: 1px solid #444;
                            color: #fff;
                            padding: 0.8rem;
                            border-radius: 4px;
                            font-size: 1rem;
                        }
                        .address-group input:focus {
                            border-color: var(--color-verde-neon);
                            outline: none;
                        }
                    </style>

                    <div class="address-grid">
                        
                        <div class="address-group">
                            <label for="cp">Código postal</label>
                            <input type="text" name="cp" id="cp" required placeholder="Ej: 85000">
                        </div>
                        <div class="address-group">
                            <label for="calle">Calle</label>
                            <input type="text" name="calle" id="calle" required>
                        </div>

                        <div class="address-group">
                            <label for="num_ext">Número externo</label>
                            <input type="text" name="num_ext" id="num_ext" required>
                        </div>
                        <div class="address-group">
                            <label for="num_int">Número interno (opcional)</label>
                            <input type="text" name="num_int" id="num_int">
                        </div>

                        <div class="address-group">
                            <label for="colonia">Colonia</label>
                            <input type="text" name="colonia" id="colonia" required>
                        </div>
                        <div class="address-group">
                            <label for="municipio">Municipio / Alcaldía</label>
                            <input type="text" name="municipio" id="municipio" required>
                        </div>

                        <div class="address-group full-width">
                            <label for="estado">Estado</label>
                            <input type="text" name="estado" id="estado" required>
                        </div>

                        <div class="address-group full-width">
                            <label for="referencias">Referencias (Opcional)</label>
                            <input type="text" name="referencias" id="referencias" placeholder="Ej: Entre calles, fachada color...">
                        </div>

                    </div>
                    
                    <div style="background: #222; padding: 1rem; border-radius: 4px; border: 1px dashed #555; margin-bottom: 1.5rem;">
                        <p style="margin: 0; font-size: 0.9rem; color: #aaa;">Método de Pago: <strong style="color: #fff;">Simulación (Demo)</strong></p>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-simular-pago">Confirmar Pedido y Pagar</button>
                </form>
            </div>

            <div class="checkout-resumen">
                <h2>Resumen de tu Pedido</h2>
                <div class="resumen-lista-items">
                    <?php foreach ($items_carrito as $item): ?>
                        <div class="resumen-item">
                            <img src="uploads/<?php echo htmlspecialchars($item['imagen_url']); ?>" alt="">
                            <div class="resumen-item-info">
                                <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                                <span>Cantidad: <?php echo htmlspecialchars($item['cantidad']); ?></span>
                            </div>
                            <div class="resumen-item-precio">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="resumen-linea"><span>Subtotal:</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
                <div class="resumen-linea"><span>Envío:</span><span>Gratis</span></div>
                <div class="resumen-linea total"><span>Total:</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>