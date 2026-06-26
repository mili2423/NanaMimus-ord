<?php
include("conexion.php");
$items_iniciales = 0;
session_start();
if (isset($_SESSION['usuario'])) {
    // Si inició sesión, muestras el botón hacia su perfil
    echo '<a href="perfil.html" class="btn-enlace">Mi Perfil (Hola, ' . $_SESSION['usuario'] . ')</a>';
} else {
    // Si no ha iniciado sesión, muestras el botón de Ingresar
    echo '<a href="inicio_sesion.html" class="btn-enlace">Iniciar Sesión / Registrarse</a>';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index | Nana Mimus</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <?php include 'header.php'; ?> 
    <div class="carousel">
        <ul>
            <li><img width="1580" height="450" src="NanaMimus/carrr1.jpg" alt=""></li>
            <li><img width="1580" height="450" src="NanaMimus/carrr2.jpg" alt=""></li>
            <li><img width="1580" height="450" src="NanaMimus/carrr3.jpg" alt=""></li>
            <li><img width="1580" height="450" src="NanaMimus/carrr4.jpg" alt=""></li>
            <li><img width="1580" height="450" src="NanaMimus/carrr7.jpg" alt=""></li>
        </ul>
    </div>

    <div class="contenedor-productos">
        <?php
        $buscar = isset($_GET['buscar']) ? $conexion->real_escape_string($_GET['buscar']) : '';
        $sql = "SELECT * FROM productos WHERE activo = 1";
        if ($buscar != '') {
            $sql .= " AND (nombre LIKE '%$buscar%' OR descripcion LIKE '%$buscar%')";
        }
        $resultado = $conexion->query($sql);
        ?>
        <main id="lista-categorias" class="seccion-productos">
            <div class="contenedor-productos">
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($producto = $resultado->fetch_assoc()): ?>
                        <div class="producto-card">
                            <img src="<?php echo $producto['imagen1']; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <div class="producto-info">
                                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                                
                                <!-- Tu botón nativo tal cual lo tenías, usando ejecutarCarrito -->
                                <button class="btn-carrito" 
                                        data-nombre="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>"
                                        data-precio="<?php echo $producto['precio']; ?>"
                                        data-imagen="<?php echo $producto['imagen1']; ?>"
                                        onclick="ejecutarCarrito('agregar', <?php echo $producto['id']; ?>)">
                                    <i class="fa-solid fa-cart-shopping" style="margin-right: 8px;"></i> Agregar al Carrito
                                </button>
                            </div>
                        </div>
                        <div style="background: white; padding: 15px; border-radius: 15px; border: 1px solid #fdeef5; text-align: center; width: 220px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <img src="<?php echo $producto['imagen1']; ?>" alt="" style="width: 100%; height: 180px; object-fit: cover; border-radius: 10px;">
                            <h4 style="margin: 10px 0 5px 0; font-size: 0.95rem; color: #333;"><?php echo $producto['nombre']; ?></h4>
                            <p style="color: #ff409f; font-weight: bold; margin: 0 0 12px 0;">$<?php echo number_format($producto['precio'], 2); ?></p>

                            <button onclick="ejecutarCarrito('agregar', <?php echo $producto['id']; ?>)"
                                style="background: #ff409f; color: white; width: 100%; padding: 12px 0; border-radius: 12px; border: none; cursor: pointer; font-size: 0.9rem; font-family: 'Poppins', sans-serif; font-weight: 600; transition: background 0.2s;">
                                Agregar al carrito
                            </button>


                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No se encontraron productos disponibles.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- ESTRUCTURA DEL MODAL LATERAL DEL CARRITO -->
    <div id="cartBackdrop" class="cart-backdrop"></div>
    <div id="cartModal" class="cart-modal">

        <div class="cart-header">
            <div class="cart-title">
                <i class="fa-solid fa-shopping-cart" style="color: #ff409f;"></i>
                <span>Mi Carrito</span>
                <span id="cartCountTag" class="cart-badge-count">0 items</span>
            </div>
            <button id="closeCartBtn" class="close-cart"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="cart-body" id="cartBody">
            <!-- Estado Vacío -->
            <div id="cartEmptyState" class="cart-empty">
                <i class="fa-solid fa-bag-shopping"></i>
                <p>Tu carrito está vacío</p>
                <button class="btn-seguir" id="btnSeguirComprando">Seguir comprando</button>
            </div>
            <!-- Contenedor dinámico de productos -->
            <div id="cartItemsList" style="display:none;"></div>
        </div>
    </div>
    <!-- ESTRUCTURA DEL MODAL LATERAL DEL FAVORITOS -->
    <div id="favBackdrop" class="fav-backdrop"></div>

    <div id="favModal" class="fav-modal">

        <div class="fav-header">
            <div class="fav-title">
                <i class="fa-regular fa-heart" style="color:#ff409f;"></i>
                <span>Mis Favoritos</span>
                <span id="favCountTag" class="fav-badge-count">0</span>
            </div>
            <button id="closeFavBtn" class="close-fav"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="fav-body">

            <!-- vacío -->
            <div id="favEmptyState" class="fav-empty">
                <i class="fa-regular fa-heart"></i>
                <p>No tienes favoritos aún</p>
            </div>

            <!-- lista dinámica -->
            <div id="favItemsList" style="display:none;"></div>

        </div>

    </div>
    <script src="productos.js"></script>
    <script src="carrito.js"></script>
    <SCript src="favoritos.js"></Script>
    <?php include 'footer.php'; ?>
</body>

</html>