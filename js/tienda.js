document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".filtro-btn");
    const productos = document.querySelectorAll(".card");

    botones.forEach(boton => {
        boton.addEventListener("click", () => {
            botones.forEach(b => b.classList.remove("activo"));
            boton.classList.add("activo");

            const categoria = boton.dataset.categoria;
            productos.forEach(card => {
                const mostrar = categoria === "todos" || card.dataset.categoria === categoria;
                card.style.display = mostrar ? "block" : "none";
                card.style.opacity = mostrar ? "1" : "0";
                card.style.transition = "opacity 0.3s ease, transform 0.3s ease";
            });
        });
    });
});
