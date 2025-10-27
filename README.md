# ☕ Chinos Café — Sistema Web de Gestión y Tienda Online

Sistema completo para la gestión de una cafetería artesanal, desarrollado con **PHP 8**, **MySQL**, **HTML5**, **CSS3** y **JavaScript ES6**.  
Este proyecto forma parte del **Taller Práctico 3** (Desarrollo de Software IV UTP 2025) y fue creado por **Eddie Man** y **Wilfredo Matute**.

---

## 📋 Descripción General

**Chinos Café** es una aplicación web integral que permite administrar todos los aspectos operativos de una cafetería:

- 🌐 Sitio web público con portada y tienda
- 🛒 Carrito de compras dinámico
- 💳 Checkout con factura
- 📦 Inventario con control de stock
- 🤝 Gestión de proveedores
- 💰 Registro de ventas y facturas
- 📊 Dashboard con estadísticas en tiempo real

Diseñado bajo una **arquitectura modular MVC simplificada**, combina un frontend responsivo con un backend seguro y escalable.

---

## ⚙️ Tecnologías Utilizadas

| Capa | Tecnologías |
|------|--------------|
| **Frontend** | HTML5 · CSS3 · JavaScript ES6 |
| **Backend** | PHP 8 (PHP PDO para MySQL) |
| **Base de Datos** | MySQL 5.7 + phpMyAdmin |
| **Servidor Local** | XAMPP / LAMPP |
| **Diseño Responsivo** | Flexbox · Grid Layout |
| **Seguridad** | Prepared Statements · Sanitización XSS |

---

## 🧩 Módulos Implementados

### 🏠 Inicio (`index.php`)

- Hero con imagen de fondo y texto promocional  
- Productos destacados (directos de la BD)  
- Formulario de contacto que guarda mensajes en la tabla `contactos`  
- Menú de navegación global (header + footer)

---

### ☕ Tienda (`tienda.php`)

- Catálogo completo de productos desde la BD  
- Filtros por categoría: _Bebidas Calientes · Frías · Postres_  
- Diseño tipo card con efectos hover y sombra suave  
- Imágenes optimizadas (logo y fondos HD)  
- **Integración con el carrito de compras**

---

### 🛍️ Carrito (`cart.php`)

- Sistema de sesiones PHP para almacenar productos  
- Sumar y restar cantidades interactivamente  
- Eliminar ítems del carrito  
- Visual del total y botón de “Finalizar Compra”  
- Archivos de soporte: `cart_add.php` y `cart_update.php`

---

### 💳 Checkout (`checkout.php`)

- Resumen del pedido previo a la compra  
- Selección de método de pago (efectivo, tarjeta, transferencia)  
- Registro de la venta en la tabla `ventas` y `facturas`  
- Reducción automática del stock en `productos`  
- Confirmación visual del pedido (versión PDF próximamente)

---

### 📦 Inventario (`inventario.php`)

- CRUD completo de productos (Agregar, Editar, Eliminar)  
- Control de stock y stock mínimo  
- Visual en tabla con alertas de bajo inventario  
- Integración con ventas para descontar stock automáticamente  

---

### 🤝 Proveedores (`proveedores.php`)

- Registro y gestión de proveedores  
- CRUD completo con validación básica  
- Filtros por estado (Activos / Inactivos)

---

### 💰 Ventas (`ventas.php`) y Detalles (`venta_detalle.php`)

- Listado de todas las ventas registradas  
- Detalle de productos por venta  
- Filtros por fecha y método de pago  
- Visualización resumida por día y total general

---

### 📊 Dashboard (`dashboard.php`)

- Panel principal para administradores  
- Gráficos de ventas e inventario (pendiente Chart.js)  
- Métricas de ingresos, top productos, y alertas de stock  
- Atajos a los módulos más usados

---

## 🗃️ Base de Datos

**Archivo:** `chinoscafe.sql`

Tablas principales:

- `productos` (id, nombre, descripcion, precio, imagen, stock, categoria)
- `ventas` (id, id_producto, cantidad, total, fecha)
- `facturas` (id, id_venta, metodo_pago, fecha)
- `proveedores` (id, nombre, telefono, correo)
- `contactos` (id, nombre, correo, mensaje)
- `usuarios` (id, nombre, correo, password_hash, rol)

---

## 🧱 Estructura de Carpetas
