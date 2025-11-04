#  Tienda de Electrónica 

Proyecto de E-Commerce para la venta de componentes electrónicos y periféricos, desarrollado con PHP puro, MySQL y un stack XAMPP.

##  Características

* Panel de Administración (CRUD de Productos).
* Sistema de Autenticación de Usuarios (Registro y Login).
* Catálogo de Productos (Inicio, Detalle y Categorías).
* Sistema de Reseñas (Valoraciones con estrellas y comentarios).
* Carrito de Compras 100% funcional.
* Diseño *responsive* con paleta de colores neón.

##  Stack Tecnológico

* **Backend:** PHP 8.x
* **Base de Datos:** MySQL
* **Servidor Local:** XAMPP (Apache)
* **Frontend:** HTML5, CSS3 (con variables) y JavaScript (ligero)

---

##  Instrucciones de Instalación 

Sigue estos pasos para correr el proyecto en tu máquina local:

### 1. Prerrequisitos

* Tener [XAMPP](https://www.apachefriends.org/es/index.html) instalado (que incluye Apache, PHP y MySQL).
* Tener [Git](https://git-scm.com/) instalado.
* Un cliente de base de datos (como MySQL Workbench o el phpMyAdmin de XAMPP).

### 2. Clonar el Repositorio

Clona este proyecto dentro de tu carpeta `htdocs` de XAMPP:

```bash
# Navega a tu carpeta htdocs (la ruta puede variar)
cd C:/xampp/htdocs

# Clona el proyecto
git clone [https://github.com/TU-USUARIO/tienda-electronica.git](https://github.com/TU-USUARIO/tienda-electronica.git)

# Entra a la carpeta del proyecto
cd tienda-electronica

(No olvides reemplazar TU-USUARIO/tienda-electronica.git con la URL real de tu repositorio)

#### 3. Iniciar el Servidor
Abre el panel de control de XAMPP e inicia los módulos de Apache y MySQL.

4. Configurar la Base de Datos
Abre tu cliente de base de datos .

Crea una nueva base de datos (Schema) llamada exactamente: tienda_db.

Selecciona la base de datos tienda_db que acabas de crear.

Ve a la pestaña "Importar".

Haz clic en "Seleccionar archivo" y elige el archivo database_schema.sql que está en la raíz de este proyecto.

Haz clic en "Continuar" o "Importar".

5. Crear el Archivo de Conexión (¡MUY IMPORTANTE!)
Este archivo (conexion.php) está en el .gitignore por seguridad. Debes crearlo manualmente:

Ve a la carpeta config/.

Crea un archivo nuevo llamado conexion.php.

Pega el siguiente código y añade tu contraseña de MySQL (en XAMPP, la contraseña de root suele estar vacía '').

<?php

// --- Configuración de la Base de Datos ---
$db_host = 'localhost'; // Tu servidor de base de datos
$db_name = 'tienda_db'; // El nombre de tu base de datos
$db_user = 'root';      // Tu usuario de MySQL
$db_pass = '';          // <--- PON TU CONTRASEÑA DE MYSQL AQUÍ

// --- Conexión ---
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("¡Error de conexión! No se pudo conectar a la base de datos: " . $e->getMessage());
}
?>

6. Crear la Carpeta uploads/
El último paso. Esta carpeta también está en el .gitignore.

En la raíz del proyecto (junto a index.php), crea una carpeta nueva llamada uploads.

PHP usará esta carpeta para guardar las imágenes de los productos.

7. ¡Correr el Proyecto!
¡Ya está todo listo! Abre tu navegador y ve a:

Tienda Pública: http://localhost/tienda-electronica/

Panel de Administrador: http://localhost/tienda-electronica/admin/