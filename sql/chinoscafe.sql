CREATE DATABASE IF NOT EXISTS chinocafe;
USE chinocafe;

-- Productos
CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(6,2),
  imagen VARCHAR(255)
);

-- Ventas
CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_producto INT,
  cantidad INT,
  total DECIMAL(8,2),
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- Proveedores
CREATE TABLE proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  telefono VARCHAR(20),
  correo VARCHAR(100)
);

-- Facturas
CREATE TABLE facturas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT,
  metodo_pago VARCHAR(50),
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_venta) REFERENCES ventas(id)
);

-- Contactos del sitio web
CREATE TABLE contactos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  correo VARCHAR(100),
  mensaje TEXT
);

-- Usuarios del sistema
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  correo VARCHAR(100),
  password_hash VARCHAR(255),
  rol ENUM('admin','empleado') DEFAULT 'empleado'
);
