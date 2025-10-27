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
        btn.textContent = "Añadido ✓";
        setTimeout(()=>{ btn.textContent = "Añadir"; }, 1200);
        }else{
        alert(json.msg || "No se pudo añadir");
        }
    }catch(err){
        alert("Error de red");
    }
    });
