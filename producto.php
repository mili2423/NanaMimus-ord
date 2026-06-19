<?php
include("conexion.php");

// Verificamos que venga el ID por la URL
if (!isset($_GET['id'])) {
    die("Producto no encontrado");
}

$id = intval($_GET['id']);

// Consulta a la base de datos para traer el producto seleccionado
$sql = "SELECT * FROM productos WHERE id = $id";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) == 0) {
    die("Producto no encontrado");
}

$producto = mysqli_fetch_assoc($resultado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $producto['nombre']; ?> | Nana Mimus</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="body-producto-detalle">

   <div class="navfija">
    <div class="navbar">
        
        <div class="navbar-left">
            <a href="indexNanaMimus.php">
                <img src="NanaMimus/logotipo.jpg" alt="Logo Nana Mimus" class="logo-redondo-tienda">
            </a>
        </div>

        <div class="navbar-search">
            <form action="indexNanaMimus.php" method="GET" class="search-form">
                <input type="text" name="buscar" placeholder="¿Qué estás buscando?..." value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

<div class="navbar-right-container">
            
            <a href="preguntasfrecuentes.html" class="btn-ayuda">Ayuda</a>

            <div class="navbar-icons">
                <div class="icon-container">
                    <a href="#" onclick="toggleFavoritos()">
                        <i class="fa-regular fa-heart"></i>
                    </a>
                    <span class="badge-contador" id="contadorFavoritos">1</span>
                </div>

                <div class="icon-container">
                    <a href="#">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a>
                    <span class="badge-contador" id="contadorCarrito">1</span>
                </div>

                <div class="icon-container">
                    <a href="micuenta.html">
                        <i class="fa-regular fa-user"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>
  </div>

  </div>
</div>

  <div class="main-wrapper">
      
      <nav class="breadcrumbs">
          <a href="indexNanaMimus.php">Inicio</a> / <span class="current"><?php echo $producto['nombre']; ?></span>
      </nav>

      <a href="indexNanaMimus.php" class="btn-back">
          <i class="fa-solid fa-arrow-left"></i> Volver a productos
      </a>

      <div class="contenedor-detalle-producto">
          
          <div class="imagen-producto-wrapper">
              <img src="<?php echo $producto['imagen1']; ?>" alt="<?php echo $producto['nombre']; ?>" class="img-principal">
              <span class="badge-hecho-mano">🌸 Hecho a Mano</span>
          </div>

          <div class="info-producto-wrapper">
              
              <span class="categoria-tag">🌸 Flores Tejidas</span>
              
              <h1 class="titulo-producto"><?php echo $producto['nombre']; ?></h1>

              <div class="rating-box">
                  <div class="stars">
                      <i class="fa-solid fa-star"></i>
                      <i class="fa-solid fa-star"></i>
                      <i class="fa-solid fa-star"></i>
                      <i class="fa-solid fa-star"></i>
                      <i class="fa-solid fa-star"></i>
                  </div>
                  <span class="rating-num">4.9</span>
                  <span class="reviews-count">(72 reseñas)</span>
              </div>

              <p class="precio-producto">
                  $<?php echo number_format($producto['precio'], 0, ',', '.'); ?>
              </p>

              <p class="descripcion-texto">
                  <?php echo $producto['descripcion']; ?>
              </p>

              <ul class="features-list">
                  <li><span class="check-icon">✓</span> Tejido 100% a mano</li>
                  <li><span class="check-icon">✓</span> Hilo algodón mercerizado</li>
                  <li><span class="check-icon">✓</span> Aprox. 8 rosas por ramo</li>
                  <li><span class="check-icon">✓</span> Cada pieza es única</li>
              </ul>

              <div class="actions-row">
                  <button class="btn-add-cart" data-id="<?php echo $producto['id']; ?>">
                      <i class="fa-solid fa-cart-shopping"></i> Agregar al Carrito
                  </button>
                  <button class="btn-fav-square">
                      <i class="fa-regular fa-heart"></i>
                  </button>
              </div>

          </div>

      </div>
  </div>

  <script src="productos.js"></script>
  <script src="carrito.js"></script>
  <script src="favoritos.js"></script>

</body>
</html>