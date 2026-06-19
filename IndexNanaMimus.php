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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="mas_prod.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- ENLACE DE TUS ESTILOS EXCLUSIVOS DEL CARRITO -->
    <link rel="stylesheet" href="carrito.css">
</head>

<body>
    <?php include 'header.php'; ?> 
    
    <div class="carousel">
        <ul>
            <li><img width="1580" height="450" src="NanaMimus/carrr1.jpg" alt=""></li>
            <li><img width="1580" height="450" src="NanaMimus/carrr2.jpg" alt=""></li>
            <li><img width="1580" height="450" src="NanaMimus/carrr3.jpg" alt=""></li>
            <li><img width="1580" height="450" src="NanaMimus/prueba2.jpg" alt=""></li>
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
        <main id="lista-categorias" class="productos-secciones">
            <div style="display: flex; gap: 20px; flex-wrap: wrap; padding: 20px; justify-content: center;">
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($producto = $resultado->fetch_assoc()): ?>
                        <div style="background: white; padding: 15px; border-radius: 15px; border: 1px solid #fdeef5; text-align: center; width: 220px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <img src="<?php echo $producto['imagen1']; ?>" alt="" style="width: 100%; height: 180px; object-fit: cover; border-radius: 10px;">
                            <h4 style="margin: 10px 0 5px 0; font-size: 0.95rem; color: #333;"><?php echo $producto['nombre']; ?></h4>
                            <p style="color: #ff409f; font-weight: bold; margin: 0 0 12px 0;">$<?php echo number_format($producto['precio'], 2); ?></p>

                            <!-- CORREGIDO: Envía todos los parámetros necesarios a carrito.js -->
                            <button onclick="ejecutarCarrito('agregar', <?php echo $producto['id']; ?>, '<?php echo addslashes($producto['nombre']); ?>', <?php echo $producto['precio']; ?>, '<?php echo $producto['imagen1']; ?>')"
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

        <!-- REINCORPORADO: Sección de totales y checkout del carrito -->
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

    </div> <!-- AQUÍ CIERRA CORRECTAMENTE EL MODAL DEL CARRITO -->

    <!-- CARGA DE SCRIPTS -->
    <script src="productos.js"></script>
    <script src="carrito.js"></script>

    <!-- EL FOOTER REAL DE LA TIENDA TOTALMENTE AFUERA DEL MODAL -->
    <?php include 'footer.php'; ?>
</body>

</html>