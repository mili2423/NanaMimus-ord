<header class="header">
    <div class="navfija">
        <div class="navbar">
            <div class="navbar-left">
                <a href="indexNanaMimus.php">
                    <img src="NanaMimus/logotipo.jpg" alt="Logo Nana Mimus" class="logo-redondo-tienda">
                </a>
            </div>

            <div class="navbar-search">
                <form action="indexNanaMimus.php" method="GET" class="search-form">
                    <input type="text" name="buscar" placeholder="¿Qué estás buscando?..."
                        value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <div class="navbar-right-container">
                <a href="preguntasfrecuentes.php" class="btn-ayuda">Ayuda</a>
                <div class="navbar-icons">
                    
                    <!-- 1. ICONO FAVORITOS -->
                    
                    <div class="icon-container">
                        <a href="#" onclick="toggleFavoritos()">
                            <i class="fa-regular fa-heart"></i>
                        </a>
                        <span class="badge-contador" id="contadorFavoritos">0</span>
                    </div>

                    <!-- 2. ICONO CARRITO (CORREGIDO Y DENTRO DE SU CONTENEDOR ESTILO FIGMA) -->
                    <div class="icon-container">
                        <a href="#" id="openCartFloating">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
                        <span id="globalCartCount" class="badge-contador" style="display: none;">0</span>
                    </div>
                    
                    <!-- 3. ICONO USUARIO -->
                    <div class="icon-container">
                        <a href="perfil.html"><!--Esto es por ahora-->
                            <i class="fa-regular fa-user"></i>
                        </a>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="menu">
        <ul id="box_search">
            <li><a href="#flores">Flores</a></li>
            <li><a href="#bebes">Ropa de bebé</a></li>
            <li><a href="#accesorio">Accesorios</a></li>
            <li><a href="#trajes">Trajes</a></li>
            <li><a href="#disfraz">Disfraces</a></li>
            <li><a href="sobrenosotros.html">Nosotros</a></li>
        </ul>
    </div>
</header>