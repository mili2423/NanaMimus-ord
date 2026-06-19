// FAVORITOS SYSTEM //
let favoritos = JSON.parse(localStorage.getItem("favoritos")) || [];

// GUARDAR EN LOCALSTORAGE
function guardarFavoritos() {
    localStorage.setItem("favoritos", JSON.stringify(favoritos));
}

// TOGGLE FAVORITO (AGREGAR / QUITAR)
function toggleFavorito(id, nombre, precio, imagen) {

    const index = favoritos.findIndex(p => p.id === id);

    if (index === -1) {
        favoritos.push({ id, nombre, precio, imagen });
    } else {
        favoritos.splice(index, 1);
    }

    guardarFavoritos();
    renderFavoritos();
    actualizarContadorFavoritos();
    actualizarIconos();
}

// RENDER 
function renderFavoritos() {

    const contenedor = document.getElementById("favItemsList");
    const empty = document.getElementById("favEmptyState");

    contenedor.innerHTML = "";

    if (favoritos.length === 0) {
        empty.style.display = "block";
        contenedor.style.display = "none";
        return;
    }

    empty.style.display = "none";
    contenedor.style.display = "block";

    favoritos.forEach(item => {

        contenedor.innerHTML += `
            <div class="favorito-item">
                <img src="${item.imagen}" />

                <div class="info">
                    <h4>${item.nombre}</h4>
                    <p>$${item.precio}</p>
                </div>

                <button onclick="toggleFavorito(${item.id}, '${item.nombre}', ${item.precio}, '${item.imagen}')">
                    ✕
                </button>
            </div>
        `;
    });
}

// CONTADOR
function actualizarContadorFavoritos() {
    const contador = document.getElementById("favCountTag");
    const contadorHeader = document.getElementById("contadorFavoritos");

    if (contador) contador.textContent = favoritos.length;
    if (contadorHeader) contadorHeader.textContent = favoritos.length;
}
// ICONOS CORAZÓN EN PRODUCTOS
function actualizarIconos() {

    document.querySelectorAll(".btn-fav").forEach(btn => {

        const id = parseInt(btn.dataset.id);

        const existe = favoritos.some(p => p.id === id);

        const icon = btn.querySelector("i");

        if (existe) {
            icon.classList.remove("fa-regular");
            icon.classList.add("fa-solid");
            icon.style.color = "#ff409f";
        } else {
            icon.classList.remove("fa-solid");
            icon.classList.add("fa-regular");
            icon.style.color = "#aaa";
        }
    });
}

// cerrar con X
if (closeFavBtn) {
    closeFavBtn.addEventListener('click', () => toggleFavoritos(false));
}

// ABRIR / CERRAR 
function toggleFavoritos() {
    document.getElementById("favModal").classList.toggle("open");
}

// INICIALIZAR
document.addEventListener("DOMContentLoaded", () => {
    renderFavoritos();
    actualizarContadorFavoritos();
    actualizarIconos();
});







