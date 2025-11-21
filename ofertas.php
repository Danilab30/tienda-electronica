<?php
require 'config/conexion.php';

// Consultar SOLO los productos que están marcados como oferta (en_oferta = 1)
$productos = [];
try {
    $sql = "SELECT * FROM productos WHERE en_oferta = 1 AND stock > 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar ofertas: " . $e->getMessage());
}
?>

<?php include 'includes/header.php'; ?>

    <main style="width: 100%; padding: 0;">
        
        <div class="ofertas-header">
            <h1>🔥 Ofertas Relámpago 🔥</h1>
            <p>Aprovecha los mejores precios en tecnología antes de que se agoten.</p>
        </div>

        <div class="container-ofertas">
            
            <div class="productos-grid">
                <?php if (empty($productos)): ?>
                    <div class="reseña-login" style="grid-column: 1 / -1; text-align: center;">
                        <p>Por el momento no hay ofertas activas. ¡Vuelve pronto!</p>
                        <a href="index.php" class="btn btn-primary" style="width: auto; display: inline-block; margin-top: 1rem;">Ver todo el catálogo</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="producto-card">
                            
                            <div class="badge-oferta">OFERTA</div>

                            <?php if (!empty($producto['imagen_url'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x250.png?text=Sin+Imagen" alt="Sin Imagen">
                            <?php endif; ?>
                            
                            <div class="producto-info">
                                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <div class="producto-precio">$<?php echo htmlspecialchars($producto['precio']); ?></div>
                                <span class="btn-ver">Ver Detalles</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </main>

<?php include 'includes/footer.php'; ?>