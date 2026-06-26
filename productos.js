fetch("productos.php")
    .then(response => response.json())
    .then(productos => {

        const contenedor = document.querySelector(".contenedor-productos");

        contenedor.innerHTML = "";

        productos.forEach(producto => {

            const nombreSeguro = producto.nombre.replace(/'/g, "\\'");

            contenedor.innerHTML += `
        <div class="producto-card">

        <button
    type="button"
    class="btn-favorito"
    data-id="${producto.id}"
    onclick="toggleFavorito(
        ${producto.id},
        '${nombreSeguro}',
        ${producto.precio},
        '${producto.imagen1}'
    )">
    <i class="fa-solid fa-heart"></i>
</button>   

            <a href="producto.php?id=${producto.id}">
                <img src="${producto.imagen1}" alt="${producto.nombre}">
            </a>

            <div class="producto-info">
    <h3>${producto.nombre}</h3>

    <div class="precio">
        $${producto.precio}
    </div>

    <button
        class="btn-carrito"
        data-id="${producto.id}"
        data-nombre="${producto.nombre}"
        data-precio="${producto.precio}"
        data-imagen="${producto.imagen1}"
        onclick="ejecutarCarrito('agregar', ${producto.id})">
        Agregar al carrito
    </button>
</div>

        </div>
        `;
        });

        // Actualiza los corazones una vez creados
        if (typeof actualizarIconos === "function") {
            actualizarIconos();
        }
    });