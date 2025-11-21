<?php
session_start();
require 'config/conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) { header('Location: index.php'); exit; }
$producto_id = $_GET['id'];

$producto = null;
try {
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

    if (!$producto) { header('Location: index.php'); exit; }

    $reseñas = [];
    $sql_reseñas = "SELECT r.*, u.nombres FROM reseñas r JOIN usuarios u ON r.usuario_id = u.id WHERE r.producto_id = ? ORDER BY r.fecha DESC";
    $stmt_reseñas = $pdo->prepare($sql_reseñas);
    $stmt_reseñas->execute([$producto_id]);
    $reseñas = $stmt_reseñas->fetchAll();
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>

<?php include 'includes/header.php'; ?>

    <main>
        <?php
        if (isset($_SESSION['mensaje'])) {
            $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
            echo "<div class='mensaje $tipo_mensaje' style='max-width: 1100px; margin: 1rem auto;'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
            unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']);
        }
        ?>

        <div class="producto-detalle-container">
            <div class="producto-imagen">
                <img src="uploads/<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            </div>
            
            <div class="producto-info">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                
                <div class="valoracion-promedio">
                    <span class="estrellas-promedio">
                        <?php
                        $promedio_redondeado = round($producto['valoracion_promedio']);
                        for ($i = 1; $i <= 5; $i++) echo ($i <= $promedio_redondeado) ? '★' : '☆';
                        ?>
                    </span>
                    <span class="conteo-reseñas">(<?php echo htmlspecialchars($producto['valoracion_conteo']); ?> reseñas)</span>
                </div>
                
                <div class="precio">$<?php echo htmlspecialchars($producto['precio']); ?></div>
                <p class="descripcion"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                
                <form action="accion_carrito.php" method="POST" class="carrito-form">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <div class="form-group-cantidad">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>">
                    </div>
                    <button type="submit" class="btn-agregar">Agregar al carrito</button>
                </form>
            </div>
        </div>
        
        <div class="seccion-reseñas">
            <h2>Opiniones del Producto</h2>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="form-reseña">
                    <h3>Deja tu reseña</h3>
                    <form action="accion_reseña.php" method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                        <div class="form-group">
                            <label for="valoracion">Valoración (Estrellas)</label>
                            <select name="valoracion" id="valoracion" class="form-group-input" required style="width: 100%; padding: 0.5rem; background: #111; color: white; border: 1px solid #444;">
                                <option value="5">5 Estrellas ★★★★★</option>
                                <option value="4">4 Estrellas ★★★★☆</option>
                                <option value="3">3 Estrellas ★★★☆☆</option>
                                <option value="2">2 Estrellas ★★☆☆☆</option>
                                <option value="1">1 Estrella ★☆☆☆☆</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="comentario">Comentario</label>
                            <textarea name="comentario" id="comentario" placeholder="Escribe tu opinión..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: auto;">Enviar Reseña</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="reseña-login"><p>Debes <a href="login.php">iniciar sesión</a> para dejar una reseña.</p></div>
            <?php endif; ?>

            <div class="lista-reseñas">
                <?php if (empty($reseñas)): ?>
                    <p>Este producto aún no tiene reseñas. ¡Sé el primero!</p>
                <?php else: ?>
                    <?php foreach ($reseñas as $reseña): ?>
                        <div class="reseña">
                            <div class="reseña-header">
                                <strong><?php echo htmlspecialchars($reseña['nombres']); ?></strong>
                                <span class="estrellas"><?php for ($i = 1; $i <= 5; $i++) echo ($i <= $reseña['valoracion']) ? '★' : '☆'; ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($reseña['comentario'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>