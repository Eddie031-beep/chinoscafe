<?php
session_start();
require_once("../config/db.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestras Sucursales | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sucursales-page {
            padding: 50px 6%;
            background: var(--crema);
            min-height: 100vh;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .page-badge {
            display: inline-block;
            background: rgba(210, 166, 121, 0.1);
            color: var(--cafe-medio);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .page-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            color: var(--cafe-oscuro);
            margin: 0 0 20px 0;
        }
        
        .page-description {
            font-size: 1.2rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }
        
        .sucursales-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .sucursal-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .sucursal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .sucursal-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .sucursal-content {
            padding: 30px;
        }
        
        .sucursal-nombre {
            font-size: 1.8rem;
            color: var(--cafe-oscuro);
            margin: 0 0 20px 0;
            font-weight: 700;
        }
        
        .sucursal-info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-icon {
            width: 24px;
            height: 24px;
            color: var(--cafe-medio);
            flex-shrink: 0;
        }
        
        .info-text {
            flex: 1;
            color: #555;
            line-height: 1.6;
        }
        
        .info-text strong {
            display: block;
            color: var(--cafe-oscuro);
            margin-bottom: 5px;
        }
        
        .sucursal-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }
        
        .btn-sucursal {
            flex: 1;
            padding: 12px 20px;
            border-radius: 25px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
        }
        
        .btn-secondary {
            background: rgba(210, 166, 121, 0.1);
            color: var(--cafe-medio);
            border: 2px solid var(--cafe-medio);
        }
        
        .btn-sucursal:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(210, 166, 121, 0.3);
        }
        
        .map-section {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }
        
        .map-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .map-title {
            font-size: 2rem;
            color: var(--cafe-oscuro);
            margin: 0 0 10px 0;
        }
        
        .map-description {
            color: #666;
        }
        
        .map-container {
            width: 100%;
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        @media (max-width: 768px) {
            .sucursales-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .sucursal-actions {
                flex-direction: column;
            }
            
            .map-container {
                height: 350px;
            }
            
            .page-title {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 1100px) {
            .sucursales-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="sucursales-page">
        <div class="page-header">
            <span class="page-badge">📍 Ubicaciones</span>
            <h1 class="page-title">Nuestras Sucursales</h1>
            <p class="page-description">Encuéntranos en las mejores ubicaciones de Panamá. Cada sucursal ofrece la misma calidad y excelencia que nos caracteriza.</p>
        </div>

        <!-- TARJETAS DE SUCURSALES -->
        <div class="sucursales-grid">
            <!-- Sucursal 1 -->
            <div class="sucursal-card">
                <img src="../assets/img/sucursal1.jpg" alt="Sucursal Obarrio" class="sucursal-image" onerror="this.src='../assets/img/default.jpg'">
                <div class="sucursal-content">
                    <h3 class="sucursal-nombre">🏢 Chinos Café Obarrio</h3>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Dirección</strong>
                            Calle 53 Este, Obarrio<br>
                            Edificio Plaza Obarrio, Local 5
                        </div>
                    </div>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Horario</strong>
                            Lun - Vie: 7:00 AM - 9:00 PM<br>
                            Sáb - Dom: 8:00 AM - 10:00 PM
                        </div>
                    </div>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Teléfono</strong>
                            +507 264-5000
                        </div>
                    </div>
                    
                    <div class="sucursal-actions">
                        <a href="https://www.google.com/maps/search/Obarrio+Panama" target="_blank" class="btn-sucursal btn-primary">
                            🗺️ Ver en Mapa
                        </a>
                        <a href="tel:+5072645000" class="btn-sucursal btn-secondary">
                            📞 Llamar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sucursal 2 -->
            <div class="sucursal-card">
                <img src="../assets/img/sucursal2.jpg" alt="Sucursal Casco Viejo" class="sucursal-image" onerror="this.src='../assets/img/default.jpg'">
                <div class="sucursal-content">
                    <h3 class="sucursal-nombre">🏛️ Chinos Café Casco Viejo</h3>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Dirección</strong>
                            Avenida Central, Casco Antiguo<br>
                            Frente a Plaza Herrera
                        </div>
                    </div>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Horario</strong>
                            Lun - Dom: 8:00 AM - 11:00 PM<br>
                            Festivos: 9:00 AM - 10:00 PM
                        </div>
                    </div>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Teléfono</strong>
                            +507 262-3000
                        </div>
                    </div>
                    
                    <div class="sucursal-actions">
                        <a href="https://www.google.com/maps/search/Casco+Viejo+Panama" target="_blank" class="btn-sucursal btn-primary">
                            🗺️ Ver en Mapa
                        </a>
                        <a href="tel:+5072623000" class="btn-sucursal btn-secondary">
                            📞 Llamar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sucursal 3 -->
            <div class="sucursal-card">
                <img src="../assets/img/sucursal3.jpg" alt="Sucursal Costa del Este" class="sucursal-image" onerror="this.src='../assets/img/default.jpg'">
                <div class="sucursal-content">
                    <h3 class="sucursal-nombre">🌊 Chinos Café Costa del Este</h3>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Dirección</strong>
                            Town Center Costa del Este<br>
                            Local 125, Primer Nivel
                        </div>
                    </div>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Horario</strong>
                            Lun - Jue: 7:00 AM - 9:00 PM<br>
                            Vie - Dom: 7:00 AM - 11:00 PM
                        </div>
                    </div>
                    
                    <div class="sucursal-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div class="info-text">
                            <strong>Teléfono</strong>
                            +507 271-8000
                        </div>
                    </div>
                    
                    <div class="sucursal-actions">
                        <a href="https://www.google.com/maps/search/Costa+del+Este+Panama" target="_blank" class="btn-sucursal btn-primary">
                            🗺️ Ver en Mapa
                        </a>
                        <a href="tel:+5072718000" class="btn-sucursal btn-secondary">
                            📞 Llamar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAPA GENERAL -->
        <div class="map-section">
            <div class="map-header">
                <h2 class="map-title">📍 Encuéntranos en el Mapa</h2>
                <p class="map-description">Todas nuestras ubicaciones en Panamá</p>
            </div>
            
            <div class="map-container">
                <!-- Google Maps Embed - Ajusta estas coordenadas a tus sucursales reales -->
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126161.29351352268!2d-79.53582834374999!3d9.023889999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8faca8f1dbe80363%3A0xaba25df1f042c10e!2zUGFuYW3DoSwgUGFuYW3DoQ!5e0!3m2!1ses!2s!4v1234567890123!5m2!1ses!2s"
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

        <!-- SECCIÓN DE CONTACTO RÁPIDO -->
        <div class="map-section" style="margin-top: 40px;">
            <div class="map-header">
                <h2 class="map-title">💬 ¿Necesitas Ayuda?</h2>
                <p class="map-description">Estamos aquí para atenderte</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 30px;">
                <div style="background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro)); color: #fff; padding: 30px; border-radius: 15px; text-align: center;">
                    <svg style="width: 50px; height: 50px; margin-bottom: 15px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <h3 style="margin: 0 0 10px 0; font-size: 1.3rem;">Llámanos</h3>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: 700;">+507 6000-0000</p>
                    <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 0.9rem;">Lun - Dom: 7:00 AM - 10:00 PM</p>
                </div>
                
                <div style="background: linear-gradient(135deg, #28a745, #20c997); color: #fff; padding: 30px; border-radius: 15px; text-align: center;">
                    <svg style="width: 50px; height: 50px; margin-bottom: 15px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <h3 style="margin: 0 0 10px 0; font-size: 1.3rem;">WhatsApp</h3>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: 700;">+507 6000-0000</p>
                    <a href="https://wa.me/5076000000" target="_blank" style="display: inline-block; margin-top: 15px; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 25px; color: #fff; text-decoration: none; font-weight: 600;">
                        Chatear Ahora
                    </a>
                </div>
                
                <div style="background: linear-gradient(135deg, #007bff, #0056b3); color: #fff; padding: 30px; border-radius: 15px; text-align: center;">
                    <svg style="width: 50px; height: 50px; margin-bottom: 15px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <h3 style="margin: 0 0 10px 0; font-size: 1.3rem;">Email</h3>
                    <p style="margin: 0; font-size: 1.2rem; font-weight: 600; word-break: break-all;">info@chinoscafe.com</p>
                    <a href="mailto:info@chinoscafe.com" style="display: inline-block; margin-top: 15px; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 25px; color: #fff; text-decoration: none; font-weight: 600;">
                        Enviar Email
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>
</body>
</html>