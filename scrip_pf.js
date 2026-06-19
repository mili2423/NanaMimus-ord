document.addEventListener("DOMContentLoaded", function () {
    const faqCards = document.querySelectorAll(".faq-card");

    // 1. Lógica del Acordeón (Abrir y Cerrar)
    faqCards.forEach((card) => {
        const questionRow = card.querySelector(".faq-question-row");
        const answerContent = card.querySelector(".faq-answer-content");

        if (questionRow && answerContent) {
            questionRow.addEventListener("click", function () {
                const isOpen = card.classList.contains("open");

                // Cerrar todos los demás acordeones para un efecto limpio
                faqCards.forEach((otherCard) => {
                    otherCard.classList.remove("open");
                    const otherAnswer = otherCard.querySelector(".faq-answer-content");
                    if (otherAnswer) otherAnswer.style.maxHeight = null;
                });

                // Si no estaba abierto, lo abrimos calculando su altura dinámica
                if (!isOpen) {
                    card.classList.add("open");
                    answerContent.style.maxHeight = answerContent.scrollHeight + "px";
                }
            });
        }
    });
});

// 2. Lógica de Filtros por Categoría (Global para que lo detecte el HTML)
function filtrarFaq(categoria, botonActivo) {
    // Cambiar la clase activa de los botones de categorías
    const tabs = document.querySelectorAll(".tab-btn");
    tabs.forEach(tab => tab.classList.remove("active"));
    
    // Si se hace click desde un botón válido, se le asigna la clase activa
    if (botonActivo) {
        botonActivo.classList.add("active");
    }

    // Mostrar u ocultar las tarjetas según la categoría seleccionada
    const cards = document.querySelectorAll(".faq-card");
    cards.forEach(card => {
        const cardCategory = card.getAttribute("data-category");
        if (categoria === "todos" || cardCategory === categoria) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
}