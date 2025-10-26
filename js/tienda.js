// 🎨 Filtros de productos
document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".filtro-btn");
    const productos = document.querySelectorAll(".card");

    botones.forEach(boton => {
        boton.addEventListener("click", () => {
            // Quitar selección previa
            botones.forEach(b => b.classList.remove("activo"));
            boton.classList.add("activo");

            const categoria = boton.dataset.categoria;

            productos.forEach(card => {
                const coincide = categoria === "todos" || card.dataset.categoria === categoria;
                card.style.display = coincide ? "block" : "none";
                card.style.opacity = coincide ? "1" : "0";
            });
        });
    });
});


// 🌅 Efecto de animación del fondo transicional
window.addEventListener("scroll", () => {
    const titulo = document.querySelector(".fondo-transicion h2");
    if (titulo && titulo.getBoundingClientRect().top < window.innerHeight * 0.8) {
        titulo.style.opacity = "1";
        titulo.style.transform = "translateY(0)";
    }

    // ✨ Animación de tarjetas
    document.querySelectorAll(".card").forEach(card => {
        const rect = card.getBoundingClientRect();
        if (rect.top < window.innerHeight * 0.85) {
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }
    });
});
