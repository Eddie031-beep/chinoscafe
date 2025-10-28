<?php 
session_start();
require_once("../config/db.php");
global $pdo;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/tienda.css">
    <style>
        /* FILTROS MODERNOS */
        .filtros-modernos {
            background: #fff;
            padding: 30px 7%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 80px;
            z-index: 100;
        }
        
        .filtros-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .filtros-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filtros-title {
            font-size: 1.2rem;
            color: var(--cafe-oscuro);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--cafe-medio);
            box-shadow: 0 0 0 3px rgba(210, 166, 121, 0.1);
        }
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--cafe-medio);
            pointer-events: none;
        }
        
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }
        
        .filtro-card {
            background: linear-gradient(135deg, #f8f6f1 0%, #fff 100%);
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .filtro-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--cafe-medio), var(--cafe-claro));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .filtro-card.activo {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            border-color: var(--cafe-medio);
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(210, 166, 121, 0.4);
        }
        
        .filtro-card.activo::before {
            transform: scaleX(1);
        }
        
        .filtro-card:hover:not(.activo) {
            border-color: var(--cafe-medio);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .filtro-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .filtro-nombre {
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .filtro-count {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        /* PRODUCTOS GRID MEJORADO */
        .productos-section {
            padding: 50px 7%;
            background: var(--crema);
        }
        
        .productos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .productos-info {
            font-size: 1.1rem;
            color: #666;
        }
        
        .sort-select {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            background: #fff;
            transition: all 0.3s ease;
        }
        
        .sort-select:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }
        
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .producto-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            cursor: pointer;
        }
        
        .producto-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .producto-image-wrapper {
            position: relative;
            height: 250px;
            overflow: hidden;
            background: var(--crema);
        }
        
        .producto-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        
        .producto-card:hover .producto-image {
            transform: scale(1.1);
        }
        
        .producto-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--cafe-medio);
            color: #fff;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(139, 94, 60, 0.4);
        }
        
        .producto-actions-quick {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .producto-card:hover .producto-actions-quick {
            opacity: 1;
            transform: translateY(0);
        }
        
        .btn-quick {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .btn-quick:hover {
            background: var(--cafe-medio);
            color: #fff;
            transform: scale(1.1);
        }
        
        .producto-info {
            padding: 25px;
        }
        
        .producto-categoria {
            color: var(--cafe-medio);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .producto-nombre {
            font-size: 1.3rem;
            color: var(--cafe-oscuro);
            margin: 0 0 10px 0;
            font-weight: 600;
            min-height: 50px;
        }
        
        .producto-descripcion {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 45px;
        }
        
        .producto-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .producto-precio {
            font-size: 1.8rem;
            color: var(--cafe-medio);
            font-weight: 700;
        }
        
        .btn-add-cart {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(210, 166, 121, 0.3);
        }
        
        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(210, 166, 121, 0.5);
        }
        
        .no-results {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        
        .no-results svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        @media (max-width: 768px) {
            .filtros-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .search-box {
                max-width: 100%;
            }
        }
    </style>
</head>
<body class="page-tienda">
    <?php include("../includes/header.php"); ?>

    <!-- HERO -->
    <section class="hero-tienda">
        <div class="contenido-hero">
            <h1>Explora Nuestro Menú</h1>
            <p>Encuentra tu bebida perfecta entre nuestra selecta variedad</p>
        </div>
    </section>

    <!-- FILTROS MODERNOS -->
    <section class="filtros-modernos">
        <div class="filtros-container">
            <div class="filtros-header">
                <h2 class="filtros-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtrar por Categoría
                </h2>
                
                <div class="search-box">
                    <input type="text" id="searchProducts" class="search-input" placeholder="Buscar productos...">
                    <svg class="search-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>
            </div>
            
            <div class="filtros-grid">
                <div class="filtro-card activo" data-categoria="todos">
                    <div class="filtro-icon">🎯</div>
                    <div class="filtro-nombre">Todos</div>
                    <div class="filtro-count" id="count-todos">0 productos</div>
                </div>
                
                <div class="filtro-card" data-categoria="Bebida Caliente">
                    <div class="filtro-icon">☕</div>
                    <div class="filtro-nombre">Bebidas Calientes</div>
                    <div class="filtro-count" id="count-calientes">0</div>
                </div>
                
                <div class="filtro-card" data-categoria="Bebida Fría">
                    <div class="filtro-icon">🧊</div>
                    <div class="filtro-nombre">Bebidas Frías</div>
                    <div class="filtro-count" id="count-frias">0</div>
                </div>
                
                <div class="filtro-card" data-categoria="Postre">
                    <div class="filtro-icon">🍰</div>
                    <div class="filtro-nombre">Postres</div>
                    <div class="filtro-count" id="count-postres">0</div>
                </div>
                
                <div class="filtro-card" data-categoria="Panadería">
                    <div class="filtro-icon">🥐</div>
                    <div class="filtro-nombre">Panadería</div>
                    <div class="filtro-count" id="count-panaderia">0</div>
                </div>
            </div>
        </div>
    </section>

    <!-- PRODUCTOS -->
    <main id="productos" class="productos-section">
        <div class="productos-header">
            <p class="productos-info">
                Mostrando <strong id="productos-count">0</strong> producto(s)
            </p>
            
            <select class="sort-select" id="sortSelect">
                <option value="default">Ordenar por</option>
                <option value="price-asc">Precio: Menor a Mayor</option>
                <option value="price-desc">Precio: Mayor a Menor</option>
                <option value="name-asc">Nombre: A-Z</option>
                <option value="name-desc">Nombre: Z-A</option>
            </select>
        </div>