<?php
include("conexion.php");
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
</head>

<body>
    <div id="header"></div>
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
    <!-- Carga la nueva lógica del modal del carrito -->
    <script src="carrito.js"></script>
    <div id="footer"></div>                
    <script src="productos.js"></script>
    <script src="carrito.js"></script>
</body>
</html>