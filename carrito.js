// Estado interno persistido mediante LocalStorage
let carrito = JSON.parse(localStorage.getItem('nanamimus_cart')) || [];
const COSTO_ENVIO = 5.99;
const META_ENVIO_GRATIS = 50.00;

// Vinculación de elementos del DOM
const cartModal = document.getElementById('cartModal');
const cartBackdrop = document.getElementById('cartBackdrop');
const closeCartBtn = document.getElementById('closeCartBtn');
const btnSeguirComprando = document.getElementById('btnSeguirComprando');
const openCartFloating = document.getElementById('openCartFloating');

const cartEmptyState = document.getElementById('cartEmptyState');
const cartItemsList = document.getElementById('cartItemsList');
const cartFooter = document.getElementById('cartFooter');

const cartCountTag = document.getElementById('cartCountTag');
const globalCartCount = document.getElementById('globalCartCount');
const cartTotalPrice = document.getElementById('cartTotalPrice');
const shippingPrice = document.getElementById('shippingPrice');
const shippingAlert = document.getElementById('shippingAlert');
const neededAmount = document.getElementById('neededAmount');

// Abrir y cerrar el modal lateral
function toggleCart(show) {
    if (show) {
        cartBackdrop.classList.add('show');
        cartModal.classList.add('open');
    } else {
        cartBackdrop.classList.remove('show');
        cartModal.classList.remove('open');
    }
}

if(openCartFloating) openCartFloating.addEventListener('click', () => toggleCart(true));
if(closeCartBtn) closeCartBtn.addEventListener('click', () => toggleCart(false));
if(btnSeguirComprando) btnSeguirComprando.addEventListener('click', () => toggleCart(false));
if(cartBackdrop) cartBackdrop.addEventListener('click', () => toggleCart(false));

// Lógica principal de acciones del carrito
function ejecutarCarrito(accion, id, nombre = '', precio = 0, imagen = '') {
    if (accion === 'agregar') {
        const itemExistente = carrito.find(prod => prod.id === id);
        if (itemExistente) {
            itemExistente.cantidad += 1;
        } else {
            carrito.push({ id, nombre, precio: parseFloat(precio), imagen, cantidad: 1 });
        }
        actualizarInterfazCarrito();
        toggleCart(true); 
    }
}

function cambiarCantidad(id, cambio) {
    const item = carrito.find(prod => prod.id === id);
    if (item) {
        item.cantidad += cambio;
        if (item.cantidad <= 0) {
            eliminarDelCarrito(id);
        } else {
            actualizarInterfazCarrito();
        }
    }
}

function eliminarDelCarrito(id) {
    carrito = carrito.filter(prod => prod.id !== id);
    actualizarInterfazCarrito();
}

if(document.getElementById('btnVaciarCarrito')) {
    document.getElementById('btnVaciarCarrito').addEventListener('click', () => {
        carrito = [];
        actualizarInterfazCarrito();
    });
}

// Renderiza los productos en tiempo real modificando el HTML
function actualizarInterfazCarrito() {
    localStorage.setItem('nanamimus_cart', JSON.stringify(carrito));
    
    const totalItems = carrito.reduce((acc, prod) => acc + prod.cantidad, 0);
    cartCountTag.innerText = `${totalItems} ${totalItems === 1 ? 'item' : 'items'}`;
    
    if (totalItems > 0) {
        globalCartCount.innerText = totalItems;
        globalCartCount.style.display = 'block';
    } else {
        globalCartCount.style.display = 'none';
    }

    if (carrito.length === 0) {
        cartEmptyState.style.display = 'flex';
        cartItemsList.style.display = 'none';
        cartFooter.style.display = 'none';
        return;
    }

    cartEmptyState.style.display = 'none';
    cartItemsList.style.display = 'block';
    cartFooter.style.display = 'block';

    cartItemsList.innerHTML = '';
    let subtotal = 0;

    carrito.forEach(prod => {
        const subtotalItem = prod.precio * prod.cantidad;
        subtotal += subtotalItem;

        cartItemsList.innerHTML += `
            <div class="cart-item">
                <img src="${prod.imagen}" alt="${prod.nombre}">
                <div class="item-details">
                    <h5 class="item-title">${prod.nombre}</h5>
                    <p class="item-price">$${prod.precio.toFixed(2)}</p>
                    <div class="quantity-control">
                        <button onclick="cambiarCantidad(${prod.id}, -1)">-</button>
                        <span>${prod.cantidad}</span>
                        <button onclick="cambiarCantidad(${prod.id}, 1)">+</button>
                    </div>
                </div>
                <span class="item-subtotal">$${subtotalItem.toFixed(2)}</span>
                <button class="btn-remove" onclick="eliminarDelCarrito(${prod.id})">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;
    });

    const envioGratis = subtotal >= META_ENVIO_GRATIS;
    const envioAplicado = envioGratis ? 0 : COSTO_ENVIO;
    shippingPrice.innerText = envioGratis ? 'Gratis' : `$${COSTO_ENVIO.toFixed(2)}`;

    if (envioGratis) {
        shippingAlert.style.display = 'none';
    } else {
        shippingAlert.style.display = 'block';
        neededAmount.innerText = `$${(META_ENVIO_GRATIS - subtotal).toFixed(2)}`;
    }

    cartTotalPrice.innerText = `$${(subtotal + envioAplicado).toFixed(2)}`;
}

// Inicializar por si ya existían productos guardados previamente
actualizarInterfazCarrito();

// Envío estructurado de datos de compra a PHP
document.getElementById('btnFinalizarCompra').addEventListener('click', () => {
    if(carrito.length === 0) return;

    const subtotal = carrito.reduce((acc, prod) => acc + (prod.precio * prod.cantidad), 0);
    const envio = subtotal >= META_ENVIO_GRATIS ? 0 : COSTO_ENVIO;

    const datosPedido = {
        usuario_id: 1, 
        subtotal: subtotal,
        envio: envio,
        productos: carrito
    };

    fetch('procesar_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosPedido)
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            alert('¡Pedido guardado con éxito! ID de Pedido: ' + res.pedido_id);
            carrito = [];
            actualizarInterfazCarrito();
            toggleCart(false);
        } else {
            alert('Error al guardar el pedido: ' + res.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error de comunicación con el servidor.');
    });
});