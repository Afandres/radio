<style>
    body {
        background-image: url('https://images.unsplash.com/photo-1636294155433-29863ca33aa6?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        margin: 0;
    }

    .auth-card {
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
        color: #fff;
        max-width: 400px;
        width: 100%;
        margin: auto;
        margin-top: 150px;
    }

    .auth-card label,
    .auth-card input {
        color: #f0f0f0;
    }

    .auth-card input {
        background-color: #1e1e1e;
        border: 1px solid #444;
    }

    .auth-card input:focus {
        border-color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    }

    .auth-btn {
        background-color: #ffffff;
        border: none;
        color: rgb(0, 0, 0);
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: bold;
        transition: background 0.3s;
    }

    .auth-btn:hover {
        background-color: #d80e62;
    }

    .auth-link {
        color: #ccc;
    }

    .auth-link:hover {
        color: #fff;
        text-decoration: underline;
    }
</style>

<x-guest-layout>
    <div class="auth-card">
        <h1>!Ingresar con el usuario de Administrador¡</h1>
    </div>
</x-guest-layout>
