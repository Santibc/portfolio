<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Betogether</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body id="welcome-body">
    <!-- 🎯 HEADER -->
    <header>
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="{{ url('/') }}">Inicio</a></li>

                @auth
                    <li><a href="{{ route('dashboard') }}">Mi cuenta</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn outline"
                                style="border:none;background:none;padding:0;cursor:pointer;">
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Iniciar sesión</a></li>
                @endauth
            </ul>
        </nav>

        <div class="hero">
            <div class="hero-text">
                <img width="20%" src="{{ asset('images/logo.png') }}"
                    alt="Logo" class="logo">
                <h1>¡Hola Colombia!<br>Tu Negocio, <span>Nuestra causa.</span></h1>
                <p>Te abrimos la puerta a un ecosistema de <strong>crecimiento sin límites.</strong></p>
                <ul class="benefits">
                    <li>🛒 Tienda Online al Instante</li>
                    <li>🚚 Pasarela de pago y Logística integrada</li>
                    <li>🎉 Festivales Exclusivos para nuestros miembros</li>
                </ul>
                <div class="buttons">
                    <a href="#" class="btn pink">Así lo hacemos posible</a>
                    <a href="{{ route('register') }}" class="btn outline">Regístrate ahora</a>
                </div>
            </div>
        </div>
        <div class="wave-container">
            <svg viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="white" fill-opacity="1"
                    d="M0,256L26.7,213.3C53.3,171,107,85,160,90.7C213.3,96,267,192,320,224C373.3,256,427,224,480,224C533.3,224,587,256,640,261.3C693.3,267,747,245,800,234.7C853.3,224,907,224,960,197.3C1013.3,171,1067,117,1120,122.7C1173.3,128,1227,192,1280,208C1333.3,224,1387,192,1413,176L1440,160L1440,320L1413.3,320C1386.7,320,1333,320,1280,320C1226.7,320,1173,320,1120,320C1066.7,320,1013,320,960,320C906.7,320,853,320,800,320C746.7,320,693,320,640,320C586.7,320,533,320,480,320C426.7,320,373,320,320,320C266.7,320,213,320,160,320C106.7,320,53,320,27,320L0,320Z">
                </path>

                <path fill="white" fill-opacity="0.6"
                    d="M0,160L34.3,181.3C68.6,203,137,245,206,250.7C274.3,256,343,224,411,192C480,160,549,128,617,133.3C685.7,139,754,181,823,213.3C891.4,245,960,267,1029,250.7C1097.1,235,1166,181,1234,160C1302.9,139,1371,149,1406,154.7L1440,160L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                </path>
                <path fill="white" fill-opacity="0.3"
                    d="M0,64L18.5,90.7C36.9,117,74,171,111,213.3C147.7,256,185,288,222,266.7C258.5,245,295,171,332,154.7C369.2,139,406,181,443,186.7C480,192,517,160,554,144C590.8,128,628,128,665,138.7C701.5,149,738,171,775,165.3C812.3,160,849,128,886,144C923.1,160,960,224,997,250.7C1033.8,277,1071,267,1108,240C1144.6,213,1182,171,1218,144C1255.4,117,1292,107,1329,122.7C1366.2,139,1403,181,1422,202.7L1440,224L1440,320L1421.5,320C1403.1,320,1366,320,1329,320C1292.3,320,1255,320,1218,320C1181.5,320,1145,320,1108,320C1070.8,320,1034,320,997,320C960,320,923,320,886,320C849.2,320,812,320,775,320C738.5,320,702,320,665,320C627.7,320,591,320,554,320C516.9,320,480,320,443,320C406.2,320,369,320,332,320C295.4,320,258,320,222,320C184.6,320,148,320,111,320C73.8,320,37,320,18,320L0,320Z">
                </path>
                <path fill="white" fill-opacity="0.3"
                    d="M0,192L34.3,197.3C68.6,203,137,213,206,192C274.3,171,343,117,411,112C480,107,549,149,617,181.3C685.7,213,754,235,823,218.7C891.4,203,960,149,1029,106.7C1097.1,64,1166,32,1234,26.7C1302.9,21,1371,43,1406,53.3L1440,64L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                </path>
            </svg>
        </div>
    </header>

    <!-- 🎯 SVG SECTION -->


    <!-- 🎯 HERO SECTION -->
    <section class="seccion-oferta">
        <p class="subtitulo">¡ACTIVA TU MARCA EN LO DIGITAL Y PRESENCIAL!</p>
        <h2 class="titulo">¿Emprendes o lideras una fundación?</h2>
        <p class="descripcion">
            Con nosotros no solo accedes a una plataforma…<br />
            ¡Abres la puerta a un <strong>ecosistema completo</strong> que se enfoca en atraer visitantes y potenciales
            clientes para ti.
        </p>

        <div class="tarjetas-contenedor">
            <!-- Tarjeta 1 -->
            <div class="tarjeta">
                <div class="icono"><i class="bi bi-laptop"></i></div>
                <h3><a href="#">Tu Tienda Online ¡Lista para Vender!</a></h3>
                <p>Lánzala en minutos y gestiona pagos seguros, envíos y estadísticas para el control total de tus
                    ventas. 🧾</p>
            </div>

            <!-- Tarjeta 2 -->
            <div class="tarjeta">
                <div class="icono"><i class="fa-solid fa-handshake"></i></div>
                <h3><a href="#">Tu Marca Siempre Visible.</a></h3>
                <p>¡No solo vendes, tu marca conecta! ❤️ Creamos contenido audiovisual en nuestras redes que cuenta la
                    historia de algunos de nuestros miembros, impulsando su reconocimiento. <strong>Somos tu aliado para
                        hacerte conocer.</strong></p>
            </div>

            <!-- Tarjeta 3 -->
            <div class="tarjeta">
                <div class="icono"><i class="bi bi-star-fill"></i></div>
                <h3><a href="#">¡Brilla en nuestros eventos exclusivos!</a></h3>
                <p>Lleva tu marca al siguiente nivel en festivales comerciales. Tu tienda digital y física se fusionan
                    para que solo te preocupes por vender, nosotros nos encargamos del resto y, <strong>¡nosotros
                        ponemos la infraestructura!</strong> 🏠</p>
            </div>
        </div>
    </section>





    <section class="hero2">
        <!-- Onda superior -->
        <svg class="wave wave-top" viewBox="0 0 1440 320">
            <path fill="#fff" fill-opacity="0.3"
                d="M0,128L60,154.7C120,181,240,235,360,240C480,245,600,203,720,176C840,149,960,139,1080,149.3C1200,160,1320,192,1380,208L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z">
            </path>
            <path fill="#fff" fill-opacity="0.3"
                d="M0,256L24,234.7C48,213,96,171,144,154.7C192,139,240,149,288,154.7C336,160,384,160,432,170.7C480,181,528,203,576,192C624,181,672,139,720,122.7C768,107,816,117,864,133.3C912,149,960,171,1008,197.3C1056,224,1104,256,1152,240C1200,224,1248,160,1296,154.7C1344,149,1392,203,1416,229.3L1440,256L1440,0L1416,0C1392,0,1344,0,1296,0C1248,0,1200,0,1152,0C1104,0,1056,0,1008,0C960,0,912,0,864,0C816,0,768,0,720,0C672,0,624,0,576,0C528,0,480,0,432,0C384,0,336,0,288,0C240,0,192,0,144,0C96,0,48,0,24,0L0,0Z">
            </path>
            <path fill="#fff" fill-opacity="0.5"
                d="M0,224L26.7,186.7C53.3,149,107,75,160,69.3C213.3,64,267,128,320,128C373.3,128,427,64,480,64C533.3,64,587,128,640,133.3C693.3,139,747,85,800,80C853.3,75,907,117,960,117.3C1013.3,117,1067,75,1120,90.7C1173.3,107,1227,181,1280,208C1333.3,235,1387,213,1413,202.7L1440,192L1440,0L1413.3,0C1386.7,0,1333,0,1280,0C1226.7,0,1173,0,1120,0C1066.7,0,1013,0,960,0C906.7,0,853,0,800,0C746.7,0,693,0,640,0C586.7,0,533,0,480,0C426.7,0,373,0,320,0C266.7,0,213,0,160,0C106.7,0,53,0,27,0L0,0Z">
            </path>
            <path fill="#fff" fill-opacity="1"
                d="M0,96L48,90.7C96,85,192,75,288,85.3C384,96,480,128,576,128C672,128,768,96,864,96C960,96,1056,128,1152,128C1248,128,1344,96,1392,80L1440,64L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
            </path>
        </svg>

        <!-- Contenido -->
        <h2>¡Únete a la <strong>primera membresía</strong> en Colombia para Emprendedores y
            <strong>Fundaciones</strong>!
        </h2>
        <h1>Juntos, ya impactamos a más de:</h1>
        <div class="contador"><span id="contador">0</span>K</div>
        <div class="subcontador">VISITANTES TOTALES</div>
        <script>
            const contador = document.getElementById('contador');
            const valorFinal = 134;
            let actual = 0;
            const velocidad = 15;

            const animar = setInterval(() => {
                actual++;
                contador.textContent = actual;
                if (actual >= valorFinal) clearInterval(animar);
            }, velocidad);
        </script>

        <!-- Onda inferior -->

    </section>
    <svg class="wave-bottom" viewBox="0 0 1440 320">
        <path fill="#fff" fill-opacity="1"
            d="M0,192L48,202.7C96,213,192,235,288,224C384,213,480,171,576,176C672,181,768,235,864,240C960,245,1056,203,1152,186.7C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
        </path>



        <path fill="#fff" fill-opacity="0.5"
            d="M0,32L30,64C60,96,120,160,180,176C240,192,300,160,360,149.3C420,139,480,149,540,165.3C600,181,660,203,720,192C780,181,840,139,900,128C960,117,1020,139,1080,165.3C1140,192,1200,224,1260,234.7C1320,245,1380,235,1410,229.3L1440,224L1440,320L1410,320C1380,320,1320,320,1260,320C1200,320,1140,320,1080,320C1020,320,960,320,900,320C840,320,780,320,720,320C660,320,600,320,540,320C480,320,420,320,360,320C300,320,240,320,180,320C120,320,60,320,30,320L0,320Z">
        </path>

        <path fill="#FFF" fill-opacity="0.5"
            d="M0,32L18.5,26.7C36.9,21,74,11,111,53.3C147.7,96,185,192,222,245.3C258.5,299,295,309,332,298.7C369.2,288,406,256,443,250.7C480,245,517,267,554,256C590.8,245,628,203,665,208C701.5,213,738,267,775,277.3C812.3,288,849,256,886,256C923.1,256,960,288,997,272C1033.8,256,1071,192,1108,181.3C1144.6,171,1182,213,1218,218.7C1255.4,224,1292,192,1329,165.3C1366.2,139,1403,117,1422,106.7L1440,96L1440,320L1421.5,320C1403.1,320,1366,320,1329,320C1292.3,320,1255,320,1218,320C1181.5,320,1145,320,1108,320C1070.8,320,1034,320,997,320C960,320,923,320,886,320C849.2,320,812,320,775,320C738.5,320,702,320,665,320C627.7,320,591,320,554,320C516.9,320,480,320,443,320C406.2,320,369,320,332,320C295.4,320,258,320,222,320C184.6,320,148,320,111,320C73.8,320,37,320,18,320L0,320Z">
        </path>
        <path fill="#fff" fill-opacity="0.3"
            d="M0,64L21.8,101.3C43.6,139,87,213,131,229.3C174.5,245,218,203,262,181.3C305.5,160,349,160,393,181.3C436.4,203,480,245,524,229.3C567.3,213,611,139,655,122.7C698.2,107,742,149,785,192C829.1,235,873,277,916,266.7C960,256,1004,192,1047,138.7C1090.9,85,1135,43,1178,69.3C1221.8,96,1265,192,1309,208C1352.7,224,1396,160,1418,128L1440,96L1440,320L1418.2,320C1396.4,320,1353,320,1309,320C1265.5,320,1222,320,1178,320C1134.5,320,1091,320,1047,320C1003.6,320,960,320,916,320C872.7,320,829,320,785,320C741.8,320,698,320,655,320C610.9,320,567,320,524,320C480,320,436,320,393,320C349.1,320,305,320,262,320C218.2,320,175,320,131,320C87.3,320,44,320,22,320L0,320Z">
        </path>

    </svg>
    <!--<img src="{{ asset('images/imagen1.png') }}" alt="imagen"> -->
    <section class="features-section">
        <div class="features-container">
            <div class="features-image">
                <img src="{{ asset('images/imagen1.png') }}" alt="imagen">
            </div>

            <div class="features-text">
                <p class="subtitulo">UN ECOSISTEMA COMPLETO Y LISTO PARA TI</p>
                <h2>Sencillo, Rápido y Poderoso</h2>
                <p class="intro">
                    Tu tienda,<br />
                    Tus eventos, tu momento<br />
                    Inscríbete en la lista de espera 🚨
                </p>

                <div class="feature-item">
                    <div class="icon pink"><i class="bi bi-person-plus"></i></i></div>
                    <div class="feature-content">
                        <h3>Regístrate</h3>
                        <p>Da el primer paso para impulsar tu marca. Crea tu cuenta <strong>sin costo</strong> y
                            configúrala en minutos.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="icon blue"><i class="bi bi-check-lg"></i></i></div>
                    <div class="feature-content">
                        <h3>Activa</h3>
                        <p>Elige tu <strong>membresía</strong> activála ✅ y accede a múltiples beneficios. <strong>¡Así
                                podrás enfocarte solo en vender!</strong></p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="icon pink"><i class="bi bi-calendar"></i></div>
                    <div class="feature-content">
                        <h3>Agéndate</h3>
                        <p><strong>Alquila tu espacio</strong> en nuestros festivales de comercio. ¡Tú solo vende,
                            nosotros hacemos el resto!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bt-section">
        <p class="bt-subtitle">Nacimos para transformar el emprendimiento y el impacto social en Colombia.</p>
        <h2 class="bt-title">“Emprender no debe ser imposible, debe ser accesible.”</h2>

        <div class="bt-wrapper">
            <div class="bt-left">
                <div class="bt-icon">
                    <i class="bi bi-calendar2-event-fill"></i>
                </div>
                <div class="bt-text">
                    <p>
                        <strong>Better Together:</strong>
                        <em>Tu ecosistema único</em> y accesible <em>en Colombia.</em>
                        Con nuestra <strong>plataforma y eventos exclusivos</strong>, te damos las herramientas para que
                        solo te enfoques en tu negocio y vender.
                        <br>¡Únete y transforma tu estrategia!
                    </p>
                </div>
            </div>
            <div class="bt-right">
                <img src="{{ asset('images/imagen2.png') }}" alt="Personas emprendiendo">
            </div>
        </div>

        <p class="bt-footer-text">Somos una inversión para tu marca</p>
    </section>

    <section class="btg-benefits-section">
        <div class="btg-benefits-content">
            <h4 class="btg-benefits-subtitle">Acceso exclusivo por inscripción anticipada</h4>
            <h2 class="btg-benefits-title">Todo lo que necesitas para crecer</h2>
            <p class="btg-benefits-description">
                El acceso a Better Together es limitado en esta fase inicial.<br>
                <strong>¡Solo quienes se registren en la lista de espera</strong> podrán ser parte de este grupo!<br>
                <strong>No te quedes por fuera y asegura tu lugar ahora.</strong>
            </p>
        </div>
    </section>
    <div class="rectangulo-amarillo">
        <section class="benefits-container">
            <div class="benefit-box left-box hidden" data-animate="slideInLeft">
                <div class="horizontal-line"></div>
                <div class="benefit-item">
                    <i class="bi bi-shop"></i>
                    <p><strong>1. Lanza tu e-commerce en minutos:</strong> gestiona tus pedidos, inventario y potencia
                        tus
                        ingresos</p>
                </div>
                <div class="benefit-item">
                    <i class="bi bi-cash"></i>
                    <p><strong>2. Pagos rápidos y directos:</strong> Pagos ágiles y seguros usando nuestra plataforma en
                        lo
                        digital y en festivales.</p>
                </div>
            </div>

            <div class="benefit-center">
                <img src="{{ asset('images/imagen3.png') }}" alt="persona con bolsa y tablet">
                <button class="cta-button hidden" data-animate="slideInBottom">¡EMPIEZA AHORA!</button>
            </div>

            <div class="benefit-box right-box animate-right hidden" data-animate="slideInRight">
                <div class="horizontal-line"></div>
                <div class="benefit-item">
                    <i class="bi bi-truck"></i>
                    <p><strong>3. Logística Optimizada para ti:</strong> Cotiza y gestiona tus despachos con las
                        transportadoras, ¡todo desde nuestra plataforma!</p>
                </div>
                <div class="benefit-item">
                    <i class="bi bi-card-text"></i>
                    <p><strong>4. Presencia en eventos físicos:</strong> Participa en festivales de comercio con
                        afluencia
                        de público. Conecta con nuevos clientes e impulsa tus ventas.</p>
                </div>
            </div>
            <script>
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const el = entry.target;
                            const anim = el.dataset.animate;
                            el.classList.remove('hidden');
                            el.classList.add(anim);
                            observer.unobserve(el); // para que solo se anime una vez
                        }
                    });
                }, {
                    threshold: 0.5
                });

                document.querySelectorAll('.hidden[data-animate]').forEach(el => {
                    observer.observe(el);
                });
            </script>
        </section>
    </div>

    <section class="hero3">
        <svg class="wave-top2" viewBox="0 0 1440 320">
            <path fill="#fff" fill-opacity="0.3"
                d="M0,128L60,154.7C120,181,240,235,360,240C480,245,600,203,720,176C840,149,960,139,1080,149.3C1200,160,1320,192,1380,208L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z">
            </path>
            <path fill="#fff" fill-opacity="0.3"
                d="M0,256L24,234.7C48,213,96,171,144,154.7C192,139,240,149,288,154.7C336,160,384,160,432,170.7C480,181,528,203,576,192C624,181,672,139,720,122.7C768,107,816,117,864,133.3C912,149,960,171,1008,197.3C1056,224,1104,256,1152,240C1200,224,1248,160,1296,154.7C1344,149,1392,203,1416,229.3L1440,256L1440,0L1416,0C1392,0,1344,0,1296,0C1248,0,1200,0,1152,0C1104,0,1056,0,1008,0C960,0,912,0,864,0C816,0,768,0,720,0C672,0,624,0,576,0C528,0,480,0,432,0C384,0,336,0,288,0C240,0,192,0,144,0C96,0,48,0,24,0L0,0Z">
            </path>
            <path fill="#fff" fill-opacity="0.5"
                d="M0,224L26.7,186.7C53.3,149,107,75,160,69.3C213.3,64,267,128,320,128C373.3,128,427,64,480,64C533.3,64,587,128,640,133.3C693.3,139,747,85,800,80C853.3,75,907,117,960,117.3C1013.3,117,1067,75,1120,90.7C1173.3,107,1227,181,1280,208C1333.3,235,1387,213,1413,202.7L1440,192L1440,0L1413.3,0C1386.7,0,1333,0,1280,0C1226.7,0,1173,0,1120,0C1066.7,0,1013,0,960,0C906.7,0,853,0,800,0C746.7,0,693,0,640,0C586.7,0,533,0,480,0C426.7,0,373,0,320,0C266.7,0,213,0,160,0C106.7,0,53,0,27,0L0,0Z">
            </path>
            <path fill="#fff" fill-opacity="1"
                d="M0,96L48,90.7C96,85,192,75,288,85.3C384,96,480,128,576,128C672,128,768,96,864,96C960,96,1056,128,1152,128C1248,128,1344,96,1392,80L1440,64L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
            </path>
        </svg>
        <div class="hero-content3">
            <h1>¿Listo para llevar tu negocio al siguiente Nivel?</h1>
            <p>
                Forma parte de nuestra comunidad.
                <span class="highlight3">Regístrate en la lista de espera hoy y prepárate para crecer</span> con
                nosotros.
            </p>
        </div>
        <div class="form-container">
            <form>
                <label>Tu nombre *</label>
                <input type="text" required>

                <label>¿Eres un ...? *</label>
                <div class="radio-group">
                    <label><input type="radio" name="tipo" required> Emprendimiento</label>
                    <label><input type="radio" name="tipo" required> Fundación</label>
                </div>

                <label>¿Ya vendes en línea? *</label>
                <div class="radio-group">
                    <label><input type="radio" name="online" required> Sí</label>
                    <label><input type="radio" name="online" required> No</label>
                </div>

                <label>¿Has invertido en festivales anteriormente? *</label>
                <div class="radio-group">
                    <label><input type="radio" name="festival" required> Sí</label>
                    <label><input type="radio" name="festival" required> No</label>
                </div>

                <label>¿Cómo podemos encontrar tu negocio en redes sociales?</label>
                <input type="text">

                <label>Elige la red social</label>
                <select>
                    <option>Facebook</option>
                    <option>Instagram</option>
                    <option>TikTok</option>
                </select>

                <label>¿Te gustaría participar en eventos? *</label>
                <select required>
                    <option>No estoy interesado</option>
                    <option>Sí, claro</option>
                    <option>Depende del evento</option>
                </select>

                <label>Un email para ponernos en contacto *</label>
                <input type="email" required>

                <label>Tu número WhatsApp</label>
                <input type="text">

                <label>Algo que quieras decirnos</label>
                <textarea rows="4"></textarea>

                <button class="button2" type="submit">Enviar</button>
            </form>
        </div>
        <section class="socials">
            <p>Síguenos en nuestras redes sociales</p>
            <div class="icon-container">
                <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-tiktok"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
            </div>
        </section>
    </section>


    <footer class="footer-personalizado">
        <hr class="linea-gris">
        <p class="texto-gris">© BETOGETHER.COM.CO - TODOS LOS DERECHOS RESERVADOS</p>
        <p class="texto-negro">TECNOLOGÍA ÚTIL, CERCANA Y SIN COMPLICACIONES.</p>
    </footer>

    <a href="https://wa.me/#" class="whatsapp-button" target="_blank">
        <i class="bi bi-whatsapp"></i> Contáctanos vía Whatsapp
    </a>
</body>

</html>
