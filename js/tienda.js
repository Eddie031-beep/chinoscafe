// === Filtros por categoría ===
document.addEventListener("click", (e)=>{
    const boton = e.target.closest(".filtro-btn");
    if(!boton) return;
    document.querySelectorAll(".filtro-btn").forEach(b=>b.classList.remove("activo"));
    boton.classList.add("activo");
    const categoria = boton.dataset.categoria;
    document.querySelectorAll(".card").forEach(card=>{
        const show = (categoria === "todos" || card.dataset.categoria === categoria);
        card.style.display = show ? "block" : "none";
    });
});

// === Añadir al carrito ===
document.addEventListener("click", async (e)=>{
    const btn = e.target.closest(".btn-add");
    if(!btn) return;
    const id = btn.dataset.id;
    try{
        const resp = await fetch("../php/cart_add.php",{
            method:"POST",
            headers:{ "Content-Type":"application/x-www-form-urlencoded" },
            body: "id="+encodeURIComponent(id)
        });
        const json = await resp.json();
        if(json.ok){
            // Mostrar notificación
            showNotification('Producto agregado al carrito');
            // Actualizar contador en el header
            updateCartCount(json.count);
        }else{
            alert(json.msg || "No se pudo añadir");
        }
    }catch(err){
        alert("Error de red");
    }
});

function showNotification(message) {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: var(--cafe-medio);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transition: opacity 0.3s;
    `;
    document.body.appendChild(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function updateCartCount(count) {
    const cartLink = document.querySelector('a[href*="cart.php"]');
    if (cartLink) {
        cartLink.textContent = `Carrito (${count})`;
    }
}