@extends('layouts.public')

@section('title', 'Contacto - Betogether')
@section('title-suffix', ' | Contacto')
@section('body-id', 'contacto-body')

@section('content')
<section class="contacto-hero">
    <div class="hero-content3">
        <h1>{{ $pagina->contenido['hero_title'] ?? '¿Listo para llevar tu negocio al siguiente Nivel?' }}</h1>
        <p>
            {!! $pagina->contenido['hero_description'] ?? 'Forma parte de nuestra comunidad. <span class="highlight3">Regístrate en la lista de espera hoy y prepárate para crecer</span> con nosotros.' !!}
        </p>
    </div>

    <div class="form-container">
        @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('formulario.contacto') }}" method="POST">
            @csrf
            <label>Tu nombre *</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required>

            <label>¿Eres un ...? *</label>
            <div class="radio-group">
                <label><input type="radio" name="tipo" value="emprendimiento" {{ old('tipo') == 'emprendimiento' ? 'checked' : '' }} required> Emprendimiento</label>
                <label><input type="radio" name="tipo" value="fundacion" {{ old('tipo') == 'fundacion' ? 'checked' : '' }} required> Fundación</label>
            </div>

            <label>¿Ya vendes en línea? *</label>
            <div class="radio-group">
                <label><input type="radio" name="online" value="si" {{ old('online') == 'si' ? 'checked' : '' }} required> Sí</label>
                <label><input type="radio" name="online" value="no" {{ old('online') == 'no' ? 'checked' : '' }} required> No</label>
            </div>

            <label>¿Has invertido en festivales anteriormente? *</label>
            <div class="radio-group">
                <label><input type="radio" name="festival" value="si" {{ old('festival') == 'si' ? 'checked' : '' }} required> Sí</label>
                <label><input type="radio" name="festival" value="no" {{ old('festival') == 'no' ? 'checked' : '' }} required> No</label>
            </div>

            <label>¿Cómo podemos encontrar tu negocio en redes sociales?</label>
            <input type="text" name="redes_sociales" value="{{ old('redes_sociales') }}">

            <label>Elige la red social</label>
            <select name="red_social">
                <option value="">Selecciona una opción</option>
                <option value="facebook" {{ old('red_social') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                <option value="instagram" {{ old('red_social') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                <option value="tiktok" {{ old('red_social') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
            </select>

            <label>¿Te gustaría participar en eventos? *</label>
            <select name="participar_eventos" required>
                <option value="">Selecciona una opción</option>
                <option value="no_interesado" {{ old('participar_eventos') == 'no_interesado' ? 'selected' : '' }}>No estoy interesado</option>
                <option value="si_claro" {{ old('participar_eventos') == 'si_claro' ? 'selected' : '' }}>Sí, claro</option>
                <option value="depende_evento" {{ old('participar_eventos') == 'depende_evento' ? 'selected' : '' }}>Depende del evento</option>
            </select>

            <label>Un email para ponernos en contacto *</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>Tu número WhatsApp</label>
            <input type="text" name="whatsapp" value="{{ old('whatsapp') }}">

            <label>Algo que quieras decirnos</label>
            <textarea name="mensaje_adicional" rows="4">{{ old('mensaje_adicional') }}</textarea>

            <button class="button2" type="submit">Enviar</button>
        </form>
    </div>

    @if($config->facebook_url || $config->tiktok_url || $config->instagram_url || $config->linkedin_url)
    <section class="socials">
        <p>Síguenos en nuestras redes sociales</p>
        <div class="icon-container">
            @if($config->facebook_url)
                <a href="{{ $config->facebook_url }}" class="social-icon" target="_blank"><i class="bi bi-facebook"></i></a>
            @endif
            @if($config->tiktok_url)
                <a href="{{ $config->tiktok_url }}" class="social-icon" target="_blank"><i class="bi bi-tiktok"></i></a>
            @endif
            @if($config->instagram_url)
                <a href="{{ $config->instagram_url }}" class="social-icon" target="_blank"><i class="bi bi-instagram"></i></a>
            @endif
            @if($config->linkedin_url)
                <a href="{{ $config->linkedin_url }}" class="social-icon" target="_blank"><i class="bi bi-linkedin"></i></a>
            @endif
        </div>
    </section>
    @endif
</section>
@endsection

@push('styles')
<style>
/* Contacto Hero */
.contacto-hero {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d1f4e 50%, #1c1c2e 100%);
    padding: 80px 20px 60px;
    position: relative;
    overflow: hidden;
    min-height: calc(100vh - 68px);
}

.hero-content3 {
    text-align: center;
    padding: 40px 20px;
    max-width: 800px;
    margin: 0 auto;
}

.hero-content3 h1 {
    color: white;
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.3;
}

.hero-content3 p {
    color: rgba(255, 255, 255, 0.85);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

.highlight3 {
    color: #ff00c8;
    font-weight: 600;
}

/* Form Container */
.form-container {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.form-container label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}

.form-container input[type="text"],
.form-container input[type="email"],
.form-container select,
.form-container textarea {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f9f9f9;
}

.form-container input:focus,
.form-container select:focus,
.form-container textarea:focus {
    outline: none;
    border-color: #ff00c8;
    background: white;
    box-shadow: 0 0 0 4px rgba(255, 0, 200, 0.1);
}

.radio-group {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.radio-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 400;
    cursor: pointer;
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.radio-group label:hover {
    border-color: #ff00c8;
    background: rgba(255, 0, 200, 0.05);
}

.radio-group input[type="radio"] {
    accent-color: #ff00c8;
    width: 18px;
    height: 18px;
}

.radio-group input[type="radio"]:checked + span,
.radio-group label:has(input:checked) {
    color: #ff00c8;
}

.button2 {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.button2:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 0, 200, 0.4);
}

/* Alerts */
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border: 1px solid #c3e6cb;
    border-radius: 10px;
    margin-bottom: 20px;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    padding: 15px;
    border: 1px solid #f5c6cb;
    border-radius: 10px;
    margin-bottom: 20px;
}

.alert-error ul {
    margin: 0;
    padding-left: 20px;
}

/* Socials */
.socials {
    text-align: center;
    padding: 40px 20px;
}

.socials p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    margin-bottom: 20px;
}

.icon-container {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.social-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: white;
    font-size: 1.3rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-icon:hover {
    background: linear-gradient(135deg, #ff00c8 0%, #7000ff 100%);
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(255, 0, 200, 0.3);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-content3 h1 {
        font-size: 1.6rem;
    }

    .form-container {
        padding: 25px 20px;
        margin: 0 10px;
    }

    .radio-group {
        flex-direction: column;
        gap: 10px;
    }

    .radio-group label {
        width: 100%;
    }
}
</style>
@endpush
