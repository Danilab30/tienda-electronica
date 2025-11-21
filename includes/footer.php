<footer style="background-color: #050505; border-top: 1px solid #333; padding: 3rem 0; margin-top: auto; width: 100%;">
        <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; padding: 0 2rem;">
            
            <div>
                <h3 style="color: var(--color-verde-neon); margin-top: 0; font-size: 1.5rem;">CyberGipsy</h3>
                <p style="color: #888; line-height: 1.6; font-size: 0.95rem;">
                    Somos una empresa 100% mexicana nacida de la pasión por el hardware. 
                    Somos gamers y entusiastas como tú, dedicados a traerte los mejores componentes para que armes la PC de tus sueños sin complicaciones.
                </p>
            </div>

            <div>
                <h4 style="color: white; margin-bottom: 1rem;">Enlaces Rápidos</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.5rem;"><a href="index.php" style="color: #888; text-decoration: none; transition: color 0.3s;">Inicio</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="categorias.php" style="color: #888; text-decoration: none; transition: color 0.3s;">Catálogo Completo</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="ofertas.php" style="color: #888; text-decoration: none; transition: color 0.3s;">Ofertas Especiales</a></li>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li><a href="mi_cuenta.php" style="color: #888; text-decoration: none; transition: color 0.3s;">Mi Cuenta</a></li>
                    <?php else: ?>
                        <li><a href="login.php" style="color: #888; text-decoration: none; transition: color 0.3s;">Iniciar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div>
                <h4 style="color: white; margin-bottom: 1rem;">Contacto</h4>
                <p style="color: #888; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <span>📍</span> Ubicación por definir
                </p>
                <p style="color: #888; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <span>📧</span> contactoGipsy@gmail.com
                </p>
                <p style="color: #888; display: flex; align-items: center; gap: 8px;">
                    <span>📞</span> 622 151 4060
                </p>
            </div>

            <div>
                <h4 style="color: white; margin-bottom: 1rem;">Síguenos</h4>
                <div style="display: flex; gap: 1rem;">
                    <a href="#" style="color: var(--color-verde-neon); font-size: 1.5rem; text-decoration: none;"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" style="color: var(--color-verde-neon); font-size: 1.5rem; text-decoration: none;"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" style="color: var(--color-verde-neon); font-size: 1.5rem; text-decoration: none;"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" style="color: var(--color-verde-neon); font-size: 1.5rem; text-decoration: none;"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #222; color: #555; font-size: 0.9rem;">
            &copy; <?php echo date('Y'); ?> CyberGipsy. Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>