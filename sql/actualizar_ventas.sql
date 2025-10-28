-- sql/actualizar_ventas.sql
-- Ejecuta este SQL en phpMyAdmin para agregar las columnas faltantes

USE chinoscafe_db;

-- Agregar columnas a la tabla ventas si no existen
ALTER TABLE ventas 
ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10,2) DEFAULT 0.00 AFTER id,
ADD COLUMN IF NOT EXISTS impuesto DECIMAL(10,2) DEFAULT 0.00 AFTER subtotal,
ADD COLUMN IF NOT EXISTS total DECIMAL(10,2) DEFAULT 0.00 AFTER impuesto,
ADD COLUMN IF NOT EXISTS metodo_pago VARCHAR(50) DEFAULT 'Efectivo' AFTER total,
ADD COLUMN IF NOT EXISTS cliente_nombre VARCHAR(100) AFTER metodo_pago,
ADD COLUMN IF NOT EXISTS cliente_correo VARCHAR(100) AFTER cliente_nombre,
ADD COLUMN IF NOT EXISTS cliente_telefono VARCHAR(20) AFTER cliente_correo,
ADD COLUMN IF NOT EXISTS direccion_entrega TEXT AFTER cliente_telefono,
ADD COLUMN IF NOT EXISTS notas TEXT AFTER direccion_entrega,
ADD COLUMN IF NOT EXISTS estado ENUM('Pendiente', 'Completada', 'Cancelada') DEFAULT 'Pendiente' AFTER notas,
ADD COLUMN IF NOT EXISTS fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER estado;

-- Verificar la estructura
DESCRIBE ventas;

-- Crear tabla venta_detalle si no existe
CREATE TABLE IF NOT EXISTS venta_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    nombre_producto VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id) ON DELETE CASCADE,
    INDEX idx_venta (id_venta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar
SELECT * FROM ventas LIMIT 1;
SELECT * FROM venta_detalle LIMIT 1;