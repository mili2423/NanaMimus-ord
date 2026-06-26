let carrito = [];
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

// Control de apertura y cierre del modal lateral
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

// Lógica para añadir artículos
function ejecutarCarrito(accion, id) {
    if (accion === 'agregar') {
        const itemExistente = carrito.find(prod => prod.id === id);
        
        if (itemExistente) {
            itemExistente.cantidad += 1;
            sincronizarConBD(id, itemExistente.cantidad);
            actualizarInterfazCarrito();
            toggleCart(true);
        } else {
            // Buscamos el botón clickeado que tiene el ID correspondiente
const boton = document.querySelector(`.btn-carrito[data-id="${id}"]`);            
            // Leemos los atributos data- de forma segura
            const nombre = boton ? boton.getAttribute('data-nombre') : "Producto";
            const precio = boton ? parseFloat(boton.getAttribute('data-precio')) : 0;
            const imagen = boton ? boton.getAttribute('data-imagen') : 'NanaMimus/carrr1.jpg';

            carrito.push({ id, nombre, precio, imagen, cantidad: 1 });
            sincronizarConBD(id, 1);
            actualizarInterfazCarrito();
            toggleCart(true);
        }
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
    fetch('actualizar_carrito_bd.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'eliminar', producto_id: id })
    });
    actualizarInterfazCarrito();
}

function sincronizarConBD(id, cantidad) {
    fetch('actualizar_carrito_bd.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'actualizar', producto_id: id, cantidad: cantidad })
    });
}

// Pintar la interfaz en tu cartBody (Línea 113 protegida contra errores 404)
function actualizarInterfazCarrito() {
    const cartCountTag = document.getElementById('cartCountTag');
    const globalCartCount = document.getElementById('globalCartCount');
    const shippingPrice = document.getElementById('shippingPrice');
    const shippingAlert = document.getElementById('shippingAlert');
    const neededAmount = document.getElementById('neededAmount');
    const cartTotalPrice = document.getElementById('cartTotalPrice');

    const totalItems = carrito.reduce((acc, prod) => acc + prod.cantidad, 0);
    if(cartCountTag) cartCountTag.innerText = `${totalItems} ${totalItems === 1 ? 'item' : 'items'}`;
    
    if (totalItems > 0 && globalCartCount) {
        globalCartCount.innerText = totalItems;
        globalCartCount.style.display = 'inline-block';
    } else if(globalCartCount) {
        globalCartCount.style.display = 'none';
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
            
            let imgSegura = prod.imagen ? prod.imagen : 'NanaMimus/carrr1.jpg';

            cartItemsList.innerHTML += `
                <div class="cart-item" style="display: flex; align-items: center; justify-content: space-between; margin: 12px; padding: 10px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); font-family: sans-serif;">
                    <!-- Protección onerror en la imagen para prevenir el 404 -->
                    <img src="${imgSegura}" alt="${prod.nombre}" onerror="this.onerror=null; this.src='NanaMimus/carrr1.jpg';" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    <div style="flex: 1; margin-left: 12px;">
                        <h5 style="margin: 0; font-size: 0.9rem; color: #333; font-weight: 600;">${prod.nombre}</h5>
                        <p style="margin: 3px 0; color: #ff409f; font-weight: bold; font-size: 0.9rem;">$${prod.precio.toFixed(2)}</p>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <button onclick="cambiarCantidad(${prod.id}, -1)" style="background: #fff0f6; border: 1px solid #ffc0cb; color: #ff409f; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold;">-</button>
                            <span style="font-size: 0.85rem; font-weight: bold; min-width: 15px; text-align: center;">${prod.cantidad}</span>
                            <button onclick="cambiarCantidad(${prod.id}, 1)" style="background: #fff0f6; border: 1px solid #ffc0cb; color: #ff409f; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold;">+</button>
                        </div>
                    </div>
                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; height: 55px;">
                        <button onclick="eliminarDelCarrito(${prod.id})" style="background: none; border: none; color: #ff409f; cursor: pointer; font-size: 0.9rem;"><i class="fa-solid fa-trash-can"></i></button>
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

// Carga asíncrona de la base de datos
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
        actualizarInterfazCarrito();
    });
}

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