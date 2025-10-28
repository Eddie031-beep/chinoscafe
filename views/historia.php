<?php
session_start();
require_once("../config/db.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestra Historia | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .historia-page {
            background: var(--crema);
        }
        
        /* HERO DE HISTORIA */
        .historia-hero {
            height: 60vh;
            background: linear-gradient(rgba(43, 30, 23, 0.8), rgba(59, 47, 47, 0.8)), 
                        url('../assets/img/hero-cafe.jpg') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            padding: 0 20px;
        }
        
        .historia-hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            margin: 0 0 20px 0;
            background: linear-gradient(135deg, #fff 0%, #d2a679 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .historia-hero p {
            font-size: clamp(1.1rem, 2vw, 1.5rem);
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.95;
        }
        
        /* LÍNEA DE TIEMPO */
        .timeline-section {
            padding: 100px 6%;
            background: #fff;
        }
        
        .section-intro {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 80px;
        }
        
        .section-badge {
            display: inline-block;
            background: rgba(210, 166, 121, 0.1);
            color: var(--cafe-medio);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            color: var(--cafe-oscuro);
            margin: 0 0 20px 0;
        }
        
        .timeline {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--cafe-medio), var(--cafe-claro));
            z-index: 1;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 80px;
            display: flex;
            align-items: center;
        }
        
        .timeline-item:nth-child(odd) {
            flex-direction: row;
        }
        
        .timeline-item:nth-child(even) {
            flex-direction: row-reverse;
        }
        
        .timeline-content {
            flex: 1;
            padding: 30px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin: 0 30px;
            transition: all 0.3s ease;
        }
        
        .timeline-item:hover .timeline-content {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .timeline-year {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 700;
            box-shadow: 0 5px 20px rgba(210, 166, 121, 0.4);
            z-index: 2;
        }
        
        .timeline-content h3 {
            color: var(--cafe-oscuro);
            font-size: 1.8rem;
            margin: 0 0 15px 0;
        }
        
        .timeline-content p {
            color: #666;
            line-height: 1.8;
            margin: 0;
        }
        
        /* VALORES */
        .valores-section {
            padding: 100px 6%;
            background: linear-gradient(135deg, var(--cafe-oscuro) 0%, var(--cafe-medio) 100%);
            color: #fff;
        }
        
        .valores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 60px auto 0;
        }
        
        .valor-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px 30px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .valor-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-10px);
        }
        
        .valor-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        
        .valor-card h3 {
            font-size: 1.5rem;
            margin: 0 0 15px 0;
        }
        
        .valor-card p {
            opacity: 0.9;
            line-height: 1.8;
        }
        
        /* EQUIPO */
        .equipo-section {
            padding: 100px 6%;
            background: var(--crema);
        }
        
        .equipo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 60px auto 0;
        }
        
        .team-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .team-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
        }
        
        .team-info {
            padding: 25px;
            text-align: center;
        }
        
        .team-name {
            font-size: 1.4rem;
            color: var(--cafe-oscuro);
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        
        .team-role {
            color: var(--cafe-medio);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .team-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .timeline::before {
                left: 30px;
            }
            
            .timeline-item,
            .timeline-item:nth-child(even) {
                flex-direction: row !important;
            }
            
            .timeline-year {
                left: 30px;
                transform: translateX(0);
            }
            
            .timeline-content {
                margin-left: 100px;
                margin-right: 0;
            }
        }
    </style>
</head>
<body class="historia-page">
    <?php include("../includes/header.php"); ?>

    <!-- HERO -->
    <section class="historia-hero">
        <div>
            <h1>Nuestra Historia</h1>
            <p>Más de 29 años creando momentos especiales, una taza a la vez</p>
        </div>
    </section>

    <!-- LÍNEA DE TIEMPO -->
    <section class="timeline-section">
        <div class="section-intro">
            <span class="section-badge">📚 Nuestra Trayectoria</span>
            <h2 class="section-title">Un Viaje de Pasión y Dedicación</h2>
            <p style="color: #666; font-size: 1.1rem; line-height: 1.8;">
                Desde nuestros humildes inicios hasta convertirnos en el café favorito de Panamá, 
                cada momento ha sido guiado por nuestra pasión por la excelencia.
            </p>
        </div>
        
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-year">1995</div>
                <div class="timeline-content">
                    <h3>🌱 El Comienzo</h3>
                    <p>Chinos Café abre sus puertas en un pequeño local de Obarrio, con el sueño de ofrecer el mejor café artesanal de Panamá. Con solo 3 empleados y mucha pasión, comenzamos nuestra historia.</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2000</div>
                <div class="timeline-content">
                    <h3>🏆 Primer Reconocimiento</h3>
                    <p>Ganamos el premio al "Mejor Café de Especialidad" otorgado por la Cámara de Comercio de Panamá, consolidando nuestra reputación de excelencia y calidad.</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2005</div>
                <div class="timeline-content">
                    <h3>🌍 Expansión Internacional</h3>
                    <p>Establecemos alianzas con productores de café de Colombia y Costa Rica, ampliando nuestra variedad de granos y fortaleciendo nuestro compromiso con la calidad.</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2010</div>
                <div class="timeline-content">
                    <h3>🏢 Segunda Sucursal</h3>
                    <p>Abrimos nuestra segunda ubicación en el histórico Casco Viejo, fusionando tradición con innovación en un espacio que rápidamente se convierte en punto de encuentro.</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2015</div>
                <div class="timeline-content">
                    <h3>📱 Era Digital</h3>
                    <p>Lanzamos nuestra plataforma de pedidos en línea y app móvil, llevando la experiencia Chinos Café a la comodidad de tu hogar. La tecnología se une a la tradición.</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2020</div>
                <div class="timeline-content">
                    <h3>💪 Resiliencia</h3>
                    <p>Durante tiempos desafiantes, nos adaptamos y fortalecemos nuestro servicio a domicilio, manteniendo la conexión con nuestros clientes y apoyando a nuestro equipo.</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2024</div>
                <div class="timeline-content">
                    <h3>🎉 Hoy</h3>
                    <p>Con 3 sucursales exitosas, un equipo de más de 50 personas apasionadas y miles de clientes satisfechos, continuamos innovando mientras honramos nuestras raíces. El futuro es prometedor.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- VALORES -->
    <section class="valores-section">
        <div class="section-intro">
            <span class="section-badge" style="background: rgba(255,255,255,0.2); color: #fff;">💎 Nuestros Valores</span>
            <h2 class="section-title" style="color: #fff;">Lo Que Nos Define</h2>
            <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem; line-height: 1.8;">
                Cada decisión que tomamos está guiada por estos principios fundamentales
            </p>
        </div>
        
        <div class="valores-grid">
            <div class="valor-card">
                <div class="valor-icon">☕</div>
                <h3>Calidad Suprema</h3>
                <p>Nunca comprometemos la calidad. Cada grano, cada preparación, cada servicio refleja nuestro compromiso con la excelencia.</p>
            </div>
            
            <div class="valor-card">
                <div class="valor-icon">❤️</div>
                <h3>Pasión por el Café</h3>
                <p>Más que un negocio, el café es nuestra pasión. Cada taza es preparada con amor y dedicación por nuestro equipo de expertos.</p>
            </div>
            
            <div class="valor-card">
                <div class="valor-icon">🤝</div>
                <h3>Comunidad</h3>
                <p>Somos parte de la familia panameña. Apoyamos a productores locales y creamos espacios donde las personas se conectan.</p>
            </div>
            
            <div class="valor-card">
                <div class="valor-icon">🌱</div>
                <h3>Sostenibilidad</h3>
                <p>Cuidamos el planeta trabajando con prácticas sostenibles y apoyando la agricultura responsable en toda nuestra cadena.</p>
            </div>
            
            <div class="valor-card">
                <div class="valor-icon">🚀</div>
                <h3>Innovación</h3>
                <p>Honramos la tradición mientras abrazamos la innovación, siempre buscando nuevas formas de mejorar tu experiencia.</p>
            </div>
            
            <div class="valor-card">
                <div class="valor-icon">✨</div>
                <h3>Experiencia Única</h3>
                <p>Cada visita a Chinos Café es especial. Desde el ambiente hasta el servicio, creamos momentos memorables.</p>
            </div>
        </div>
    </section>

    <!-- EQUIPO -->
    <section class="equipo-section">
        <div class="section-intro">
            <span class="section-badge">👥 Nuestro Equipo</span>
            <h2 class="section-title">Las Personas Detrás del Café</h2>
            <p style="color: #666; font-size: 1.1rem; line-height: 1.8;">
                Conoce a las personas apasionadas que hacen posible la magia de Chinos Café cada día
            </p>
        </div>
        
        <div class="equipo-grid">
            <div class="team-card">
                <div class="team-image">👨‍💼</div>
                <div class="team-info">
                    <h3 class="team-name">Carlos Chen</h3>
                    <p class="team-role">Fundador & CEO</p>
                    <p class="team-description">Visionario detrás de Chinos Café, con más de 30 años de experiencia en la industria del café.</p>
                </div>
            </div>
            
            <div class="team-card">
                <div class="team-image">👩‍🍳</div>
                <div class="team-info">
                    <h3 class="team-name">María Rodríguez</h3>
                    <p class="team-role">Barista Principal</p>
                    <p class="team-description">Experta en arte latte con certificación internacional. Lidera la capacitación de nuestro equipo.</p>
                </div>
            </div>
            
            <div class="team-card">
                <div class="team-image">👨‍🔬</div>
                <div class="team-info">
                    <h3 class="team-name">Roberto Sánchez</h3>
                    <p class="team-role">Maestro Tostador</p>
                    <p class="team-description">Responsable del tostado artesanal que da vida a cada perfil único de sabor de nuestros cafés.</p>
                </div>
            </div>
            
            <div class="team-card">
                <div class="team-image">👩‍💼</div>
                <div class="team-info">
                    <h3 class="team-name">Ana Martínez</h3>
                    <p class="team-role">Directora de Operaciones</p>
                    <p class="team-description">Garantiza que cada sucursal mantenga los más altos estándares de calidad y servicio.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ESTADÍSTICAS -->
    <section style="padding: 80px 6%; background: #fff;">
        <div class="section-intro">
            <span class="section-badge">📊 Nuestro Impacto</span>
            <h2 class="section-title">En Números</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; max-width: 1200px; margin: 60px auto 0;">
            <div style="text-align: center; padding: 30px; background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro)); border-radius: 20px; color: #fff; box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3);">
                <div style="font-size: 3.5rem; font-weight: 700; margin-bottom: 10px;">29+</div>
                <div style="font-size: 1.1rem; opacity: 0.95;">Años de Historia</div>
            </div>
            
            <div style="text-align: center; padding: 30px; background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro)); border-radius: 20px; color: #fff; box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3);">
                <div style="font-size: 3.5rem; font-weight: 700; margin-bottom: 10px;">3</div>
                <div style="font-size: 1.1rem; opacity: 0.95;">Sucursales</div>
            </div>
            
            <div style="text-align: center; padding: 30px; background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro)); border-radius: 20px; color: #fff; box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3);">
                <div style="font-size: 3.5rem; font-weight: 700; margin-bottom: 10px;">50+</div>
                <div style="font-size: 1.1rem; opacity: 0.95;">Colaboradores</div>
            </div>
            
            <div style="text-align: center; padding: 30px; background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro)); border-radius: 20px; color: #fff; box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3);">
                <div style="font-size: 3.5rem; font-weight: 700; margin-bottom: 10px;">50K+</div>
                <div style="font-size: 1.1rem; opacity: 0.95;">Clientes Felices</div>
            </div>
            
            <div style="text-align: center; padding: 30px; background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro)); border-radius: 20px; color: #fff; box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3);">
                <div style="font-size: 3.5rem; font-weight: 700; margin-bottom: 10px;">15+</div>
                <div style="font-size: 1.1rem; opacity: 0.95;">Variedades de Café</div>
            </div>
            
            <div style="text-align: center; padding: 30px; background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro)); border-radius: 20px; color: #fff; box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3);">
                <div style="font-size: 3.5rem; font-weight: 700; margin-bottom: 10px;">100%</div>
                <div style="font-size: 1.1rem; opacity: 0.95;">Satisfacción</div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section style="padding: 100px 6%; background: var(--crema); text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-size: clamp(2rem, 5vw, 3.5rem); color: var(--cafe-oscuro); margin: 0 0 20px 0;">
                Sé Parte de Nuestra Historia
            </h2>
            <p style="font-size: 1.2rem; color: #666; margin-bottom: 40px; line-height: 1.8;">
                Cada cliente que entra por nuestras puertas se convierte en parte de nuestra familia. 
                Te invitamos a ser parte de los próximos capítulos de esta increíble historia.
            </p>
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="tienda.php" style="background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro)); color: #fff; padding: 16px 40px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 1.1rem; box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3); transition: all 0.3s ease; display: inline-block;">
                    🛍️ Visita Nuestra Tienda
                </a>
                <a href="sucursales.php" style="background: rgba(210, 166, 121, 0.1); color: var(--cafe-medio); padding: 16px 40px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 1.1rem; border: 2px solid var(--cafe-medio); transition: all 0.3s ease; display: inline-block;">
                    📍 Encuéntranos
                </a>
            </div>
        </div>
    </section>

    <?php include("../includes/footer.php"); ?>

    <script>
        // Animación de entrada para timeline items
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.timeline-item').forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'all 0.6s ease';
            observer.observe(item);
        });

        // Animación para valor cards
        document.querySelectorAll('.valor-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            card.style.transitionDelay = `${index * 0.1}s`;
            observer.observe(card);
        });

        // Animación para team cards
        document.querySelectorAll('.team-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            card.style.transition = 'all 0.6s ease';
            card.style.transitionDelay = `${index * 0.1}s`;
            observer.observe(card);
        });
    </script>
</body>
</html>