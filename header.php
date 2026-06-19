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
                    <div class="icon-container">
                        <a href="#" onclick="toggleFavoritos()">
                            <i class="fa-regular fa-heart"></i>
                        </a>
                        <span class="badge-contador" id="contadorFavoritos">0</span>
                    </div>

                    <div class="icon-container">
                        <a href="#" id="cart-icon-btn">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
                        <span class="badge-contador" id="contadorCarrito">
                            <?php echo $items_iniciales ?? 0; ?>
                        </span>
                    </div>

                    <div class="icon-container">
                        <a href="iniciosesion.html">
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

<!-- BOTÓN FLOTANTE PARA ABRIR EL CARRITO DIRECTAMENTE -->
    <button id="openCartFloating" style="position: fixed; top: 20px; right: 20px; z-index: 99; background: #fff0f6; border: 1px solid #fdeef5; color: #ff409f; padding: 12px 15px; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <i class="fa-solid fa-shopping-cart"></i>
        <span id="globalCartCount" style="background: #ff409f; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 50%; position: absolute; top: -5px; right: -5px; display: none; font-weight: bold;">0</span>
    </button>