<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - BeTogether</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ico.png') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Three.js para el fondo 3D -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1a1a2e;
            overflow-x: hidden;
        }

        #bg-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .content-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        .register-card {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 520px;
            margin: 2rem 0;
        }
        
        .form-input {
            border: 1px solid #D1D5DB;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-input:focus {
            outline: none;
            border-color: #FF00C1;
            box-shadow: 0 0 0 3px rgba(255, 0, 193, 0.2);
        }

        .cta-button {
            background-color: #FF00C1;
            color: white;
            font-weight: 700;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: background-color 0.3s, transform 0.2s;
            border: none;
            cursor: pointer;
        }
        .cta-button:hover {
            background-color: #E600AE;
            transform: translateY(-2px);
        }
        
        .login-link {
            color: black;
            text-decoration: underline;
            font-size: 0.875rem;
        }
        .login-link:hover {
            opacity: 0.8;
        }

        .brand-logo-img {
            max-width: 350px;
            width: 100%;
            height: auto;
            filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.25));
        }

        .plan-info {
            background-color: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Canvas para el fondo 3D -->
    <canvas id="bg-canvas"></canvas>

    <!-- Contenedor principal -->
    <div class="content-container">
        
        <img src="{{ asset('images/logo1.png') }}" alt="Logo BeTogether" class="brand-logo-img mb-8">

        <div class="register-card">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-700">{{ __('Nombre completo') }}</label>
                    <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    @if ($errors->has('name'))
                        <div class="mt-2 text-sm text-red-600">
                            @foreach ($errors->get('name') as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-700">{{ __('Correo electrónico') }}</label>
                    <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                    @if ($errors->has('email'))
                        <div class="mt-2 text-sm text-red-600">
                            @foreach ($errors->get('email') as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Teléfono (opcional) -->
                <div class="mb-4">
                    <label for="telefono" class="block mb-2 text-sm font-medium text-gray-700">{{ __('Teléfono (opcional)') }}</label>
                    <input id="telefono" class="form-input" type="text" name="telefono" value="{{ old('telefono') }}" autocomplete="tel">
                    @if ($errors->has('telefono'))
                        <div class="mt-2 text-sm text-red-600">
                            @foreach ($errors->get('telefono') as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Nombre de la Empresa -->
                <div class="mb-4">
                    <label for="nombre_empresa" class="block mb-2 text-sm font-medium text-gray-700">{{ __('Nombre de tu empresa') }}</label>
                    <input id="nombre_empresa" class="form-input" type="text" name="nombre_empresa" value="{{ old('nombre_empresa') }}" required>
                    @if ($errors->has('nombre_empresa'))
                        <div class="mt-2 text-sm text-red-600">
                            @foreach ($errors->get('nombre_empresa') as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    <p class="text-sm text-gray-500 mt-1">Este será el nombre de tu tienda en línea</p>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-700">{{ __('Contraseña') }}</label>
                    <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password">
                    @if ($errors->has('password'))
                        <div class="mt-2 text-sm text-red-600">
                            @foreach ($errors->get('password') as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-700">{{ __('Confirmar Contraseña') }}</label>
                    <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password">
                    @if ($errors->has('password_confirmation'))
                        <div class="mt-2 text-sm text-red-600">
                            @foreach ($errors->get('password_confirmation') as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Plan Info -->
                <div class="plan-info">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">🎁 Plan Fundador - GRATIS</h3>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <li>✓ 10 productos en tu catálogo</li>
                        <li>✓ Subdominio propio (tunombre.betogether.com.co)</li>
                        <li>✓ Pasarela de pagos integrada</li>
                        <li>✓ Comisión: 6.09% + $900 por transacción</li>
                    </ul>
                    <p class="text-xs text-blue-600 mt-2">Puedes actualizar tu plan en cualquier momento</p>
                </div>

                <!-- Terms and Conditions -->
                <div class="mt-4 mb-6">
                    <label for="terms" class="inline-flex items-center">
                        <input id="terms" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="terms" required>
                        <span class="ml-2 text-sm text-gray-600">
                            Acepto los <a href="#" class="underline text-indigo-600 hover:text-indigo-500">términos y condiciones</a> y la <a href="#" class="underline text-indigo-600 hover:text-indigo-500">política de privacidad</a>
                        </span>
                    </label>
                    @if ($errors->has('terms'))
                        <div class="mt-2 text-sm text-red-600">
                            @foreach ($errors->get('terms') as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <a class="login-link" href="{{ route('login') }}">
                        {{ __('¿Ya estás registrado?') }}
                    </a>

                    <button type="submit" class="cta-button">
                        {{ __('Registrarse') }}
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        // Configuración básica Three.js
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({
            canvas: document.querySelector('#bg-canvas'),
            alpha: true
        });

        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight);
        camera.position.setZ(30);

        // Creación de las partículas de fondo
        const particleCount = 5000;
        const positions = new Float32Array(particleCount * 3);
        const colors = new Float32Array(particleCount * 3);
        
        const colorMagenta = new THREE.Color(0xff00c1);
        const colorBlue = new THREE.Color(0x0b00f9);

        for (let i = 0; i < particleCount; i++) {
            const x = (Math.random() - 0.5) * 100;
            const y = (Math.random() - 0.5) * 100;
            const z = (Math.random() - 0.5) * 100;
            positions[i * 3] = x;
            positions[i * 3 + 1] = y;
            positions[i * 3 + 2] = z;

            const mixedColor = colorMagenta.clone().lerp(colorBlue, Math.random());
            colors[i * 3] = mixedColor.r;
            colors[i * 3 + 1] = mixedColor.g;
            colors[i * 3 + 2] = mixedColor.b;
        }

        const particlesGeometry = new THREE.BufferGeometry();
        particlesGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        particlesGeometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

        const particlesMaterial = new THREE.PointsMaterial({
            size: 0.1,
            vertexColors: true,
            blending: THREE.AdditiveBlending
        });

        const particleSystem = new THREE.Points(particlesGeometry, particlesMaterial);
        scene.add(particleSystem);

        // Creación de Emojis Flotantes
        const emojiGroup = new THREE.Group();
        const emojis = ['🚀', '💲', '🎪', '✈️', '📦', '🛵'];
        
        function createEmojiTexture(emoji) {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = 128;
            canvas.height = 128;
            context.font = '96px Arial';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillText(emoji, 64, 64);
            return new THREE.CanvasTexture(canvas);
        }

        emojis.forEach(emoji => {
            const texture = createEmojiTexture(emoji);
            for (let i = 0; i < 4; i++) { 
                const material = new THREE.SpriteMaterial({ map: texture, transparent: true });
                const sprite = new THREE.Sprite(material);
                
                sprite.position.x = (Math.random() - 0.5) * 60;
                sprite.position.y = (Math.random() - 0.5) * 60;
                sprite.position.z = (Math.random() - 0.5) * 60;
                
                const scale = Math.random() * 2 + 1;
                sprite.scale.set(scale, scale, scale);
                
                emojiGroup.add(sprite);
            }
        });
        scene.add(emojiGroup);

        // Interacción con el ratón
        const mouse = new THREE.Vector2();
        window.addEventListener('mousemove', (event) => {
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
        });

        // Bucle de animación
        function animate() {
            requestAnimationFrame(animate);

            particleSystem.rotation.y += 0.0005;
            emojiGroup.rotation.y += 0.0008;
            emojiGroup.rotation.x += 0.0002;
            
            camera.position.x += (mouse.x * 5 - camera.position.x) * 0.05;
            camera.position.y += (mouse.y * 5 - camera.position.y) * 0.05;
            camera.lookAt(scene.position);

            renderer.render(scene, camera);
        }
        
        // Manejo del redimensionamiento
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // Iniciar animación
        window.onload = function() {
            animate();
        }
    </script>
</body>
</html>