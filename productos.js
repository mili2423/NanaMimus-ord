fetch("productos.php")
.then(response => response.json())
.then(productos => {

    const contenedor = document.querySelector(".contenedor-productos");

    contenedor.innerHTML = "";

    productos.forEach(producto => {

        contenedor.innerHTML += `
        <div class="producto-card">

           <a href="producto.php?id=${producto.id}">
    <img src="${producto.imagen1}" alt="${producto.nombre}">
</a>

            <div class="producto-info">
                <h3>${producto.nombre}</h3>

                <div class="precio">
                    $${producto.precio}
                </div>

                <button class="btn-carrito">
                    Agregar al carrito
                </button>
            </div>

        </div>
        `;
    });

});

/*Traslada el header y footer*/
fetch('header.html')
    .then(response => response.text())
    .then(data => {
        document.getElementById('header').innerHTML = data;
    });

fetch('footer.html')
    .then(response => response.text())
    .then(data => {
        document.getElementById('footer').innerHTML = data;
    });