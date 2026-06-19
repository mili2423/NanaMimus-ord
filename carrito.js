// ==========================================================================
// LÓGICA DEL CARRITO CON BASE DE DATOS Y LOCALSTORAGE (NANA MIMUS)
// ==========================================================================

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
const cartItemsList = document.getElementById('cartItemsList'); // Contenedor dinámico
const cartFooter = document.getElementById('cartFooter');

const cartCountTag = document.getElementById('cartCountTag');
const globalCartCount = document.getElementById('globalCartCount');
const cartTotalPrice = document.getElementById('cartTotalPrice');
const shippingPrice = document.getElementById('shippingPrice');
const shippingAlert = document.getElementById('shippingAlert');
const neededAmount = document.getElementById('neededAmount');

// Abrir / Cerrar Modal
function toggleCart(show) {
    if (show) {
        cartBackdrop.classList.add('show');
        cartModal.classList.add('open');
    } else {
        cartBackdrop.classList.remove('show');
        cartModal.classList.remove('open');
    }
}

if(openCartFloating) openCartFloating.addEventListener('click', (e) => { e.preventDefault(); toggleCart(true); });
if(closeCartBtn) closeCartBtn.addEventListener('click', () => toggleCart(false));
if(btnSeguirComprando) btnSeguirComprando.addEventListener('click', () => toggleCart(false));
if(cartBackdrop) cartBackdrop.addEventListener('click', () => toggleCart(false));

// LÓGICA PRINCIPAL: AGREGAR A LA BASE DE DATOS
function ejecutarCarrito(accion, id, nombre = '', precio = 0, imagen = '') {
    if (accion === 'agregar') {
        // 1. Buscamos si el producto ya está en el catálogo visual de la tarjeta de la tienda para jalar los datos reales
        const itemExistente = carrito.find(prod => prod.id === id);
        
        if (itemExistente) {
            itemExistente.cantidad += 1;
            sincronizarConBD(id, itemExistente.cantidad);
        } else {
            // Si el nombre no viene del onclick, usamos un fallback por seguridad
            const finalNombre = nombre || "Producto Nana Mimus";
            const finalPrecio = parseFloat(precio) || 0.00;
            const finalImagen = imagen || "NanaMimus/logotipo.jpg";

            carrito.push({ id, nombre: finalNombre, precio: finalPrecio, imagen: finalImagen, cantidad: 1 });
            sincronizarConBD(id, 1);
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
            sincronizarConBD(id, item.cantidad);
            actualizarInterfazCarrito();
        }
    }
}

function eliminarDelCarrito(id) {
    carrito = carrito.filter(prod => prod.id !== id);
    
    // Notificar eliminación a la Base de Datos mediante PHP
    fetch('actualizar_carrito_bd.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'eliminar', producto_id: id })
    }).catch(err => console.error("Error sincronizando BD:", err));

    actualizarInterfazCarrito();
}

// Enviar actualizaciones a tu script PHP
function sincronizarConBD(id, cantidad) {
    fetch('actualizar_carrito_bd.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'actualizar', producto_id: id, cantidad: cantidad })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status !== 'success') console.warn("Aviso BD:", data.message);
    })
    .catch(err => console.error("Error de conexión con la Base de Datos:", err));
}

// RENDERIZAR LA INTERFAZ IGUAL A FIGMA (image_30e81.jpg)
function actualizarInterfazCarrito() {
    localStorage.setItem('nanamimus_cart', JSON.stringify(carrito));
    
    const totalItems = carrito.reduce((acc, prod) => acc + prod.cantidad, 0);
    cartCountTag.innerText = `${totalItems} ${totalItems === 1 ? 'item' : 'items'}`;
    
    if (totalItems > 0) {
        globalCartCount.innerText = totalItems;
        globalCartCount.style.display = 'flex'; // Respeta tu .badge-contador flex rosa
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

        // Estructura limpia y adaptada a tus estilos .cart-item
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

if(document.getElementById('btnVaciarCarrito')) {
    document.getElementById('btnVaciarCarrito').addEventListener('click', () => {
        carrito = [];
        fetch('actualizar_carrito_bd.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'vaciar' })
        }).catch(err => console.error(err));
        actualizarInterfazCarrito();
    });
}

// Inicializar la carga inicial
actualizarInterfazCarrito();