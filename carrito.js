let carrito = JSON.parse(localStorage.getItem('nanamimus_cart')) || [];
const COSTO_ENVIO = 5.99;
const META_ENVIO_GRATIS = 50.00;

// Elementos del DOM
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

// Abrir y Cerrar Modal del Carrito
function toggleCart(show) {
    if (show) {
        if(cartBackdrop) cartBackdrop.classList.add('show');
        if(cartModal) cartModal.classList.add('open');
    } else {
        if(cartBackdrop) cartBackdrop.classList.remove('show');
        if(cartModal) cartModal.classList.remove('open');
    }
}

if(openCartFloating) openCartFloating.addEventListener('click', (e) => { e.preventDefault(); toggleCart(true); });
if(closeCartBtn) closeCartBtn.addEventListener('click', () => toggleCart(false));
if(btnSeguirComprando) btnSeguirComprando.addEventListener('click', () => toggleCart(false));
if(cartBackdrop) cartBackdrop.addEventListener('click', () => toggleCart(false));

// Agregar Producto al hacer clic en el catálogo
function ejecutarCarrito(accion, id) {
    if (accion === 'agregar') {
        const itemExistente = carrito.find(prod => prod.id === id);
        
        if (itemExistente) {
            itemExistente.cantidad += 1;
            sincronizarConBD(id, itemExistente.cantidad);
            actualizarInterfazCarrito();
            toggleCart(true);
        } else {
            // Buscamos los datos visuales de la tarjeta del catálogo para no romper parámetros por comillas
            const botonClickeado = document.querySelector(`button[onclick*="id_producto = ${id}"], button[onclick*="${id}"]`);
            const tarjeta = botonClickeado ? botonClickeado.closest('.producto-card') : null;
            
            const nombre = tarjeta ? tarjeta.querySelector('h3').innerText : "Producto";
            const precioTxt = tarjeta ? tarjeta.querySelector('.precio').innerText : "$0.00";
            const precio = parseFloat(precioTxt.replace('$', '')) || 0;
            const imagen = tarjeta ? tarjeta.querySelector('img').src : "NanaMimus/logotipo.jpg";

            carrito.push({ id, nombre, precio, imagen, cantidad: 1 });
            sincronizarConBD(id, 1);
            actualizarInterfazCarrito();
            toggleCart(true);
        }
    }
}

// Cambiar cantidad (+ o -) en las tarjetas del carrito
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

// Eliminar un producto del carrito
function eliminarDelCarrito(id) {
    carrito = carrito.filter(prod => prod.id !== id);
    fetch('actualizar_carrito_bd.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'eliminar', producto_id: id })
    });
    actualizarInterfazCarrito();
}

// Sincronizar cantidades con el archivo PHP aparte
function sincronizarConBD(id, cantidad) {
    fetch('actualizar_carrito_bd.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'actualizar', producto_id: id, cantidad: cantidad })
    });
}

// Renderizar la interfaz idéntica a tu diseño de Figma
function actualizarInterfazCarrito() {
    localStorage.setItem('nanamimus_cart', JSON.stringify(carrito));
    
    const totalItems = carrito.reduce((acc, prod) => acc + prod.cantidad, 0);
    if(cartCountTag) cartCountTag.innerText = `${totalItems} ${totalItems === 1 ? 'item' : 'items'}`;
    
    if (totalItems > 0) {
        if(globalCartCount) {
            globalCartCount.innerText = totalItems;
            globalCartCount.style.display = 'inline-block';
        }
    } else {
        if(globalCartCount) globalCartCount.style.display = 'none';
    }

    if (carrito.length === 0) {
        if(cartEmptyState) cartEmptyState.style.display = 'flex';
        if(cartItemsList) cartItemsList.style.display = 'none';
        if(cartFooter) cartFooter.style.display = 'none';
        return;
    }

    if(cartEmptyState) cartEmptyState.style.display = 'none';
    if(cartItemsList) cartItemsList.style.display = 'block';
    if(cartFooter) cartFooter.style.display = 'block';

    if(cartItemsList) {
        cartItemsList.innerHTML = '';
        let subtotal = 0;

        carrito.forEach(prod => {
            const subtotalItem = prod.precio * prod.cantidad;
            subtotal += subtotalItem;

            cartItemsList.innerHTML += `
                <div class="cart-item" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; padding: 10px; background: #fff; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <img src="${prod.imagen}" alt="${prod.nombre}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    <div style="flex: 1; margin-left: 12px;">
                        <h5 style="margin: 0; font-size: 0.9rem; color: #333;">${prod.nombre}</h5>
                        <p style="margin: 3px 0; color: #ff409f; font-weight: bold; font-size: 0.9rem;">$${prod.precio.toFixed(2)}</p>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <button onclick="cambiarCantidad(${prod.id}, -1)" style="background: #fff0f6; border: 1px solid #ffc0cb; color: #ff409f; border-radius: 50%; width: 22px; height: 22px; cursor: pointer;">-</button>
                            <span style="font-size: 0.85rem; font-weight: bold;">${prod.cantidad}</span>
                            <button onclick="cambiarCantidad(${prod.id}, 1)" style="background: #fff0f6; border: 1px solid #ffc0cb; color: #ff409f; border-radius: 50%; width: 22px; height: 22px; cursor: pointer;">+</button>
                        </div>
                    </div>
                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                        <button onclick="eliminarDelCarrito(${prod.id})" style="background: none; border: none; color: #aaa; cursor: pointer;"><i class="fa-solid fa-trash-can"></i></button>
                        <span style="font-weight: bold; font-size: 0.9rem; color: #333;">$${subtotalItem.toFixed(2)}</span>
                    </div>
                </div>
            `;
        });

        const envioGratis = subtotal >= META_ENVIO_GRATIS;
        const envioAplicado = envioGratis ? 0 : COSTO_ENVIO;
        if(shippingPrice) shippingPrice.innerText = envioGratis ? 'Gratis' : `$${COSTO_ENVIO.toFixed(2)}`;

        if(shippingAlert) {
            if (envioGratis) {
                shippingAlert.style.display = 'none';
            } else {
                shippingAlert.style.display = 'block';
                if(neededAmount) neededAmount.innerText = `$${(META_ENVIO_GRATIS - subtotal).toFixed(2)}`;
            }
        }

        if(cartTotalPrice) cartTotalPrice.innerText = `$${(subtotal + envioAplicado).toFixed(2)}`;
    }
}

// Cargar datos asíncronamente desde el archivo PHP externo al abrir la tienda
function cargarCarritoDesdeBD() {
    fetch('actualizar_carrito_bd.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'obtener' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success' && data.productos) {
            carrito = data.productos;
            actualizarInterfazCarrito();
        }
    })
    .catch(() => {
        actualizarInterfazCarrito(); // Fallback por si acaso
    });
}

// Vaciar carrito por completo
if(document.getElementById('btnVaciarCarrito')) {
    document.getElementById('btnVaciarCarrito').addEventListener('click', () => {
        carrito = [];
        fetch('actualizar_carrito_bd.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'vaciar' })
        });
        actualizarInterfazCarrito();
    });
}

document.addEventListener('DOMContentLoaded', cargarCarritoDesdeBD);