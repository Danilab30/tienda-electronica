<?php
// (session_start ya lo hace el header, pero requerimos la conexión y la lógica de productos)
require 'config/conexion.php';

// --- LÓGICA PARA LEER PRODUCTOS ---
$productos_recientes = [];
try {
    $sql = "SELECT * FROM productos WHERE stock > 0 ORDER BY id DESC LIMIT 3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos_recientes = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al cargar productos: " . $e->getMessage();
}
?>

<?php include 'includes/header.php'; ?>

    <main>
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <?php 
                $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
                echo "<div class='mensaje $tipo_mensaje' style='max-width: 1200px; margin: 1rem auto; width: 90%;'>";
                echo htmlspecialchars($_SESSION['mensaje']);
                echo "</div>";
                unset($_SESSION['mensaje']);
                unset($_SESSION['tipo_mensaje']);
            ?>
        <?php endif; ?>

        <div class="slider-container">
            <div class="slider-wrapper" id="slider">
                
                <div class="slide">
                    <a href="categorias.php?categoria=Computadoras"> <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=1200&q=80" alt="Setup Gamer">
                        <div class="slide-content">
                            <h2>Componentes High-End</h2>
                            <p>La mejor potencia para tu PC Gamer</p>
                        </div>
                    </a>
                </div>

                <div class="slide">
                    <a href="categorias.php?categoria=Procesadores"> <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80" alt="Procesadores">
                        <div class="slide-content">
                            <h2>Nueva Generación</h2>
                            <p>Procesadores y Tarjetas de Video</p>
                        </div>
                    </a>
                </div>

                <div class="slide">
                    <a href="categorias.php?categoria=Perifericos"> <img src="https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=1200&q=80" alt="Periféricos">
                        <div class="slide-content">
                            <h2>Periféricos Pro</h2>
                            <p>Teclados, Mouse y Audio de alta fidelidad</p>
                        </div>
                    </a>
                </div>

            </div>
            
            <button class="slider-btn prev-btn" onclick="moverSlide(-1)">&#10094;</button>
            <button class="slider-btn next-btn" onclick="moverSlide(1)">&#10095;</button>
        </div>
            
            <button class="slider-btn prev-btn" onclick="moverSlide(-1)">&#10094;</button>
            <button class="slider-btn next-btn" onclick="moverSlide(1)">&#10095;</button>
        </div>

        <script>
            let indiceActual = 0;
            const slider = document.getElementById('slider');
            const totalSlides = 3; // Número de slides que pusimos

            function moverSlide(direccion) {
                indiceActual += direccion;
                
                if (indiceActual >= totalSlides) {
                    indiceActual = 0;
                } else if (indiceActual < 0) {
                    indiceActual = totalSlides - 1;
                }

                // Movemos el wrapper usando CSS transform
                slider.style.transform = `translateX(-${indiceActual * 100}%)`;
            }

            // Hacer que se mueva solo cada 5 segundos
            setInterval(() => {
                moverSlide(1);
            }, 5000);
        </script>

        <h2>Productos Recientes</h2>

        <div class="productos-grid">
            <?php if (empty($productos_recientes)): ?>
                <p>No hay productos disponibles por el momento.</p>
            <?php else: ?>
                <?php foreach ($productos_recientes as $producto): ?>
                    
                    <a href="producto.php?id=<?php echo $producto['id']; ?>" class="producto-card">
                        
                        <?php if (isset($producto['en_oferta']) && $producto['en_oferta'] == 1): ?>
                            <div class="badge-oferta" style="position: absolute; top: 10px; right: 10px; background-color: #ff0055; color: white; padding: 0.5rem 1rem; font-weight: bold; border-radius: 4px; box-shadow: 0 0 10px rgba(255, 0, 85, 0.5); z-index: 10;">OFERTA</div>
                            <?php endif; ?>

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
    </main>

<?php include 'includes/footer.php'; ?>