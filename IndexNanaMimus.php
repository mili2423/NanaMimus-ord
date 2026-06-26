<?php
include("conexion.php");
$items_iniciales = 0;
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

    <!-- BOTÓN FAVORITOS -->
    <button
        type="button"
        class="btn-favorito"
        data-id="<?php echo $producto['id']; ?>"
        onclick="toggleFavorito(
            <?php echo $producto['id']; ?>,
            '<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>',
            <?php echo $producto['precio']; ?>,
            '<?php echo $producto['imagen1']; ?>'
        )">
        <i class="fa-regular fa-heart"></i>
    </button>

    <img
        src="<?php echo $producto['imagen1']; ?>"
        alt="<?php echo htmlspecialchars($producto['nombre']); ?>">

    <div class="producto-info">
        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>

        <p class="precio">
            $<?php echo number_format($producto['precio'], 2); ?>
        </p>

        <button
            type="button"
            class="btn-carrito"
            onclick="ejecutarCarrito('agregar', <?php echo $producto['id']; ?>)">
            <i class="fa-solid fa-cart-shopping" style="margin-right:8px;"></i>
            Agregar al carrito
        </button>
    </div>

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
                <span id="favCountTag" class="fav-badge-count">0 items</span>
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
    <script src="favoritos.js"></script>
    <?php include 'footer.php'; ?>
</body>

</html>