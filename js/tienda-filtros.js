/* =========================================
   TIENDA - LÓGICA DE FILTROS
========================================= */

let filtrosAbiertos = false;

function toggleFiltros() {
    const panel = document.getElementById('filtrosPanel');
    const icon = document.getElementById('filtrosIcon');
    const text = document.getElementById('filtrosText');
    
    filtrosAbiertos = !filtrosAbiertos;
    
    if (filtrosAbiertos) {
        panel.classList.add('active');
        icon.textContent = '▲';
        text.textContent = 'Ocultar Filtros';
    } else {
        panel.classList.remove('active');
        icon.textContent = '▼';
        text.textContent = 'Mostrar Filtros';
    }
}

function aplicarFiltros() {
    const productos = document.querySelectorAll('.producto-card');
    const categoriasSeleccionadas = Array.from(document.querySelectorAll('.categoria-item input:checked'))
        .map(cb => cb.value);
    
    const precioMin = parseFloat(document.getElementById('precioMin').value) || 0;
    const precioMax = parseFloat(document.getElementById('precioMax').value) || Infinity;

    let productosVisibles = 0;

    productos.forEach(producto => {
        const categoria = producto.dataset.categoria;
        const precio = parseFloat(producto.dataset.precio);
        
        const cumpleCategoria = categoriasSeleccionadas.length === 0 || 
                                categoriasSeleccionadas.includes(categoria);
        const cumplePrecio = precio >= precioMin && precio <= precioMax;

        if (cumpleCategoria && cumplePrecio) {
            producto.style.display = '';
            productosVisibles++;
        } else {
            producto.style.display = 'none';
        }
    });

    actualizarTagsFiltros();
    console.log(`✅ Mostrando ${productosVisibles} productos`);
}

function actualizarTagsFiltros() {
    const container = document.getElementById('filtrosActivos');
    container.innerHTML = '';

    const categoriasSeleccionadas = Array.from(document.querySelectorAll('.categoria-item input:checked'));
    
    if (categoriasSeleccionadas.length === 0) return;

    categoriasSeleccionadas.forEach(checkbox => {
        const tag = document.createElement('div');
        tag.className = 'tag-filtro';
        tag.innerHTML = `
            ${checkbox.value}
            <button class="tag-close" onclick="removerFiltro('${checkbox.value.replace(/'/g, "\\'")}')">✕</button>
        `;
        container.appendChild(tag);
    });
}

function removerFiltro(valor) {
    const checkbox = document.querySelector(`.categoria-item input[value="${valor}"]`);
    if (checkbox) {
        checkbox.checked = false;
        aplicarFiltros();
    }
}

function limpiarFiltros() {
    document.querySelectorAll('.categoria-item input').forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('precioMin').value = 0;
    document.getElementById('precioMax').value = 100;
    document.querySelector('.precio-slider').value = 100;
    document.getElementById('filtrosActivos').innerHTML = '';
    aplicarFiltros();
}

// Agregar al carrito con AJAX
async function agregarAlCarrito(id) {
    try {
        const formData = new FormData();
        formData.append('id', id);

        const response = await fetch('../php/cart_add.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.ok) {
            mostrarNotificacion('✅ Producto agregado al carrito', 'success');
            
            // Actualizar contador del carrito en el header
            if (typeof updateCartBadge === 'function') {
                updateCartBadge(data.count);
            }
        } else {
            mostrarNotificacion('❌ ' + (data.msg || 'Error al agregar'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('❌ Error de conexión', 'error');
    }
}

function mostrarNotificacion(mensaje, tipo = 'success') {
    const notif = document.createElement('div');
    notif.textContent = mensaje;
    notif.style.cssText = `
        position: fixed;
        top: 110px;
        right: 20px;
        background: ${tipo === 'success' ? 'linear-gradient(135deg, #28a745, #20c997)' : 'linear-gradient(135deg, #dc3545, #c82333)'};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        z-index: 10000;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease-out;
        font-weight: 600;
    `;
    
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}

// Animaciones CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Aplicar filtros en tiempo real cuando cambia el precio
document.getElementById('precioMin').addEventListener('input', aplicarFiltros);
document.getElementById('precioMax').addEventListener('input', aplicarFiltros);

// Sincronizar el slider con los inputs de precio
document.getElementById('precioMin').addEventListener('input', function() {
    const min = parseFloat(this.value) || 0;
    const max = parseFloat(document.getElementById('precioMax').value) || 100;
    
    if (min > max) {
        this.value = max;
    }
    
    aplicarFiltros();
});

document.getElementById('precioMax').addEventListener('input', function() {
    const min = parseFloat(document.getElementById('precioMin').value) || 0;
    const max = parseFloat(this.value) || 100;
    
    if (max < min) {
        this.value = min;
    }
    
    document.querySelector('.precio-slider').value = max;
    aplicarFiltros();
});