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

        <!-- Sección de totales y checkout del carrito -->
        <div class="cart-footer" id="cartFooter" style="display:none;">
            <div class="row-fee">
                <span>Envío</span>
                <span id="shippingPrice">$5.99</span>
            </div>
            <div id="shippingAlert" class="shipping-alert">
                Agrega <span id="neededAmount">$0.00</span> más para envío gratis
            </div>
            <div class="row-total">
                <span>Total</span>
                <span class="total-price" id="cartTotalPrice">$0.00</span>
            </div>
            <button class="btn-checkout" id="btnFinalizarCompra">Finalizar Compra ✨</button>
            <button class="btn-vaciar" id="btnVaciarCarrito">Vaciar carrito</button>
        </div>

    </div> 
</body>

<!-- CARGA DE SCRIPTS -->
<script src="productos.js"></script>
<script src="carrito.js"></script>
<?php include 'footer.php'; ?>
</html>