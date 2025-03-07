@extends('layouts.main')

@section('content')
<style>
    /* Fondo fijo */
    .background-container {
        position: fixed;
        width: 100%;
        height: 100vh;
        background-image: url("{{ asset('images/bicicletas_Milan.png') }}");
        background-size: cover;
        background-position: center;
        filter: brightness(0.4); /* Oscurece un poco la imagen */
        z-index: -2; /* Para que no interfiera */
    }

    /* Imágenes en secuencia */
    .slideshow-container {
        position: fixed;
        width: 100%;
        height: 100vh;
        top: 0;
        left: 0;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        transition: opacity 1s ease-in-out;
        opacity: 0.5; /* Nivel de transparencia */
        z-index: -1; /* Para que no tape el contenido */
    }

    /* Contenedor del formulario */
    .login-container {
        position: relative;
        z-index: 1; /* Asegura que el formulario esté por encima */
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
</style>

<!-- Fondo fijo -->
<div class="background-container"></div>

<!-- Imágenes en secuencia -->
<div class="slideshow-container" id="slideshow"></div>

<!-- Contenedor del formulario -->
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="login-container" style="width: 350px;">
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo_milan.png') }}" alt="Logo" style="max-width: 200px;">
        </div>
        <h4 class="text-center mb-3">Iniciar Sesión</h4>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
        </div>
        <div class="text-center mt-2">
            <a href="{{ route('register') }}" class="text-decoration-none">¿No tienes una cuenta? Regístrate aquí</a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        let passwordField = document.getElementById('password');
        let icon = event.currentTarget.querySelector('i');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Cambiar imágenes cada 2 segundos
    let images = [
        "{{ asset('images/imagen1.png') }}",
        "{{ asset('images/imagen2.png') }}"
    ];
    let index = 0;

    function changeImage() {
        let slideshow = document.getElementById("slideshow");
        slideshow.style.backgroundImage = `url(${images[index]})`;
        index = (index + 1) % images.length;
    }

    setInterval(changeImage, 2000); // Cambia cada 2 segundos
</script>
@endsection
