<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeeCare Pharmacy</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Nunito', sans-serif;
        background: url('{{ asset('images/doctor-bg.jpg') }}') no-repeat center center fixed;
        background-size: cover;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Moving Gradient Animation Overlay */
    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 30% 30%, rgba(0,123,255,0.15), transparent),
                    radial-gradient(circle at 70% 70%, rgba(40,167,69,0.15), transparent);
        animation: moveBackground 10s linear infinite;
        z-index: 0;
    }

    @keyframes moveBackground {
        0% {
            transform: translate(0, 0);
        }
        50% {
            transform: translate(-25%, -25%);
        }
        100% {
            transform: translate(0, 0);
        }
    }

    /* Floating particles */
    .particles {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 1;
        pointer-events: none;
    }

    .particle {
        position: absolute;
        width: 10px;
        height: 10px;
        background: rgba(255,255,255,0.8);
        border-radius: 50%;
        animation: float 12s infinite ease-in-out;
        opacity: 0.6;
    }

    @keyframes float {
        0% {
            transform: translateY(0) scale(1);
            opacity: 0.6;
        }
        50% {
            transform: translateY(-100vh) scale(1.3);
            opacity: 0.2;
        }
        100% {
            transform: translateY(0) scale(1);
            opacity: 0.6;
        }
    }

    /* Overlay content */
    .overlay {
        position: relative;
        background: rgba(255, 255, 255, 0.95);
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        animation: fadeInDown 1s ease-out;
        z-index: 2;
    }

    h1 {
        font-weight: 700;
        color: #0d6efd;
        margin-bottom: 20px;
    }

    p.subtext {
        font-size: 1.2rem;
        color: #6c757d;
        margin-bottom: 30px;
    }

    .btn-custom {
        font-size: 1.1rem;
        padding: 12px 25px;
        margin: 10px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .footer-text {
        margin-top: 25px;
        font-size: 0.9rem;
        color: #999;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animated-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 20px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
</style>
</head>
<body>
    <!-- Particle Animation Layer -->
    <div class="particles">
        @for($i = 0; $i < 15; $i++)
            <div class="particle" style="
                top: {{ rand(0, 100) }}%;
                left: {{ rand(0, 100) }}%;
                width: {{ rand(6, 12) }}px;
                height: {{ rand(6, 12) }}px;
                animation-duration: {{ rand(8, 15) }}s;
                animation-delay: -{{ rand(0, 15) }}s;
            "></div>
        @endfor
    </div>

    <!-- Main Content -->
    <div class="overlay">
        <img src="https://cdn-icons-png.flaticon.com/512/3771/3771392.png" alt="Pharmacy Icon" class="animated-icon">
        <h1>Welcome to VeeCare Pharmacy</h1>
        <p class="subtext">Your trusted Pharmacy Management System</p>

        <a href="{{ route('login') }}" class="btn btn-primary btn-custom">Login as Admin</a>
        <a href="{{ route('register') }}" class="btn btn-success btn-custom">Register as Sales Person</a>

        <div class="footer-text">
    Powered by VeeCare Hospital &copy; {{ date('Y') }}
</div>

    </div>
</body>
</html>
