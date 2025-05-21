<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Radio</title>

        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <style>
            body, html {
            height: 100%;
            margin: 0;
            color: white;
            }

            .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)),
                url('https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4') no-repeat center center;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex-direction: column;
            }

            .logo {
            width: 150px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(2px 2px 5px #000);
            }

            h1 {
            font-size: 3rem;
            font-weight: bold;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.8);
            }

            p {
            font-size: 1.2rem;
            opacity: 0.9;
            }
        </style>
    </head>
    <body >
        <header class="position-absolute top-0 w-100 py-3 px-4 d-flex justify-content-end align-items-center" style="z-index: 10;">
            @if (Route::has('login'))
                <nav class="d-flex gap-2">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="btn btn-outline-light btn-sm px-4 fw-semibold"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="btn btn-light btn-sm px-4 fw-semibold"
                        >
                            Iniciar sesión
                        </a>
                    @endauth
                </nav>
            @endif
        </header>

        <section class="hero-section">
            <!-- Logo de la radio -->
            <img src="https://cdn-icons-png.flaticon.com/512/727/727245.png" alt="Logo Radio" class="logo">
            
            <!-- Nombre de la radio -->
            <h1>🎧 RADIO ONLINE</h1>
            <p>"Conectando tus sentidos"</p>
        </section>
        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
